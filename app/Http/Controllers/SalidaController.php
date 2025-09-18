<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\Inventario;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with(['inventario', 'cliente', 'user'])->paginate(15);
        return view('salidas.index', compact('salidas'));
    }

    public function create()
    {
        $inventarios = Inventario::where('existencia', '>', 0)->get();
        $clientes = Cliente::all();
        return view('salidas.create', compact('inventarios', 'clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventario_id' => 'required|exists:inventarios,id',
            'cliente_id' => 'required|exists:clientes,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $inventario = Inventario::find($request->inventario_id);

        if ($inventario->existencia < $request->cantidad) {
            return back()->withErrors(['cantidad' => 'No hay suficiente existencia']);
        }

        // Calcular precio promedio actual
        $precio_unitario = $inventario->getPrecioPromedio();
        $precio_total = $request->cantidad * $precio_unitario;
        $iva = $precio_total * 0.16;
        $total_con_iva = $precio_total + $iva;

        // Crear salida
        $salida = Salida::create([
            'inventario_id' => $request->inventario_id,
            'cliente_id' => $request->cliente_id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $precio_unitario,
            'precio_total' => $precio_total,
            'iva' => $iva,
            'total_con_iva' => $total_con_iva,
            'user_id' => auth()->id(),
        ]);

        // Actualizar inventario
        $inventario->existencia -= $request->cantidad;
        $inventario->precio_total -= $precio_total;
        $inventario->save();

        return redirect()->route('salidas.index')->with('success', 'Salida registrada correctamente');
    }

    public function show(Salida $salida)
    {
        // Cargar todas las relaciones necesarias
        $salida->load(['inventario', 'cliente', 'user']);
        
        return view('salidas.show', compact('salida'));
    }

    // Método para generar PDF de la salida
    public function generatePDF(Salida $salida)
    {
        // Cargar todas las relaciones necesarias
        $salida->load(['inventario', 'cliente', 'user']);
        
        // Crear el PDF con los datos
        $pdf = PDF::loadView('salidas.pdf', compact('salida'));
        
        // Configurar tamaño de página
        $pdf->setPaper('A4', 'portrait');
        
        // Definir nombre del archivo
        $filename = 'salida_' . str_pad($salida->id, 6, '0', STR_PAD_LEFT) . '_' . date('Y-m-d') . '.pdf';
        
        // Descargar el PDF
        return $pdf->download($filename);
    }

    // Método alternativo para ver el PDF en el navegador
    public function viewPDF(Salida $salida)
    {
        $salida->load(['inventario', 'cliente', 'user']);
        
        $pdf = PDF::loadView('salidas.pdf', compact('salida'));
        $pdf->setPaper('A4', 'portrait');
        
        // Mostrar en el navegador en lugar de descargar
        return $pdf->stream('salida_' . $salida->id . '.pdf');
    }
}