<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventarioExport;
use App\Models\EntradaDetalle;
use App\Models\SalidaDetalle;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'filter' => 'nullable|in:all,with-stock,without-stock',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|in:15,25,50,100',
            'page' => 'nullable|integer|min:1',
        ]);

        $totalInventario = Inventario::count();
        $totalEnStock = Inventario::where('existencia', '>', 0)->count();
        $totalSinStock = Inventario::where('existencia', '<=', 0)->count();
        $valorTotal = Inventario::sum('precio_total');

        $filter = $validated['filter'] ?? 'all';
        $search = trim($validated['search'] ?? '');
        $perPage = (int) ($validated['per_page'] ?? 15);

        $inventarios = $this->buildInventoryQuery($search, $filter)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('inventario.index', compact(
            'inventarios',
            'totalInventario',
            'totalEnStock',
            'totalSinStock',
            'valorTotal',
            'filter',
            'search',
            'perPage'
        ));
    }


    /**
     * Devuelve una página de resultados para la búsqueda dinámica.
     */
    public function searchAjax(Request $request)
    {
        $validated = $request->validate([
            'filter' => 'required|in:all,with-stock,without-stock',
            'search' => 'nullable|string|max:255',
            'per_page' => 'required|integer|in:15,25,50,100',
            'page' => 'nullable|integer|min:1',
        ]);

        $search = trim($validated['search'] ?? '');
        $filter = $validated['filter'];
        $perPage = (int) $validated['per_page'];
        $page = (int) ($validated['page'] ?? 1);

        $inventarios = $this->buildInventoryQuery($search, $filter)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'html' => view('inventario.partials.table-rows', compact('inventarios'))->render(),
            'total' => $inventarios->total(),
            'from' => $inventarios->firstItem(),
            'to' => $inventarios->lastItem(),
            'current_page' => $inventarios->currentPage(),
            'last_page' => $inventarios->lastPage(),
            'per_page' => $inventarios->perPage(),
        ]);
    }


    /**
     * Comparte los filtros entre la carga inicial y la búsqueda AJAX.
     */
    private function buildInventoryQuery(string $search, string $filter)
    {
        $query = Inventario::query();

        if ($filter === 'with-stock') {
            $query->where('existencia', '>', 0);
        } elseif ($filter === 'without-stock') {
            $query->where('existencia', '<=', 0);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_producto', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%")
                    ->orWhere('economico', 'like', "%{$search}%")
                    ->orWhere('medida', 'like', "%{$search}%")
                    ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // Mantener el método para API si lo necesitas
    public function getAll()
    {
        return response()->json(
            Inventario::all()
        );
    }


    public function create()
    {
        return view('inventario.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string|max:255',
            'nombre_producto' => 'required|string|max:255',
            'economico' => 'required|string|max:255',
            'medida' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        // Crear el registro con ubicación por defecto si está vacía
        Inventario::create([
            'categoria' => $request->categoria,
            'nombre_producto' => $request->nombre_producto,
            'economico' => $request->economico,
            'medida' => $request->medida,
            'ubicacion' => $request->ubicacion ?? 'N/A',
        ]);

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Producto agregado al inventario');
    }


    public function show(Inventario $inventario)
    {
        // Obtener y formatear entradas
        $entradasDetalles = EntradaDetalle::with([
            'entrada' => function ($query) {
                $query->with([
                    'proveedor',
                    'user'
                ]);
            }
        ])
            ->where('inventario_id', $inventario->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($detalle) use ($inventario) {
                return [
                    'id' => $detalle->entrada->id ?? null,
                    'numero_factura' => $detalle->entrada->numero_factura ?? 'N/A',
                    'proveedor' => $detalle->entrada->proveedor->proveedor ?? 'N/A',
                    'usuario' => $detalle->entrada->user->name ?? 'N/A',
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'precio_total' => $detalle->precio_total,
                    'iva' => $detalle->iva,
                    'total_con_iva' => $detalle->total_con_iva,
                    'fecha' => $detalle->created_at,
                    'observaciones' => $detalle->entrada->observaciones ?? 'Sin observaciones',
                    'tipo' => 'entrada',
                    'medida' => $inventario->medida,
                ];
            });

        // Obtener y formatear salidas
        $salidasDetalles = SalidaDetalle::with([
            'salida' => function ($query) {
                $query->with([
                    'cliente',
                    'user'
                ]);
            }
        ])
            ->where('inventario_id', $inventario->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($detalle) use ($inventario) {
                return [
                    'id' => $detalle->salida->id ?? null,
                    'numero_factura' => $detalle->salida->numero_factura ?? 'N/A',
                    'cliente' => $detalle->salida->cliente->nombre ?? 'N/A',
                    'area_cliente' => $detalle->salida->cliente->area ?? 'N/A',
                    'usuario' => $detalle->salida->user->name ?? 'N/A',
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'precio_total' => $detalle->precio_total,
                    'iva' => $detalle->iva,
                    'total_con_iva' => $detalle->total_con_iva,
                    'fecha' => $detalle->created_at,
                    'observaciones' => $detalle->salida->observaciones ?? 'Sin observaciones',
                    'tipo' => 'salida',
                    'medida' => $inventario->medida,
                ];
            });

        // Combinar y ordenar por fecha
        $movimientos = $entradasDetalles
            ->merge($salidasDetalles)
            ->sortByDesc('fecha')
            ->take(10);

        return view(
            'inventario.show',
            compact(
                'inventario',
                'movimientos'
            )
        );
    }


    public function edit(Inventario $inventario)
    {
        return view('inventario.edit', compact('inventario'));
    }


    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'categoria' => 'required|string|max:255',
            'nombre_producto' => 'required|string|max:255',
            'economico' => 'required|string|max:255',
            'medida' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        // Actualizar con ubicación por defecto si está vacía
        $inventario->update([
            'categoria' => $request->categoria,
            'nombre_producto' => $request->nombre_producto,
            'economico' => $request->economico,
            'medida' => $request->medida,
            'ubicacion' => $request->ubicacion ?? 'N/A',
        ]);

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Producto actualizado');
    }


    public function destroy(Inventario $inventario)
    {
        if ($inventario->ajustes()->exists()) {
            return redirect()
                ->route('inventario.index')
                ->with(
                    'error',
                    'No se puede eliminar un producto que tiene ajustes registrados. El historial debe conservarse.'
                );
        }

        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $inventario->delete();

            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()
                ->route('inventario.index')
                ->with(
                    'success',
                    'Producto eliminado exitosamente'
                );

        } catch (\Exception $e) {

            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()
                ->route('inventario.index')
                ->withErrors([
                    'error' =>
                        'Error al eliminar el producto: '
                        . $e->getMessage()
                ]);
        }
    }


    public function exportPDF()
    {
        $inventarios = Inventario::all();

        $totalProductos = $inventarios->count();

        $totalEnStock = $inventarios
            ->where('existencia', '>', 0)
            ->count();

        $totalSinStock = $inventarios
            ->where('existencia', '<=', 0)
            ->count();

        $valorTotal = $inventarios
            ->sum('precio_total');

        $data = [
            'inventarios' => $inventarios,
            'totalProductos' => $totalProductos,
            'totalEnStock' => $totalEnStock,
            'totalSinStock' => $totalSinStock,
            'valorTotal' => $valorTotal,
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView(
            'inventario.pdf',
            $data
        );

        $pdf->setPaper(
            'A4',
            'landscape'
        );

        $filename =
            'inventario_completo_'
            . date('Y-m-d_H-i-s')
            . '.pdf';

        return $pdf->download($filename);
    }


    public function exportExcel()
    {
        $filename =
            'inventario_completo_'
            . date('Y-m-d_H-i-s')
            . '.xlsx';

        return Excel::download(
            new InventarioExport,
            $filename
        );
    }


    public function viewPDF()
    {
        $inventarios = Inventario::all();

        $totalProductos = $inventarios->count();

        $totalEnStock = $inventarios
            ->where('existencia', '>', 0)
            ->count();

        $totalSinStock = $inventarios
            ->where('existencia', '<=', 0)
            ->count();

        $valorTotal = $inventarios
            ->sum('precio_total');

        $data = [
            'inventarios' => $inventarios,
            'totalProductos' => $totalProductos,
            'totalEnStock' => $totalEnStock,
            'totalSinStock' => $totalSinStock,
            'valorTotal' => $valorTotal,
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView(
            'inventario.pdf',
            $data
        );

        $pdf->setPaper(
            'A4',
            'landscape'
        );

        return $pdf->stream(
            'inventario_completo.pdf'
        );
    }
}
