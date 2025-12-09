<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\SalidaDetalle;
use App\Models\Inventario;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::with(['cliente', 'user', 'detalles.inventario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('salidas.index', compact('salidas'));
    }

    public function create()
    {
        $inventarios = Inventario::where('existencia', '>', 0)->get();
        $clientes = Cliente::all();
        
        $salidaReciente = session('salida_reciente', null);
        
        return view('salidas.create', compact('inventarios', 'clientes', 'salidaReciente'));
    }

    public function store(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'fecha_salida' => 'required|date',
        'observaciones' => 'nullable|string|max:500',
        'productos' => 'required|array|min:1',
        'productos.*.inventario_id' => 'required|exists:inventarios,id',
        'productos.*.cantidad' => 'required|integer|min:1',
    ]);

    $erroresStock = [];
    $productosValidos = [];

    // Validar stock y preparar datos
    foreach ($request->productos as $index => $producto) {
        $inventario = Inventario::find($producto['inventario_id']);
        
        if (!$inventario) {
            $erroresStock[] = "Producto #" . ($index + 1) . ": No encontrado";
            continue;
        }

        if ($inventario->existencia < $producto['cantidad']) {
            $erroresStock[] = "Producto #" . ($index + 1) . ": " . 
                $inventario->nombre_producto . " - Stock insuficiente. Disponible: " . 
                $inventario->existencia . ", Solicitado: " . $producto['cantidad'];
            continue;
        }

        $precioUnitario = $inventario->getPrecioPromedio();
        
        $productosValidos[] = [
            'inventario_id' => $producto['inventario_id'],
            'cantidad' => $producto['cantidad'],
            'precio_unitario' => $precioUnitario,
        ];
    }

    if (!empty($erroresStock) && empty($productosValidos)) {
        return back()->withErrors(['productos' => implode("\n", $erroresStock)]);
    }

    // Crear salida (cabecera) - SOLO con los campos correctos
    $salida = Salida::create([
        'cliente_id' => $request->cliente_id,
        'fecha_salida' => $request->fecha_salida,
        'observaciones' => $request->observaciones,
        'user_id' => auth()->id(),
        // NO incluir cantidad, precio_unitario, etc. - se calcularán de los detalles
    ]);

    // Crear detalles
    foreach ($productosValidos as $producto) {
        SalidaDetalle::create([
            'salida_id' => $salida->id,
            'inventario_id' => $producto['inventario_id'],
            'cantidad' => $producto['cantidad'],
            'precio_unitario' => $producto['precio_unitario'],
        ]);
    }

    // Los totales se calculan automáticamente en el modelo
    
    // Guardar en sesión
    session()->flash('salida_reciente', [
        'id' => $salida->id,
        'numero_factura' => $salida->numero_factura,
        'fecha' => $salida->created_at->format('d/m/Y H:i'),
        'cliente_nombre' => $salida->cliente->nombre,
        'cliente_area' => $salida->cliente->area,
        'cantidad_productos' => $salida->cantidad_productos,
        'cantidad_total' => $salida->cantidad_total,
        'subtotal' => $salida->precio_total,
        'iva' => $salida->iva,
        'total' => $salida->total_con_iva,
    ]);

    if (!empty($erroresStock)) {
        session()->flash('warning', "Algunos productos no pudieron ser procesados:\n" . 
                       implode("\n", $erroresStock));
        
        return redirect()->route('salidas.show', $salida)
            ->with('success', 'Salida registrada parcialmente');
    }

    return redirect()->route('salidas.show', $salida)
        ->with('success', 'Salida registrada correctamente');
}

    public function show(Salida $salida)
    {
        $salida->load(['cliente', 'user', 'detalles.inventario']);
        
        return view('salidas.show', compact('salida'));
    }

    public function destroy(Salida $salida)
    {
        if ($salida->created_at->diffInHours(now()) > 24) {
            return redirect()->route('salidas.show', $salida)
                ->with('error', 'No se puede eliminar una salida después de 24 horas');
        }

        // Eliminar detalles primero (esto revertirá el inventario automáticamente)
        $salida->detalles()->delete();
        $salida->delete();
        
        return redirect()->route('salidas.index')
            ->with('success', 'Salida eliminada correctamente');
    }

    public function generatePDF(Salida $salida)
    {
        $salida->load(['cliente', 'user', 'detalles.inventario']);
        
        $pdf = PDF::loadView('salidas.pdf', compact('salida'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'salida_' . $salida->numero_factura . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function viewPDF(Salida $salida)
    {
        $salida->load(['cliente', 'user', 'detalles.inventario']);
        
        $pdf = PDF::loadView('salidas.pdf', compact('salida'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('salida_' . $salida->numero_factura . '.pdf');
    }
}