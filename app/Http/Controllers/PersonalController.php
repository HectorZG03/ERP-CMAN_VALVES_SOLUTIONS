<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personal;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');
        
        $query = Personal::query();
        
        // Aplicar filtro de estatus
        if ($filter === 'activo') {
            $query->activo();
        } elseif ($filter === 'baja') {
            $query->baja();
        }
        
        // Aplicar búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('departamento', 'like', "%{$search}%")
                  ->orWhere('grado', 'like', "%{$search}%");
            });
        }
        
        $personal = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Estadísticas
        $totalPersonal = Personal::count();
        $totalActivo = Personal::activo()->count();
        $totalBaja = Personal::baja()->count();
        $totalSueldos = Personal::activo()->sum('sueldo');
        
        return view('personal.index', compact(
            'personal',
            'totalPersonal',
            'totalActivo',
            'totalBaja',
            'totalSueldos',
            'filter',
            'search'
        ));
    }

    public function create()
    {
        return view('personal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'sueldo' => 'required|numeric|min:0',
            'grado' => 'nullable|string|max:255',
        ]);

        Personal::create($request->all());

        return redirect()->route('personal.index')
            ->with('success', 'Colaborador agregado exitosamente');
    }

    public function show(Personal $personal)
    {
        $personal->load([
            'bajas',
            'cambiosPuestoSueldo' => function($query) {
                $query->orderBy('fecha_cambio', 'desc');
            },
            'valepp' => function($query) {
                $query->with('detalles.inventario')->orderBy('fecha_solicitud', 'desc');
            }
        ]);

        return view('personal.show', compact('personal'));
    }

    public function edit(Personal $personal)
    {
        return view('personal.edit', compact('personal'));
    }

    public function update(Request $request, Personal $personal)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'sueldo' => 'required|numeric|min:0',
            'grado' => 'nullable|string|max:255',
        ]);

        $personal->update($request->all());

        return redirect()->route('personal.index')
            ->with('success', 'Colaborador actualizado exitosamente');
    }

    public function destroy(Personal $personal)
    {
        try {
            $personal->delete();
            return redirect()->route('personal.index')
                ->with('success', 'Colaborador eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('personal.index')
                ->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    // Buscar personal para otros formularios
    public function buscar(Request $request)
    {
        $search = $request->get('q', '');
        
        $personal = Personal::activo()
            ->where('nombre_completo', 'like', "%{$search}%")
            ->orWhere('area', 'like', "%{$search}%")
            ->limit(10)
            ->get();
        
        return response()->json($personal);
    }

    // Obtener datos de un colaborador específico
    public function getDatos($id)
    {
        $personal = Personal::findOrFail($id);
        return response()->json($personal);
    }

    public function exportPDF()
    {
        $personal = Personal::orderBy('nombre_completo')->get();
        
        $data = [
            'personal' => $personal,
            'totalActivo' => Personal::activo()->count(),
            'totalBaja' => Personal::baja()->count(),
            'totalSueldos' => Personal::activo()->sum('sueldo'),
            'fechaGeneracion' => now()
        ];

        $pdf = PDF::loadView('personal.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('personal_' . date('Y-m-d') . '.pdf');
    }
}