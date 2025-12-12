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
    public function index()
    {
        $entradas = Entrada::with(['proveedor', 'user', 'detalles.inventario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('entradas.index', compact('entradas'));
    }

    public function create()
    {
        $inventarios = Inventario::all();
        $proveedores = Proveedor::all();
        
        $entradaReciente = session('entrada_reciente', null);
        
        return view('entradas.create', compact('inventarios', 'proveedores', 'entradaReciente'));
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