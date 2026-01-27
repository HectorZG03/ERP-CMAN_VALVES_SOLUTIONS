<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valepp;
use App\Models\ValeppDetalle;
use App\Models\Personal;
use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ValeppController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = Valepp::with('personal', 'user', 'detalles.inventario');
        
        // Aplicar búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_vale', 'like', "%{$search}%")
                  ->orWhereHas('personal', function($pq) use ($search) {
                      $pq->where('nombre_completo', 'like', "%{$search}%")
                        ->orWhere('area', 'like', "%{$search}%");
                  });
            });
        }
        
        $valepp = $query->orderBy('fecha_solicitud', 'desc')->paginate(15);
        
        // Estadística simple
        $totalVales = Valepp::count();
        
        return view('valepp.index', compact(
            'valepp',
            'totalVales',
            'search'
        ));
    }

    public function create()
    {
        // Personal activo e inventario con existencia
        $personalActivo = Personal::activo()
            ->orderBy('nombre_completo')
            ->get();
        
        $inventarios = Inventario::where('existencia', '>', 0)
            ->orderBy('nombre_producto')
            ->get();
        
        // Generar número de vale automáticamente
        $numeroVale = Valepp::generarNumeroVale();
        
        return view('valepp.create', compact('personalActivo', 'inventarios', 'numeroVale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id',
            'fecha_solicitud' => 'required|date',
            'observaciones' => 'nullable|string',
            'inventario_id' => 'required|array|min:1',
            'inventario_id.*' => 'required|exists:inventarios,id',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            // Validar que haya suficiente existencia
            foreach ($request->inventario_id as $index => $inventario_id) {
                $inventario = Inventario::findOrFail($inventario_id);
                $cantidad = $request->cantidad[$index];
                
                if ($inventario->existencia < $cantidad) {
                    throw new \Exception("No hay suficiente existencia de {$inventario->nombre_producto}. Disponible: {$inventario->existencia}");
                }
            }
            
            // Crear el vale
            $valepp = Valepp::create([
                'numero_vale' => Valepp::generarNumeroVale(),
                'personal_id' => $request->personal_id,
                'fecha_solicitud' => $request->fecha_solicitud,
                'observaciones' => $request->observaciones,
                'user_id' => Auth::id(),
            ]);
            
            // Crear los detalles y descontar del inventario
            foreach ($request->inventario_id as $index => $inventario_id) {
                $inventario = Inventario::findOrFail($inventario_id);
                $cantidad = $request->cantidad[$index];
                
                // Descontar del inventario
                $inventario->decrement('existencia', $cantidad);
                
                // Actualizar precio total proporcional
                $precioPromedio = $inventario->getPrecioPromedio();
                $inventario->decrement('precio_total', $precioPromedio * $cantidad);
                
                // Crear detalle del vale con fecha de entrega actual
                ValeppDetalle::create([
                    'valepp_id' => $valepp->id,
                    'inventario_id' => $inventario_id,
                    'cantidad' => $cantidad,
                    'fecha_entrega' => now(), // Se entrega inmediatamente
                    'observaciones' => $request->observaciones_detalle[$index] ?? null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('valepp.index')
                ->with('success', 'Vale PP creado y materiales entregados exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el vale: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Valepp $valepp)
    {
        $valepp->load('personal', 'user', 'detalles.inventario');
        
        return view('valepp.show', compact('valepp'));
    }

    public function edit(Valepp $valepp)
    {
        // No permitir edición una vez creado
        return redirect()->route('valepp.index')
            ->withErrors(['error' => 'Los vales no se pueden editar una vez creados']);
    }

    public function update(Request $request, Valepp $valepp)
    {
        return redirect()->route('valepp.index')
            ->withErrors(['error' => 'Los vales no se pueden editar']);
    }

    public function destroy(Valepp $valepp)
    {
        try {
            // No permitir eliminar una vez creado
            return redirect()->route('valepp.index')
                ->withErrors(['error' => 'Los vales no se pueden eliminar una vez creados']);
                
        } catch (\Exception $e) {
            return redirect()->route('valepp.index')
                ->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }



    




    public function exportPDF(Valepp $valepp)
{
    $valepp->load('personal', 'user', 'detalles.inventario');
    
    $data = [
        'valepp' => $valepp,
        'fechaGeneracion' => now()->format('d/m/Y H:i:s')
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('valepp.pdf', $data);
    $pdf->setPaper('A4', 'portrait');
    
    return $pdf->download("vale_pp_{$valepp->numero_vale}.pdf");
}





}