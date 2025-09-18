<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventarioExport;

class InventarioController extends Controller
{
    public function index()
    {
        $inventarios = Inventario::paginate(15);
        return view('inventario.index', compact('inventarios'));
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
            'medida' => 'required|string|max:255',
        ]);

        Inventario::create($request->all());

        return redirect()->route('inventario.index')->with('success', 'Producto agregado al inventario');
    }

    public function show(Inventario $inventario)
    {
        // Cargar relaciones para mostrar historial
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
            'medida' => 'required|string|max:255',
        ]);

        $inventario->update($request->all());

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado');
    }

    public function destroy(Inventario $inventario)
    {
        // Verificar si tiene movimientos antes de eliminar
        $tieneMovimientos = $inventario->entradas()->exists() || $inventario->salidas()->exists() || $inventario->solicitudesMateriales()->exists();
        
        if ($tieneMovimientos) {
            return redirect()->route('inventario.index')->withErrors(['error' => 'No se puede eliminar el producto porque tiene movimientos asociados (entradas, salidas o solicitudes).']);
        }
        
        $inventario->delete();
        return redirect()->route('inventario.index')->with('success', 'Producto eliminado');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $inventarios = Inventario::where('nombre_producto', 'like', "%{$term}%")
                                ->where('existencia', '>', 0)
                                ->get(['id', 'nombre_producto', 'existencia']);
        
        return response()->json($inventarios);
    }

    // Método para exportar inventario completo a PDF
    public function exportPDF()
    {
        $inventarios = Inventario::all();
        
        // Calcular totales
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
        $pdf->setPaper('A4', 'landscape'); // Horizontal para más columnas
        
        $filename = 'inventario_completo_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    // Método para exportar inventario completo a Excel
    public function exportExcel()
    {
        $filename = 'inventario_completo_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new InventarioExport, $filename);
    }

    // Método alternativo para ver el PDF en el navegador
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