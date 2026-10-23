<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\EntradaDetalle;
use App\Models\Inventario;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));
        $periodo = $request->input('periodo', 'todos');

        $query = Entrada::with([
            'proveedor',
            'user',
            'detalles.inventario',
        ]);

        // Buscar por folio, proveedor, usuario o fecha.
        if ($buscar !== '') {
            $fecha = null;

            // Acepta fechas d/m/Y y Y-m-d.
            try {
                $fechaCandidata = \Carbon\Carbon::createFromFormat('d/m/Y', $buscar);

                if ($fechaCandidata && $fechaCandidata->format('d/m/Y') === $buscar) {
                    $fecha = $fechaCandidata->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // El término no tiene formato d/m/Y.
            }

            if (!$fecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $buscar)) {
                try {
                    $fechaCandidata = \Carbon\Carbon::createFromFormat('Y-m-d', $buscar);

                    if ($fechaCandidata && $fechaCandidata->format('Y-m-d') === $buscar) {
                        $fecha = $buscar;
                    }
                } catch (\Throwable $e) {
                    // El término no es una fecha válida.
                }
            }

            $query->where(function ($q) use ($buscar, $fecha) {
                $q->where('numero_factura', 'like', "%{$buscar}%")
                    ->orWhereHas('proveedor', function ($proveedor) use ($buscar) {
                        $proveedor->where('proveedor', 'like', "%{$buscar}%");
                    })
                    ->orWhereHas('user', function ($usuario) use ($buscar) {
                        $usuario->where('name', 'like', "%{$buscar}%");
                    });

                if ($fecha) {
                    $q->orWhereDate('fecha_entrada', $fecha)
                        ->orWhereDate('created_at', $fecha);
                }
            });
        }

        // Filtrar por periodo.
        switch ($periodo) {
            case 'hoy':
                $query->whereDate('created_at', today());
                break;

            case 'semana':
                $query->where('created_at', '>=', now()->subDays(6)->startOfDay());
                break;

            case 'mes':
                $query->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ]);
                break;
        }

        $entradas = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('entradas.index', compact('entradas'));
    }

    public function create()
    {
        $entradaReciente = session('entrada_reciente', null);

        return view('entradas.create', compact('entradaReciente'));
    }

    /**
     * Buscar proveedores para el selector de entradas.
     */
    public function buscarProveedores(Request $request)
    {
        $termino = trim($request->input('search', ''));

        $proveedores = Proveedor::query()
            ->select(['id', 'proveedor'])
            ->when($termino !== '', function ($query) use ($termino) {
                $query->where(
                    'proveedor',
                    'like',
                    "%{$termino}%"
                );
            })
            ->orderBy('proveedor')
            ->limit(20)
            ->get();

        return response()->json($proveedores);
    }

    /**
     * Buscar productos para el selector de entradas.
     */
    public function buscarProductos(Request $request)
    {
        $termino = trim($request->input('search', ''));

        $inventarios = Inventario::query()
            ->select([
                'id',
                'nombre_producto',
                'categoria',
                'existencia',
                'medida',
            ])
            ->when($termino !== '', function ($query) use ($termino) {
                $query->where(function ($q) use ($termino) {
                    $q->where('nombre_producto', 'like', "%{$termino}%")
                        ->orWhere('categoria', 'like', "%{$termino}%");
                });
            })
            ->orderBy('nombre_producto')
            ->limit(20)
            ->get();

        return response()->json($inventarios);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero_factura_proveedor' => 'nullable|string|max:100',
            'fecha_entrada' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
            'descuento_global' => 'nullable|numeric|min:0|decimal:0,2',
            'materiales' => 'required|array|min:1',
            'materiales.*.inventario_id' => 'required|exists:inventarios,id',
            'materiales.*.cantidad' => 'required|integer|min:1',
            'materiales.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $productosValidos = [];

        // Preparar los materiales recibidos.
        foreach ($datos['materiales'] as $material) {
            $inventario = Inventario::find($material['inventario_id']);

            if (!$inventario) {
                continue;
            }

            $productosValidos[] = [
                'inventario_id' => $material['inventario_id'],
                'cantidad' => $material['cantidad'],
                'precio_unitario' => $material['precio_unitario'],
            ];
        }

        if (empty($productosValidos)) {
            return back()
                ->withInput()
                ->withErrors([
                    'materiales' => 'Debe agregar al menos un material válido.',
                ]);
        }

        /*
         * Calcular el subtotal desde los datos de los materiales.
         * Se redondea cada línea a dos decimales para coincidir
         * con los importes almacenados en la base de datos.
         */
        $subtotalProductos = array_reduce(
            $productosValidos,
            function ($subtotal, $producto) {
                $subtotalLinea = round(
                    $producto['cantidad'] * (float) $producto['precio_unitario'],
                    2
                );

                return round($subtotal + $subtotalLinea, 2);
            },
            0
        );

        // El campo puede venir vacío; en ese caso no se aplica descuento.
        $descuentoGlobal = round(
            (float) ($datos['descuento_global'] ?? 0),
            2
        );

        /*
         * Un descuento positivo debe ser menor que el subtotal.
         * Un descuento de cero se permite incluso si los materiales
         * tienen importe cero, porque equivale a no aplicar descuento.
         */
        if (
            $descuentoGlobal > 0
            && $descuentoGlobal >= $subtotalProductos
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'descuento_global' =>
                        'El descuento debe ser menor que el subtotal de los productos.',
                ]);
        }

        /*
         * Guardar la entrada y sus detalles en una transacción.
         * Si ocurre un error, también se revierten los cambios de inventario
         * realizados por los eventos de EntradaDetalle.
         */
        $entrada = DB::transaction(function () use (
            $datos,
            $productosValidos,
            $descuentoGlobal
        ) {
            $entrada = Entrada::create([
                'proveedor_id' => $datos['proveedor_id'],
                'numero_factura_proveedor' => $datos['numero_factura_proveedor'] ?? null,
                'fecha_entrada' => $datos['fecha_entrada'],
                'observaciones' => $datos['observaciones'] ?? null,
                'descuento_global' => $descuentoGlobal,
                'user_id' => auth()->id(),
            ]);

            foreach ($productosValidos as $producto) {
                EntradaDetalle::create([
                    'entrada_id' => $entrada->id,
                    'inventario_id' => $producto['inventario_id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                ]);
            }

            return $entrada->fresh([
                'proveedor',
                'user',
                'detalles.inventario',
            ]);
        });

        session()->flash('entrada_reciente', [
            'id' => $entrada->id,
            'numero_factura' => $entrada->numero_factura,
            'fecha' => $entrada->created_at->format('d/m/Y H:i'),
            'proveedor_nombre' => $entrada->proveedor->proveedor,
            'cantidad_productos' => $entrada->cantidad_productos,
            'cantidad_total' => $entrada->cantidad_total,
            'subtotal' => $entrada->precio_total,
            'descuento_global' => $entrada->descuento_global,
            'subtotal_con_descuento' => $entrada->subtotal_con_descuento,
            'iva' => $entrada->iva,
            'total' => $entrada->total_con_iva,
        ]);

        return redirect()
            ->route('entradas.show', $entrada)
            ->with('success', 'Entrada registrada correctamente');
    }

    public function show(Entrada $entrada)
    {
        $entrada->load(['proveedor', 'user', 'detalles.inventario']);

        return view('entradas.show', compact('entrada'));
    }

    public function destroy(Entrada $entrada)
    {
        if ($entrada->created_at->diffInHours(now()) > 24) {
            return redirect()
                ->route('entradas.show', $entrada)
                ->with('error', 'No se puede eliminar una entrada después de 24 horas');
        }

        // Eliminar detalles primero; esto revierte el inventario automáticamente.
        $entrada->detalles()->delete();
        $entrada->delete();

        return redirect()
            ->route('entradas.index')
            ->with('success', 'Entrada eliminada correctamente');
    }

    /**
     * Obtiene la firma del usuario relacionada con la entrada.
     *
     * El campo users.signature almacena la ruta relativa
     * dentro del disco public, por ejemplo: signatures/firma.png.
     */
    private function obtenerFirma(Entrada $entrada): array
    {
        $ruta = $entrada->user?->signature;

        if (!$ruta || !Storage::disk('public')->exists($ruta)) {
            return [
                'firmaBase64' => null,
                'firmaMime' => null,
            ];
        }

        return [
            'firmaBase64' => base64_encode(
                Storage::disk('public')->get($ruta)
            ),
            'firmaMime' => Storage::disk('public')->mimeType($ruta),
        ];
    }

    public function generatePDF(Entrada $entrada)
    {
        $entrada->load(['proveedor', 'user', 'detalles.inventario']);

        $datosFirma = $this->obtenerFirma($entrada);

        $pdf = Pdf::loadView(
            'entradas.pdf',
            array_merge(compact('entrada'), $datosFirma)
        );

        $pdf->setPaper('letter', 'portrait');

        $filename = 'entrada_'
            . $entrada->numero_factura
            . '_'
            . date('Y-m-d')
            . '.pdf';

        return $pdf->download($filename);
    }

    public function viewPDF(Entrada $entrada)
    {
        $entrada->load(['proveedor', 'user', 'detalles.inventario']);

        $datosFirma = $this->obtenerFirma($entrada);

        $pdf = Pdf::loadView(
            'entradas.pdf',
            array_merge(compact('entrada'), $datosFirma)
        );

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream(
            'entrada_' . $entrada->numero_factura . '.pdf'
        );
    }
}