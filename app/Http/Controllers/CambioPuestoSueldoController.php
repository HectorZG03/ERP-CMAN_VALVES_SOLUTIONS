<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CambioPuestoSueldo;
use App\Models\Personal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CambioPuestoSueldoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = CambioPuestoSueldo::with('personal', 'user');
        
        if ($search) {
            $query->whereHas('personal', function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }
        
        $cambios = $query->orderBy('fecha_cambio', 'desc')->paginate(15);
        
        $totalCambios = CambioPuestoSueldo::count();
        
        return view('cambios.index', compact('cambios', 'totalCambios', 'search'));
    }

    public function create()
    {
        // Solo mostrar personal activo
        $personalActivo = Personal::activo()
            ->orderBy('nombre_completo')
            ->get();
        
        return view('cambios.create', compact('personalActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id',
            'puesto_nuevo' => 'required|string|max:255',
            'sueldo_nuevo' => 'required|numeric|min:0',
            'fecha_cambio' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $personal = Personal::findOrFail($request->personal_id);
            
            // Registrar el cambio con los datos anteriores
            CambioPuestoSueldo::create([
                'personal_id' => $request->personal_id,
                'puesto_anterior' => $personal->grado ?? 'N/A',
                'puesto_nuevo' => $request->puesto_nuevo,
                'sueldo_anterior' => $personal->sueldo,
                'sueldo_nuevo' => $request->sueldo_nuevo,
                'fecha_cambio' => $request->fecha_cambio,
                'observaciones' => $request->observaciones,
                'user_id' => Auth::id(),
            ]);
            
            // Actualizar el personal con los nuevos datos
            $personal->update([
                'grado' => $request->puesto_nuevo,
                'sueldo' => $request->sueldo_nuevo,
            ]);
            
            DB::commit();
            
            return redirect()->route('cambios.index')
                ->with('success', 'Cambio de puesto y sueldo registrado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error al registrar el cambio: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(CambioPuestoSueldo $cambio)
    {
        $cambio->load('personal', 'user');
        
        return view('cambios.show', compact('cambio'));
    }

    public function edit(CambioPuestoSueldo $cambio)
    {
        $personalActivo = Personal::activo()
            ->orWhere('id', $cambio->personal_id)
            ->orderBy('nombre_completo')
            ->get();
        
        return view('cambios.edit', compact('cambio', 'personalActivo'));
    }

    public function update(Request $request, CambioPuestoSueldo $cambio)
    {
        $request->validate([
            'puesto_nuevo' => 'required|string|max:255',
            'sueldo_nuevo' => 'required|numeric|min:0',
            'fecha_cambio' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Actualizar el registro de cambio
            $cambio->update([
                'puesto_nuevo' => $request->puesto_nuevo,
                'sueldo_nuevo' => $request->sueldo_nuevo,
                'fecha_cambio' => $request->fecha_cambio,
                'observaciones' => $request->observaciones,
            ]);
            
            // Actualizar el personal con los nuevos datos
            $cambio->personal->update([
                'grado' => $request->puesto_nuevo,
                'sueldo' => $request->sueldo_nuevo,
            ]);
            
            DB::commit();
            
            return redirect()->route('cambios.index')
                ->with('success', 'Cambio actualizado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(CambioPuestoSueldo $cambio)
    {
        try {
            $cambio->delete();
            
            return redirect()->route('cambios.index')
                ->with('success', 'Registro de cambio eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('cambios.index')
                ->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    // Obtener historial de cambios de un colaborador
    public function historial($personal_id)
    {
        $personal = Personal::findOrFail($personal_id);
        $cambios = CambioPuestoSueldo::where('personal_id', $personal_id)
            ->orderBy('fecha_cambio', 'desc')
            ->get();
        
        return view('cambios.historial', compact('personal', 'cambios'));
    }

    public function exportPDF()
    {
        $cambios = CambioPuestoSueldo::with('personal')
            ->orderBy('fecha_cambio', 'desc')
            ->get();
        
        $data = [
            'cambios' => $cambios,
            'totalCambios' => $cambios->count(),
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView('cambios.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('cambios_puesto_sueldo_' . date('Y-m-d') . '.pdf');
    }
}