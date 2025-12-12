<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventarioExport;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        // Estadísticas de TODO el inventario
        $totalInventario = Inventario::count();
        $totalEnStock = Inventario::where('existencia', '>', 0)->count();
        $totalSinStock = Inventario::where('existencia', '<=', 0)->count();
        $valorTotal = Inventario::sum('precio_total');
        
        // Verificar si se está aplicando un filtro
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');
        
        // Consulta base
        $query = Inventario::query();
        
        // Aplicar filtro de stock si se especifica
        if ($filter === 'with-stock') {
            $query->where('existencia', '>', 0);
        } elseif ($filter === 'without-stock') {
            $query->where('existencia', '<=', 0);
        }
        
        // Aplicar búsqueda si se especifica
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_producto', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%")
                  ->orWhere('economico', 'like', "%{$search}%")
                  ->orWhere('medida', 'like', "%{$search}%");
            });
        }
        
        // Si hay filtro activo o búsqueda, mostrar todos sin paginación
        // Si no, mostrar con paginación
        if ($filter !== 'all' || $search) {
            $inventarios = $query->orderBy('created_at', 'desc')->get();
            $showPagination = false;
        } else {
            $inventarios = $query->orderBy('created_at', 'desc')->paginate(15);
            $showPagination = true;
        }
        
        return view('inventario.index', compact(
            'inventarios', 
            'totalInventario',
            'totalEnStock', 
            'totalSinStock', 
            'valorTotal',
            'filter',
            'search',
            'showPagination'
        ));
    }

    // Nuevo método para búsqueda AJAX
    public function searchAjax(Request $request)
    {
        $search = $request->get('search', '');
        $filter = $request->get('filter', 'all');
        
        $query = Inventario::query();
        
        // Aplicar filtro de stock
        if ($filter === 'with-stock') {
            $query->where('existencia', '>', 0);
        } elseif ($filter === 'without-stock') {
            $query->where('existencia', '<=', 0);
        }
        
        // Aplicar búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_producto', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%")
                  ->orWhere('economico', 'like', "%{$search}%")
                  ->orWhere('medida', 'like', "%{$search}%");
            });
        }
        
        $inventarios = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'html' => view('inventario.partials.table-rows', compact('inventarios'))->render(),
            'count' => $inventarios->count()
        ]);
    }

    // Mantener el método para API si lo necesitas
    public function getAll()
    {
        return response()->json(Inventario::all());
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
            'ubicacion' => $request->ubicacion ?? 'N/A', // ← Esto es lo importante
        ]);

        return redirect()->route('inventario.index')->with('success', 'Producto agregado al inventario');
    }

    public function show(Inventario $inventario)
    {
        $inventario->load(['entradas' => function($query) {
            $query->latest()->take(5);
        }, 'salidas' => function($query) {
            $query->latest()->take(5);
        }]);
        
        return view('inventario.show', compact('inventario'));
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

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado');
    }

    public function destroy(Inventario $inventario)
    {
        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $inventario->delete();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            return redirect()->route('inventario.index')->with('success', 'Producto eliminado exitosamente');
            
        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->route('inventario.index')
                ->withErrors(['error' => 'Error al eliminar el producto: ' . $e->getMessage()]);
        }
    }

    public function exportPDF()
    {
        $inventarios = Inventario::all();
        
        $totalProductos = $inventarios->count();
        $totalEnStock = $inventarios->where('existencia', '>', 0)->count();
        $totalSinStock = $inventarios->where('existencia', '<=', 0)->count();
        $valorTotal = $inventarios->sum('precio_total');
        
        $data = [
            'inventarios' => $inventarios,
            'totalProductos' => $totalProductos,
            'totalEnStock' => $totalEnStock,
            'totalSinStock' => $totalSinStock,
            'valorTotal' => $valorTotal,
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView('inventario.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'inventario_completo_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportExcel()
    {
        $filename = 'inventario_completo_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new InventarioExport, $filename);
    }

    

    public function viewPDF()
    {
        $inventarios = Inventario::all();
        
        $totalProductos = $inventarios->count();
        $totalEnStock = $inventarios->where('existencia', '>', 0)->count();
        $totalSinStock = $inventarios->where('existencia', '<=', 0)->count();
        $valorTotal = $inventarios->sum('precio_total');
        
        $data = [
            'inventarios' => $inventarios,
            'totalProductos' => $totalProductos,
            'totalEnStock' => $totalEnStock,
            'totalSinStock' => $totalSinStock,
            'valorTotal' => $valorTotal,
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView('inventario.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->stream('inventario_completo.pdf');
    }
}