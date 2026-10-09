<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\EntradaDetalle;
use App\Models\Inventario;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Buscar por proveedor, usuario o fecha.
        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('proveedor', function ($proveedor) use ($buscar) {
                    $proveedor->where('proveedor', 'like', "%{$buscar}%");
                })
                ->orWhereHas('user', function ($usuario) use ($buscar) {
                    $usuario->where('name', 'like', "%{$buscar}%");
                });

                // Permite buscar fechas con formato dd/mm/yyyy.
                try {
                    $fecha = \Carbon\Carbon::createFromFormat('d/m/Y', $buscar);

                    if ($fecha && $fecha->format('d/m/Y') === $buscar) {
                        $q->orWhereDate('created_at', $fecha->format('Y-m-d'));
                    }
                } catch (\Throwable $e) {
                    // Si no es una fecha válida, continúa con la búsqueda textual.
                }

                // También permite buscar por año-mes-día.
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $buscar)) {
                    $q->orWhereDate('created_at', $buscar);
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
        // Validación de los datos
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_entrada' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
            'materiales' => 'required|array|min:1',
            'materiales.*.inventario_id' => 'required|exists:inventarios,id',
            'materiales.*.cantidad' => 'required|integer|min:1',
            'materiales.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $productosValidos = [];

        // Validar y preparar datos
        foreach ($request->materiales as $index => $material) {
            $inventario = Inventario::find($material['inventario_id']);
            
            if (!$inventario) {
                continue; // Saltar si el inventario no existe
            }

            $precioUnitario = $material['precio_unitario'];
            
            $productosValidos[] = [
                'inventario_id' => $material['inventario_id'],
                'cantidad' => $material['cantidad'],
                'precio_unitario' => $precioUnitario,
            ];
        }

        if (empty($productosValidos)) {
            return back()->withErrors(['materiales' => 'Debe agregar al menos un material válido']);
        }

        // Crear entrada (cabecera)
        $entrada = Entrada::create([
            'proveedor_id' => $request->proveedor_id,
            'fecha_entrada' => $request->fecha_entrada,
            'observaciones' => $request->observaciones,
            'user_id' => auth()->id(),
        ]);

        // Crear detalles
        foreach ($productosValidos as $producto) {
            EntradaDetalle::create([
                'entrada_id' => $entrada->id,
                'inventario_id' => $producto['inventario_id'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $producto['precio_unitario'],
            ]);
        }

        // Los totales se calculan automáticamente en el modelo
        
        // Guardar en sesión
        session()->flash('entrada_reciente', [
            'id' => $entrada->id,
            'numero_factura' => $entrada->numero_factura,
            'fecha' => $entrada->created_at->format('d/m/Y H:i'),
            'proveedor_nombre' => $entrada->proveedor->proveedor,
            'cantidad_productos' => $entrada->cantidad_productos,
            'cantidad_total' => $entrada->cantidad_total,
            'subtotal' => $entrada->precio_total,
            'iva' => $entrada->iva,
            'total' => $entrada->total_con_iva,
        ]);

        return redirect()->route('entradas.show', $entrada)
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
            return redirect()->route('entradas.show', $entrada)
                ->with('error', 'No se puede eliminar una entrada después de 24 horas');
        }

        // Eliminar detalles primero (esto revertirá el inventario automáticamente)
        $entrada->detalles()->delete();
        $entrada->delete();
        
        return redirect()->route('entradas.index')
            ->with('success', 'Entrada eliminada correctamente');
    }

    public function generatePDF(Entrada $entrada)
    {
        $entrada->load(['proveedor', 'user', 'detalles.inventario']);
        
        $pdf = PDF::loadView('entradas.pdf', compact('entrada'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'entrada_' . $entrada->numero_factura . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function viewPDF(Entrada $entrada)
    {
        $entrada->load(['proveedor', 'user', 'detalles.inventario']);
        
        $pdf = PDF::loadView('entradas.pdf', compact('entrada'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('entrada_' . $entrada->numero_factura . '.pdf');
    }
}