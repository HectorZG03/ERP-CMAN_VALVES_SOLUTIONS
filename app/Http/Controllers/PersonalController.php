<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('departamento', 'like', "%{$search}%")
                  ->orWhere('grado', 'like', "%{$search}%")
                  ->orWhere('curp', 'like', "%{$search}%")
                  ->orWhere('rfc', 'like', "%{$search}%");
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
        $validated = $request->validate([
            // Campos obligatorios
            'nombre_completo' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'division' => 'required|in:Operativa,Administrativa',
            
            // Campos opcionales - Información Personal
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employee_id' => 'nullable|string|max:50',
            'edad' => 'nullable|integer|min:18|max:100',
            'nacionalidad' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            
            'estado_civil' => 'nullable|string|max:50',
            'grupo_sanguineo' => 'nullable|string|max:10',
            
            // Campos opcionales - Documentos
            'curp' => 'nullable|string|max:18',
            'rfc' => 'nullable|string|max:13',
            'nss' => 'nullable|string|max:11',
            'clave_interbancaria' => 'nullable|string|max:18',
            
            // Campos opcionales - Contacto
            'direccion' => 'nullable|string|max:500',
            'correo_electronico' => 'nullable|email|max:255',
            'numero_telefonico' => 'nullable|string|max:20',
            
            // Campos opcionales - Emergencia
            'nombre_contacto_emergencia' => 'nullable|string|max:255',
            'numero_telefonico_emergencia' => 'nullable|string|max:20',
            
            // Campos opcionales - Otros
            'grado' => 'nullable|string|max:255',
            'sueldo' => 'nullable|numeric|min:0',
            'bonos' => 'nullable|numeric|min:0',
            'enfermedad_alergia' => 'nullable|string|max:1000',
        ]);

        // Manejar la carga de foto
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('personal/fotos', 'public');
            $validated['foto'] = $fotoPath;
        }

        // Crear el registro
        Personal::create($validated);

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
        $validated = $request->validate([
            // Campos obligatorios
            'nombre_completo' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            
            // Campos opcionales - Información Personal
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employee_id' => 'nullable|string|max:50',
            'edad' => 'nullable|integer|min:18|max:100',
            'nacionalidad' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'sexo' => 'nullable|in:Masculino,Femenino,Otro',
            'division' => 'required|in:Operativa,Administrativa',

            'estado_civil' => 'nullable|Operativo,Administrativo,N/A',
            'estado_civil' => 'nullable|string|max:50',
            'grupo_sanguineo' => 'nullable|string|max:10',
            
            // Campos opcionales - Documentos
            'curp' => 'nullable|string|max:18',
            'rfc' => 'nullable|string|max:13',
            'nss' => 'nullable|string|max:11',
            'clave_interbancaria' => 'nullable|string|max:18',
            
            // Campos opcionales - Contacto
            'direccion' => 'nullable|string|max:500',
            'correo_electronico' => 'nullable|email|max:255',
            'numero_telefonico' => 'nullable|string|max:20',
            
            // Campos opcionales - Emergencia
            'nombre_contacto_emergencia' => 'nullable|string|max:255',
            'numero_telefonico_emergencia' => 'nullable|string|max:20',
            
            // Campos opcionales - Otros
            'grado' => 'nullable|string|max:255',
            'sueldo' => 'nullable|numeric|min:0',
            'bonos' => 'nullable|numeric|min:0',
            'enfermedad_alergia' => 'nullable|string|max:1000',
        ]);

        // Manejar la carga de nueva foto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($personal->foto && $personal->foto !== 'N/A' && Storage::disk('public')->exists($personal->foto)) {
                Storage::disk('public')->delete($personal->foto);
            }
            $fotoPath = $request->file('foto')->store('personal/fotos', 'public');
            $validated['foto'] = $fotoPath;
        }

        $personal->update($validated);

        return redirect()->route('personal.index')
            ->with('success', 'Colaborador actualizado exitosamente');
    }

    public function destroy(Personal $personal)
    {
        try {
            // Eliminar foto si existe
            if ($personal->foto && $personal->foto !== 'N/A' && Storage::disk('public')->exists($personal->foto)) {
                Storage::disk('public')->delete($personal->foto);
            }
            
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
            ->orWhere('employee_id', 'like', "%{$search}%")
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