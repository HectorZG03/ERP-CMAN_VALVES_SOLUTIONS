<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;


class OrdenCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = OrdenCompra::with('user')->orderBy('created_at', 'desc');
        
        // Aplicar búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhere('nombre_proveedor', 'like', "%{$search}%")
                  ->orWhere('email_proveedor', 'like', "%{$search}%");
            });
        }
        
        $ordenes = $query->paginate(15)->withQueryString();
        
        // Estadísticas
        $totalOrdenes = OrdenCompra::count();
        $totalGastado = OrdenCompra::sum('total_general');
        
        return view('OrdenCompra.index', compact('ordenes', 'search', 'totalOrdenes', 'totalGastado'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('OrdenCompra.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_proveedor'    => 'required|string|max:255',
            'direccion_proveedor' => 'nullable|string|max:500',
            'telefono_proveedor'  => 'nullable|string|max:50',
            'email_proveedor'     => 'nullable|email|max:255',
            'envio'               => 'nullable|numeric|min:0',
            'otros'               => 'nullable|numeric|min:0',
            'comentarios'         => 'nullable|string|max:1000',
            'articulos'           => 'required|array|min:1',
            'articulos.*.codigo'  => 'required|string|max:100',
            'articulos.*.descripcion' => 'required|string|max:500',
            'articulos.*.cantidad' => 'required|numeric|min:0.01',
            'articulos.*.unidad'  => 'required|string|max:50',
            'articulos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Calcular subtotal
            $subtotal = 0;
            foreach ($request->articulos as $articulo) {
                $subtotal += $articulo['cantidad'] * $articulo['precio_unitario'];
            }
            
            $envio = (float) $request->envio ?? 0;
            $otros = (float) $request->otros ?? 0;
            $iva = round($subtotal * 0.16, 2);
            $totalGeneral = $subtotal + $iva + $envio + $otros;
            
            // Crear la orden de compra
            $ordenCompra = OrdenCompra::create([
                'folio'               => OrdenCompra::generarFolio(),
                'nombre_proveedor'    => $request->nombre_proveedor,
                'direccion_proveedor' => $request->direccion_proveedor,
                'telefono_proveedor'  => $request->telefono_proveedor,
                'email_proveedor'     => $request->email_proveedor,
                'envio'               => $envio,
                'otros'               => $otros,
                'subtotal'            => $subtotal,
                'iva'                 => $iva,
                'total_general'       => $totalGeneral,
                'comentarios'         => $request->comentarios,
                'user_id'             => Auth::id(),
            ]);
            
            // Crear los detalles
            foreach ($request->articulos as $articulo) {
                $totalArticulo = $articulo['cantidad'] * $articulo['precio_unitario'];
                
                OrdenCompraDetalle::create([
                    'orden_compra_id'  => $ordenCompra->id,
                    'codigo'           => $articulo['codigo'],
                    'descripcion'      => $articulo['descripcion'],
                    'cantidad'         => $articulo['cantidad'],
                    'unidad'           => $articulo['unidad'],
                    'precio_unitario'  => $articulo['precio_unitario'],
                    'total'            => $totalArticulo,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('orden-compra.show', $ordenCompra->id)
                ->with('success', "Orden de Compra {$ordenCompra->folio} creada exitosamente.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear la Orden de Compra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrdenCompra $ordenCompra)
    {
        $ordenCompra->load(['detalles', 'user']);
        
        return view('OrdenCompra.show', compact('ordenCompra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
 * Show the form for editing the specified resource.
 */
/**
 * Show the form for editing the specified resource.
 */
public function edit(OrdenCompra $ordenCompra)
{
    $ordenCompra->load(['detalles' => function($query) {
        $query->orderBy('created_at', 'asc');
    }]);
    
    // Si ya fue generada hace más de 24 horas, no permitir edición
    if ($ordenCompra->created_at->diffInHours(now()) > 24) {
        return redirect()->route('orden-compra.index')
            ->withErrors(['error' => 'No se pueden editar órdenes con más de 24 horas de antigüedad.']);
    }
    
    // Preparar los artículos existentes como array PHP (no como colección)
    $articulosExistentes = [];
    foreach ($ordenCompra->detalles as $detalle) {
        $articulosExistentes[] = [
            'codigo'          => $detalle->codigo,
            'descripcion'     => $detalle->descripcion,
            'cantidad'        => $detalle->cantidad,
            'unidad'          => $detalle->unidad,
            'precio_unitario' => $detalle->precio_unitario,
        ];
    }
    
    // Convertir a JSON en PHP
    $articulosJson = json_encode($articulosExistentes);
    
    return view('OrdenCompra.edit', compact('ordenCompra', 'articulosJson'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrdenCompra $ordenCompra)
    {
        $request->validate([
            'nombre_proveedor'    => 'required|string|max:255',
            'direccion_proveedor' => 'nullable|string|max:500',
            'telefono_proveedor'  => 'nullable|string|max:50',
            'email_proveedor'     => 'nullable|email|max:255',
            'envio'               => 'nullable|numeric|min:0',
            'otros'               => 'nullable|numeric|min:0',
            'comentarios'         => 'nullable|string|max:1000',
            'articulos'           => 'required|array|min:1',
            'articulos.*.codigo'  => 'required|string|max:100',
            'articulos.*.descripcion' => 'required|string|max:500',
            'articulos.*.cantidad' => 'required|numeric|min:0.01',
            'articulos.*.unidad'  => 'required|string|max:50',
            'articulos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Calcular subtotal
            $subtotal = 0;
            foreach ($request->articulos as $articulo) {
                $subtotal += $articulo['cantidad'] * $articulo['precio_unitario'];
            }
            
            $envio = (float) $request->envio ?? 0;
            $otros = (float) $request->otros ?? 0;
            $iva = round($subtotal * 0.16, 2);
            $totalGeneral = $subtotal + $iva + $envio + $otros;
            
            // Actualizar la orden
            $ordenCompra->update([
                'nombre_proveedor'    => $request->nombre_proveedor,
                'direccion_proveedor' => $request->direccion_proveedor,
                'telefono_proveedor'  => $request->telefono_proveedor,
                'email_proveedor'     => $request->email_proveedor,
                'envio'               => $envio,
                'otros'               => $otros,
                'subtotal'            => $subtotal,
                'iva'                 => $iva,
                'total_general'       => $totalGeneral,
                'comentarios'         => $request->comentarios,
            ]);
            
            // Eliminar detalles antiguos
            $ordenCompra->detalles()->delete();
            
            // Crear nuevos detalles
            foreach ($request->articulos as $articulo) {
                $totalArticulo = $articulo['cantidad'] * $articulo['precio_unitario'];
                
                OrdenCompraDetalle::create([
                    'orden_compra_id'  => $ordenCompra->id,
                    'codigo'           => $articulo['codigo'],
                    'descripcion'      => $articulo['descripcion'],
                    'cantidad'         => $articulo['cantidad'],
                    'unidad'           => $articulo['unidad'],
                    'precio_unitario'  => $articulo['precio_unitario'],
                    'total'            => $totalArticulo,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('orden-compra.show', $ordenCompra->id)
                ->with('success', "Orden de Compra {$ordenCompra->folio} actualizada exitosamente.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrdenCompra $ordenCompra)
    {
        try {
            $folio = $ordenCompra->folio;
            
            // Eliminar detalles primero
            $ordenCompra->detalles()->delete();
            
            // Eliminar la orden
            $ordenCompra->delete();
            
            return redirect()->route('orden-compra.index')
                ->with('success', "Orden de Compra {$folio} eliminada exitosamente.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    /**
     * Buscar productos para autocomplete.
     */
    /**
 * Buscar productos para autocomplete.
 */
public function buscarProductos(Request $request)
{
    $term = $request->get('q', '');
    
    if (strlen($term) < 2) {
        return response()->json([]);
    }
    
    $productos = Inventario::where('nombre_producto', 'LIKE', "%{$term}%")
        ->orWhere('economico', 'LIKE', "%{$term}%")
        ->orderBy('nombre_producto')
        ->limit(10)
        ->get(['id', 'nombre_producto', 'economico', 'medida', 'existencia', 'precio_total']);
    
    // Calcular precio unitario promedio si existe
    $productos->transform(function($producto) {
        $producto->precio_unitario = $producto->existencia > 0 
            ? round($producto->precio_total / $producto->existencia, 2) 
            : 0;
        return $producto;
    });
    
    return response()->json($productos);
}


    /**
     * Devuelve PDF de una orden de compra.
     */
    /**
     * Genera y descarga el PDF de una orden de compra.
     */
    

public function pdf(OrdenCompra $ordenCompra)
{
    $html = view('OrdenCompra.pdf', compact('ordenCompra'))->render();

    $pdf = Browsershot::html($html)
        ->format('Letter')
        ->showBackground()
        ->margins(0,0,0,0)
        ->pdf();

    return response($pdf)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="orden.pdf"');
}

}