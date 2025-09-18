<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Inventario;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;

class EntradaController extends Controller
{
    public function index()
    {
        $entradas = Entrada::with(['inventario', 'proveedor', 'user'])->paginate(15);
        return view('entradas.index', compact('entradas'));
    }

    public function create()
    {
        $inventarios = Inventario::all();
        $proveedores = Proveedor::all();
        return view('entradas.create', compact('inventarios', 'proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventario_id' => 'required|exists:inventarios,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $inventario = Inventario::find($request->inventario_id);
        $precio_total = $request->cantidad * $request->precio_unitario;
        $iva = $precio_total * 0.16;
        $total_con_iva = $precio_total + $iva;

        // Crear entrada
        $entrada = Entrada::create([
            'inventario_id' => $request->inventario_id,
            'proveedor_id' => $request->proveedor_id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'precio_total' => $precio_total,
            'iva' => $iva,
            'total_con_iva' => $total_con_iva,
            'user_id' => auth()->id(),
        ]);

        // Actualizar inventario
        $inventario->existencia += $request->cantidad;
        $inventario->precio_total += $precio_total;
        $inventario->save();

        return redirect()->route('entradas.index')->with('success', 'Entrada registrada correctamente');
    }

    public function show(Entrada $entrada)
    {
        // Cargar todas las relaciones necesarias
        $entrada->load(['inventario', 'proveedor', 'user']);
        
        return view('entradas.show', compact('entrada'));
    }

    // Método para generar PDF de la entrada
    public function generatePDF(Entrada $entrada)
    {
        // Cargar todas las relaciones necesarias
        $entrada->load(['inventario', 'proveedor', 'user']);
        
        // Crear el PDF con los datos
        $pdf = PDF::loadView('entradas.pdf', compact('entrada'));
        
        // Configurar tamaño de página
        $pdf->setPaper('A4', 'portrait');
        
        // Definir nombre del archivo
        $filename = 'entrada_' . str_pad($entrada->id, 6, '0', STR_PAD_LEFT) . '_' . date('Y-m-d') . '.pdf';
        
        // Descargar el PDF
        return $pdf->download($filename);
    }

    // Método alternativo para ver el PDF en el navegador
    public function viewPDF(Entrada $entrada)
    {
        $entrada->load(['inventario', 'proveedor', 'user']);
        
        $pdf = PDF::loadView('entradas.pdf', compact('entrada'));
        $pdf->setPaper('A4', 'portrait');
        
        // Mostrar en el navegador en lugar de descargar
        return $pdf->stream('entrada_' . $entrada->id . '.pdf');
    }
}