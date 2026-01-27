<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BajaColaborador;
use App\Models\Personal;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BajaColaboradorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = BajaColaborador::with('personal', 'user');
        
        if ($search) {
            $query->whereHas('personal', function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }
        
        $bajas = $query->orderBy('fecha_baja', 'desc')->paginate(15);
        
        $totalBajas = BajaColaborador::count();
        
        return view('bajas.index', compact('bajas', 'totalBajas', 'search'));
    }

    public function create()
    {
        // Solo mostrar personal activo
        $personalActivo = Personal::activo()
            ->orderBy('nombre_completo')
            ->get();
        
        return view('bajas.create', compact('personalActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id',
            'fecha_baja' => 'required|date',
            'motivo_baja' => 'required|string',
        ]);

        // Crear la baja
        BajaColaborador::create([
            'personal_id' => $request->personal_id,
            'fecha_baja' => $request->fecha_baja,
            'motivo_baja' => $request->motivo_baja,
            'user_id' => Auth::id(),
        ]);

        // Actualizar el estatus del personal a 'baja'
        $personal = Personal::findOrFail($request->personal_id);
        $personal->update(['estatus' => 'baja']);

        return redirect()->route('bajas.index')
            ->with('success', 'Baja registrada exitosamente');
    }

    public function show(BajaColaborador $baja)
    {
        $baja->load('personal', 'user');
        
        return view('bajas.show', compact('baja'));
    }

    public function edit(BajaColaborador $baja)
    {
        $personalActivo = Personal::activo()
            ->orWhere('id', $baja->personal_id)
            ->orderBy('nombre_completo')
            ->get();
        
        return view('bajas.edit', compact('baja', 'personalActivo'));
    }

    public function update(Request $request, BajaColaborador $baja)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id',
            'fecha_baja' => 'required|date',
            'motivo_baja' => 'required|string',
        ]);

        $baja->update([
            'personal_id' => $request->personal_id,
            'fecha_baja' => $request->fecha_baja,
            'motivo_baja' => $request->motivo_baja,
        ]);

        return redirect()->route('bajas.index')
            ->with('success', 'Baja actualizada exitosamente');
    }

    public function destroy(BajaColaborador $baja)
    {
        try {
            // Reactivar al personal
            $personal = $baja->personal;
            $personal->update(['estatus' => 'activo']);
            
            $baja->delete();
            
            return redirect()->route('bajas.index')
                ->with('success', 'Baja eliminada y colaborador reactivado');
        } catch (\Exception $e) {
            return redirect()->route('bajas.index')
                ->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

     public function exportPDF()
    {
        $bajas = BajaColaborador::with('personal')->orderBy('fecha_baja', 'desc')->get();
        
        $data = [
            'bajas' => $bajas,
            'totalBajas' => $bajas->count(),
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView('bajas.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('reporte_bajas_' . date('Y-m-d') . '.pdf');
    }

    // PDF individual de una baja específica
    public function exportIndividualPDF(BajaColaborador $baja)
    {
        $baja->load('personal', 'user');
        
        $data = [
            'baja' => $baja,
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView('bajas.individual-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $nombreArchivo = 'baja_' . str_pad($baja->id, 4, '0', STR_PAD_LEFT) . '_' . str_replace(' ', '_', $baja->personal->nombre_completo) . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

}