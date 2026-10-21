<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\DatosLaborales;
use App\Models\EstadoLaboral;
use App\Models\Personal;
use App\Models\Puesto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class PersonalController extends Controller
{
    private const CAMPOS_PERSONALES = [
        'nombre',
        'ape_pat',
        'ape_mat',
        'edad',
        'nacionalidad',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'grupo_sanguineo',
        'curp',
        'rfc',
        'nss',
        'clave_interbancaria',
        'direccion',
        'correo_electronico',
        'numero_telefonico',
        'nombre_contacto_emergencia',
        'numero_telefonico_emergencia',
        'enfermedad_alergia',
    ];

    private function catalogosArea()
    {
        return Area::with(['departamentos.puestos'])
            ->orderBy('nombre')
            ->get();
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = trim($request->get('search', ''));

        $query = Personal::with([
            'datosLaborales.puesto.departamento.area',
            'datosLaborales.estadoLaboral',
        ]);

        if (in_array($filter, ['activo', 'baja', 'suspendido'], true)) {
            $query->whereHas('datosLaborales.estadoLaboral', function ($q) use ($filter) {
                $q->where('clave', $filter);
            });
        }

        if ($search !== '') {
            $like = "%{$search}%";

            $query->where(function ($q) use ($like) {
                $q->whereRaw(
                    "CONCAT_WS(' ', nombre, ape_pat, ape_mat) LIKE ?",
                    [$like]
                )
                ->orWhere('rfc', 'like', $like)
                ->orWhere('curp', 'like', $like)
                ->orWhere('correo_electronico', 'like', $like)
                ->orWhere('numero_telefonico', 'like', $like)
                ->orWhereHas('datosLaborales', function ($laboral) use ($like) {
                    $laboral->where('employee_id', 'like', $like)
                        ->orWhereRaw("DATE_FORMAT(fecha_ingreso, '%d/%m/%Y') LIKE ?", [$like]);
                })
                ->orWhereHas('datosLaborales.puesto', function ($puesto) use ($like) {
                    $puesto->where('nombre', 'like', $like)
                        ->orWhereHas('departamento', function ($departamento) use ($like) {
                            $departamento->where('nombre', 'like', $like)
                                ->orWhereHas('area', function ($area) use ($like) {
                                    $area->where('nombre', 'like', $like);
                                });
                        });
                });
            });
        }

        $personal = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $totalPersonal = Personal::count();

        $totalActivo = Personal::whereHas('datosLaborales.estadoLaboral', function ($q) {
            $q->where('clave', 'activo');
        })->count();

        $totalBaja = Personal::whereHas('datosLaborales.estadoLaboral', function ($q) {
            $q->where('clave', 'baja');
        })->count();

        $totalSuspendido = Personal::whereHas('datosLaborales.estadoLaboral', function ($q) {
            $q->where('clave', 'suspendido');
        })->count();

        $totalAdministrativos = Personal::where(function ($query) {
            $query->whereHas('datosLaborales.puesto.departamento.area', function ($area) {
                $area->whereRaw('LOWER(nombre) LIKE ?', ['%administrativ%']);
            })->orWhere(function ($sinAreaAsignada) {
                $sinAreaAsignada
                    ->whereDoesntHave('datosLaborales.puesto.departamento.area')
                    ->whereHas('datosLaborales', function ($laboral) {
                        $laboral->where('employee_id', 'like', 'CMAN-ADM-%');
                    });
            });
        })->count();

        $totalOperativos = Personal::where(function ($query) {
            $query->whereHas('datosLaborales.puesto.departamento.area', function ($area) {
                $area->whereRaw('LOWER(nombre) LIKE ?', ['%operativ%']);
            })->orWhere(function ($sinAreaAsignada) {
                $sinAreaAsignada
                    ->whereDoesntHave('datosLaborales.puesto.departamento.area')
                    ->whereHas('datosLaborales', function ($laboral) {
                        $laboral->where('employee_id', 'like', 'CMAN-OP-%');
                    });
            });
        })->count();

        $totalSueldos = DatosLaborales::whereHas('estadoLaboral', function ($q) {
            $q->where('clave', 'activo');
        })->sum('sueldo');

        return view('personal.index', compact(
            'personal',
            'totalPersonal',
            'totalActivo',
            'totalBaja',
            'totalSuspendido',
            'totalAdministrativos',
            'totalOperativos',
            'totalSueldos',
            'filter',
            'search'
        ));
    }

    public function create()
    {
        $areas = $this->catalogosArea();

        return view('personal.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ape_pat' => 'required|string|max:255',
            'ape_mat' => 'required|string|max:255',

            'area_id' => ['required', 'integer', Rule::exists('areas', 'id')],
            'departamento_id' => [
                'required',
                'integer',
                Rule::exists('departamentos', 'id')
                    ->where(fn ($q) => $q->where('area_id', $request->input('area_id'))),
            ],
            'puesto_id' => [
                'required',
                'integer',
                Rule::exists('puestos', 'id')
                    ->where(fn ($q) => $q->where('departamento_id', $request->input('departamento_id'))),
            ],

            'employee_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('datos_laborales', 'employee_id'),
            ],
            'fecha_ingreso' => 'required|date',
            'sueldo' => 'nullable|numeric|min:0',

            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'edad' => 'nullable|integer|min:18|max:100',
            'nacionalidad' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'estado_civil' => 'nullable|string|max:50',
            'grupo_sanguineo' => 'nullable|string|max:10',
            'curp' => 'nullable|string|max:18',
            'rfc' => 'nullable|string|max:13',
            'nss' => 'nullable|string|max:11',
            'clave_interbancaria' => 'nullable|string|max:18',
            'direccion' => 'nullable|string|max:500',
            'correo_electronico' => 'nullable|email|max:255',
            'numero_telefonico' => 'nullable|string|max:20',
            'nombre_contacto_emergencia' => 'nullable|string|max:255',
            'numero_telefonico_emergencia' => 'nullable|string|max:20',
            'enfermedad_alergia' => 'nullable|string|max:1000',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('personal/fotos', 'public');
        }

        try {
            DB::transaction(function () use ($validated, $fotoPath) {
                $datosPersonales = Arr::only($validated, self::CAMPOS_PERSONALES);

                if ($fotoPath) {
                    $datosPersonales['foto'] = $fotoPath;
                }

                $personal = Personal::create($datosPersonales);

                $estadoActivoId = EstadoLaboral::where('clave', 'activo')->value('id');

                if (!$estadoActivoId) {
                    throw new \RuntimeException('No está registrado el estado laboral Activo.');
                }

                $personal->datosLaborales()->create([
                    'puesto_id' => $validated['puesto_id'],
                    'estado_laboral_id' => $estadoActivoId,
                    'employee_id' => $validated['employee_id'] ?? null,
                    'fecha_ingreso' => $validated['fecha_ingreso'],
                    'sueldo' => $validated['sueldo'] ?? null,
                ]);
            });
        } catch (Throwable $e) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }

            throw $e;
        }

        return redirect()->route('personal.index')
            ->with('success', 'Colaborador agregado exitosamente.');
    }

    public function show(Personal $personal)
    {
        $personal->load([
            'datosLaborales.puesto.departamento.area',
            'datosLaborales.estadoLaboral',
        ]);

        $areas = $this->catalogosArea();

        return view('personal.perfil_personal', compact('personal', 'areas'));
    }

    public function edit(Personal $personal)
    {
        return redirect()->route('personal.show', $personal);
    }

    public function update(Request $request, Personal $personal)
    {
        $datosLaboralesActuales = $personal->datosLaborales;

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ape_pat' => 'required|string|max:255',
            'ape_mat' => 'required|string|max:255',

            'area_id' => ['required', 'integer', Rule::exists('areas', 'id')],
            'departamento_id' => [
                'required',
                'integer',
                Rule::exists('departamentos', 'id')
                    ->where(fn ($q) => $q->where('area_id', $request->input('area_id'))),
            ],
            'puesto_id' => [
                'required',
                'integer',
                Rule::exists('puestos', 'id')
                    ->where(fn ($q) => $q->where('departamento_id', $request->input('departamento_id'))),
            ],

            'employee_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('datos_laborales', 'employee_id')
                    ->ignore($datosLaboralesActuales?->id),
            ],
            'fecha_ingreso' => 'required|date',
            'sueldo' => 'nullable|numeric|min:0',

            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'edad' => 'nullable|integer|min:18|max:100',
            'nacionalidad' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'estado_civil' => 'nullable|string|max:50',
            'grupo_sanguineo' => 'nullable|string|max:10',
            'curp' => 'nullable|string|max:18',
            'rfc' => 'nullable|string|max:13',
            'nss' => 'nullable|string|max:11',
            'clave_interbancaria' => 'nullable|string|max:18',
            'direccion' => 'nullable|string|max:500',
            'correo_electronico' => 'nullable|email|max:255',
            'numero_telefonico' => 'nullable|string|max:20',
            'nombre_contacto_emergencia' => 'nullable|string|max:255',
            'numero_telefonico_emergencia' => 'nullable|string|max:20',
            'enfermedad_alergia' => 'nullable|string|max:1000',
        ]);

        $fotoAnterior = $personal->foto;
        $fotoNueva = null;

        if ($request->hasFile('foto')) {
            $fotoNueva = $request->file('foto')->store('personal/fotos', 'public');
        }

        try {
            DB::transaction(function () use ($validated, $personal, $fotoNueva) {
                $datosPersonales = Arr::only($validated, self::CAMPOS_PERSONALES);

                if ($fotoNueva) {
                    $datosPersonales['foto'] = $fotoNueva;
                }

                $personal->update($datosPersonales);

                $estadoActualId = $personal->datosLaborales?->estado_laboral_id;

                if (!$estadoActualId) {
                    $estadoActualId = EstadoLaboral::where('clave', 'activo')->value('id');
                }

                if (!$estadoActualId) {
                    throw new \RuntimeException('No está registrado un estado laboral para la persona.');
                }

                $personal->datosLaborales()->updateOrCreate(
                    ['personal_id' => $personal->id],
                    [
                        'puesto_id' => $validated['puesto_id'],
                        'estado_laboral_id' => $estadoActualId,
                        'employee_id' => $validated['employee_id'] ?? null,
                        'fecha_ingreso' => $validated['fecha_ingreso'],
                        'sueldo' => $validated['sueldo'] ?? null,
                    ]
                );
            });
        } catch (Throwable $e) {
            if ($fotoNueva) {
                Storage::disk('public')->delete($fotoNueva);
            }

            throw $e;
        }

        if (
            $fotoNueva
            && $fotoAnterior
            && $fotoAnterior !== 'N/A'
            && Storage::disk('public')->exists($fotoAnterior)
        ) {
            Storage::disk('public')->delete($fotoAnterior);
        }

        return redirect()->route('personal.show', $personal)
            ->with('success', 'Colaborador actualizado exitosamente.');
    }

    public function destroy(Personal $personal)
    {
        try {
            if (
                $personal->foto
                && $personal->foto !== 'N/A'
                && Storage::disk('public')->exists($personal->foto)
            ) {
                Storage::disk('public')->delete($personal->foto);
            }

            $personal->delete();

            return redirect()->route('personal.index')
                ->with('success', 'Colaborador eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('personal.index')
                ->withErrors([
                    'error' => 'Error al eliminar: ' . $e->getMessage(),
                ]);
        }
    }

    public function buscar(Request $request)
    {
        $search = trim($request->get('q', ''));

        $personal = Personal::with([
            'datosLaborales.puesto.departamento.area',
            'datosLaborales.estadoLaboral',
        ])
            ->whereHas('datosLaborales.estadoLaboral', function ($query) {
                $query->where('clave', 'activo');
            })
            ->where(function ($query) use ($search) {
                $query->whereRaw(
                    "CONCAT_WS(' ', nombre, ape_pat, ape_mat) LIKE ?",
                    ["%{$search}%"]
                )
                ->orWhereHas('datosLaborales', function ($laboral) use ($search) {
                    $laboral->where('employee_id', 'like', "%{$search}%");
                });
            })
            ->limit(10)
            ->get();

        return response()->json($personal->map(function ($persona) {
            $laboral = $persona->datosLaborales;
            $puesto = $laboral?->puesto;
            $departamento = $puesto?->departamento;

            return [
                'id' => $persona->id,
                'nombre' => $persona->nombre,
                'ape_pat' => $persona->ape_pat,
                'ape_mat' => $persona->ape_mat,
                'nombre_mostrar' => $persona->nombre_mostrar,
                'employee_id' => $laboral?->employee_id,
                'area' => $departamento?->area?->nombre,
                'departamento' => $departamento?->nombre,
                'puesto' => $puesto?->nombre,
            ];
        }));
    }

    public function getDatos($id)
    {
        $personal = Personal::with([
            'datosLaborales.puesto.departamento.area',
            'datosLaborales.estadoLaboral',
        ])->findOrFail($id);

        $laboral = $personal->datosLaborales;
        $puesto = $laboral?->puesto;
        $departamento = $puesto?->departamento;

        return response()->json([
            'id' => $personal->id,
            'nombre' => $personal->nombre,
            'ape_pat' => $personal->ape_pat,
            'ape_mat' => $personal->ape_mat,
            'nombre_mostrar' => $personal->nombre_mostrar,
            'employee_id' => $laboral?->employee_id,
            'fecha_ingreso' => $laboral?->fecha_ingreso?->format('Y-m-d'),
            'sueldo' => $laboral?->sueldo,
            'estado_laboral' => $laboral?->estadoLaboral?->clave,
            'area' => $departamento?->area?->nombre,
            'departamento' => $departamento?->nombre,
            'puesto' => $puesto?->nombre,
        ]);
    }

    public function exportPDF()
    {
        $personal = Personal::with([
            'datosLaborales.puesto.departamento.area',
            'datosLaborales.estadoLaboral',
        ])
            ->orderBy('ape_pat')
            ->orderBy('ape_mat')
            ->orderBy('nombre')
            ->get();

        $totalActivo = Personal::whereHas('datosLaborales.estadoLaboral', function ($query) {
            $query->where('clave', 'activo');
        })->count();

        $totalBaja = Personal::whereHas('datosLaborales.estadoLaboral', function ($query) {
            $query->where('clave', 'baja');
        })->count();

        $totalSueldos = DatosLaborales::whereHas('estadoLaboral', function ($query) {
            $query->where('clave', 'activo');
        })->sum('sueldo');

        $pdf = Pdf::loadView('personal.pdf', [
            'personal' => $personal,
            'totalActivo' => $totalActivo,
            'totalBaja' => $totalBaja,
            'totalSueldos' => $totalSueldos,
            'fechaGeneracion' => now(),
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('personal_' . now()->format('Y-m-d') . '.pdf');
    }
}