<?php

namespace App\Http\Controllers;

use App\Models\PermisoPersonal;
use App\Models\Personal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermisoPersonalController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        $usuario->loadMissing('personal.datosLaborales.puesto');
        $personalPropio = $usuario->personal;
        $esRh = $usuario->canManagePersonal();
        $esDireccion = $usuario->canApproveRequests();
        $esFinanzasJefe = $usuario->role === 'finanzas';

        $puedeVerTodas = $esRh || $esDireccion || $esFinanzasJefe;

        $query = PermisoPersonal::with([
            'personal.usuario',
            'personal.datosLaborales.puesto',
            'personal.datosLaborales.estadoLaboral',
            'registradoPor',
            'revisadoPor',
            'resueltoPor',
        ]);

        $mensajeVinculo = null;

        if ($esFinanzasJefe) {
            // Finanzas revisa exclusivamente permisos de personas con rol RH.
            $query->whereHas('personal.usuario', function ($subquery) {
                $subquery->whereIn('role', ['rh', 'aux_rh']);
            });
        } elseif (! $puedeVerTodas) {
            if ($personalPropio) {
                $query->where('personal_id', $personalPropio->id);
            } else {
                $query->whereRaw('1 = 0');
                $mensajeVinculo = 'Tu cuenta aún no está vinculada con un expediente de personal. Contacta a TI.';
            }
        }

        $permisos = $query
            ->orderByDesc('fecha_permiso')
            ->orderByDesc('id')
            ->paginate(15);

        return view('permisos.index', [
            'permisos' => $permisos,
            'personalPropio' => $personalPropio,
            'puedeSolicitar' => ! $esDireccion
                && ! $esFinanzasJefe
                && ($esRh || $personalPropio !== null),
            'puedeRegistrarParaOtros' => $esRh,
            'puedeRevisarRh' => $esRh,
            'puedeRevisarFinanzas' => $esFinanzasJefe,
            'puedeRevisar' => $esRh || $esFinanzasJefe,
            'puedeResolver' => $esDireccion,
            'mensajeVinculo' => $mensajeVinculo,
        ]);
    }

    public function buscarPersonal(Request $request)
    {
        abort_unless(
            $request->user()->canManagePersonal(),
            403,
            'Solo Recursos Humanos puede buscar personal para registrar permisos.'
        );

        $termino = trim((string) $request->query('q', ''));

        if (mb_strlen($termino) < 2 && ! ctype_digit($termino)) {
            return response()->json(['resultados' => []]);
        }

        $coincidencia = '%' . $termino . '%';

        $personas = Personal::query()
            ->with('datosLaborales.puesto')
            ->where(function ($query) use ($coincidencia, $termino) {
                $query
                    ->whereRaw(
                        "CONCAT_WS(' ', ape_pat, ape_mat, nombre) LIKE ?",
                        [$coincidencia]
                    )
                    ->orWhere('nombre', 'like', $coincidencia)
                    ->orWhere('ape_pat', 'like', $coincidencia)
                    ->orWhere('ape_mat', 'like', $coincidencia)
                    ->orWhere('correo_electronico', 'like', $coincidencia)
                    ->when(ctype_digit($termino), function ($query) use ($termino) {
                        $query->orWhere('id', (int) $termino);
                    })
                    ->orWhereHas('datosLaborales', function ($laboral) use ($coincidencia) {
                        $laboral->where('employee_id', 'like', $coincidencia);
                    });
            })
            ->orderBy('ape_pat')
            ->orderBy('ape_mat')
            ->orderBy('nombre')
            ->limit(15)
            ->get()
            ->map(fn (Personal $persona) => [
                'id' => $persona->id,
                'nombre' => $persona->nombre_mostrar,
                'numero_empleado' => $persona->datosLaborales?->employee_id,
                'puesto' => $persona->datosLaborales?->puesto?->nombre,
            ])
            ->values();

        return response()->json(['resultados' => $personas]);
    }

    public function store(Request $request)
    {
        $usuario = $request->user();

        abort_if(
            $usuario->canApproveRequests() || $usuario->role === 'finanzas',
            403,
            'Dirección y Finanzas no pueden generar solicitudes de permiso.'
        );

        $esRh = $usuario->canManagePersonal();
        $personalPropio = $usuario->personal;

        if (! $esRh && ! $personalPropio) {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'Tu cuenta no está vinculada con un expediente de personal. Contacta a TI.');
        }

        $reglas = [
            'fecha_permiso' => ['required', 'date'],
            'horas_solicitadas' => ['required', 'numeric', 'gt:0', 'max:24'],
            'motivo' => ['required', 'string', 'max:5000'],
        ];

        if ($esRh) {
            $reglas['personal_id'] = [
                'required',
                'integer',
                'exists:personal,id',
            ];
        }

        $validated = $request->validate($reglas, [
            'personal_id.required' => 'Selecciona a la persona que recibirá el permiso.',
            'personal_id.exists' => 'El expediente de personal seleccionado no existe.',
            'fecha_permiso.required' => 'La fecha del permiso es obligatoria.',
            'horas_solicitadas.required' => 'Indica cuántas horas se solicitan.',
            'horas_solicitadas.gt' => 'Las horas solicitadas deben ser mayores que cero.',
            'horas_solicitadas.max' => 'No se pueden solicitar más de 24 horas.',
            'motivo.required' => 'El motivo del permiso es obligatorio.',
        ]);

        // RH puede registrar para cualquier persona.
        // Los demás usuarios solo pueden solicitar para sí mismos.
        $personalId = $esRh
            ? $validated['personal_id']
            : $personalPropio->id;

        PermisoPersonal::create([
            'personal_id' => $personalId,
            'registrado_por_user_id' => $usuario->id,
            'fecha_permiso' => $validated['fecha_permiso'],
            'horas_solicitadas' => $validated['horas_solicitadas'],
            'motivo' => $validated['motivo'],
            'estatus' => 'pendiente',
            'goce_sueldo' => null,
        ]);

        return redirect()
            ->route('permisos.index')
            ->with('success', $esRh
                ? 'El permiso se registró y está pendiente de revisión.'
                : 'Tu solicitud de permiso fue enviada y está pendiente de revisión.');
    }

    public function show(Request $request, PermisoPersonal $permiso)
    {
        $usuario = $request->user();

        $permiso->load([
            'personal.usuario',
            'registradoPor',
            'revisadoPor',
            'resueltoPor',
        ]);

        $solicitanteEsRh = $this->solicitanteEsRh($permiso);

        $puedeVerTodas = $usuario->canManagePersonal()
            || $usuario->canApproveRequests()
            || ($usuario->role === 'finanzas' && $solicitanteEsRh);

        $esSuPermiso = $usuario->personal_id
            && (int) $permiso->personal_id === (int) $usuario->personal_id;

        abort_unless(
            $puedeVerTodas || $esSuPermiso,
            403,
            'No tienes permiso para consultar esta solicitud.'
        );

        $rolRevisor = $solicitanteEsRh
            ? 'Finanzas'
            : 'Recursos Humanos';

        $etapa = match (true) {
            $permiso->estatus !== 'pendiente' => $permiso->estatus,
            ! $permiso->revisado_at && $solicitanteEsRh => 'pendiente_revision_finanzas',
            ! $permiso->revisado_at => 'pendiente_revision_rh',
            default => 'pendiente_aprobacion_direccion',
        };

        return response()->json([
            'permiso' => [
                'id' => $permiso->id,
                'fecha_permiso' => $permiso->fecha_permiso?->format('Y-m-d'),
                'horas_solicitadas' => $permiso->horas_solicitadas,
                'motivo' => $permiso->motivo,
                'estatus' => $permiso->estatus,
                'etapa' => $etapa,
                'rol_revisor' => $rolRevisor,
                'goce_sueldo' => $permiso->goce_sueldo,
                'revisado_at' => $permiso->revisado_at?->format('d/m/Y H:i'),
                'comentario_revision' => $permiso->comentario_revision,
                'resuelto_at' => $permiso->resuelto_at?->format('d/m/Y H:i'),
                'comentario_resolucion' => $permiso->comentario_resolucion,
                'personal' => [
                    'id' => $permiso->personal?->id,
                    'nombre' => $permiso->personal?->nombre_mostrar,
                ],
                'registrado_por' => $permiso->registradoPor
                    ? [
                        'nombre' => $permiso->registradoPor->name,
                        'correo' => $permiso->registradoPor->email,
                    ]
                    : null,
                'revisado_por' => $permiso->revisadoPor
                    ? [
                        'nombre' => $permiso->revisadoPor->name,
                        'correo' => $permiso->revisadoPor->email,
                    ]
                    : null,
                'resuelto_por' => $permiso->resueltoPor
                    ? [
                        'nombre' => $permiso->resueltoPor->name,
                        'correo' => $permiso->resueltoPor->email,
                    ]
                    : null,
            ],
        ]);
    }

    public function revisar(Request $request, PermisoPersonal $permiso)
    {
        $usuario = $request->user();

        $permiso->loadMissing('personal.usuario');

        $solicitanteEsRh = $this->solicitanteEsRh($permiso);

        $puedeRevisarEstePermiso = $solicitanteEsRh
            ? $usuario->role === 'finanzas'
            : $usuario->canManagePersonal();

        abort_unless(
            $puedeRevisarEstePermiso,
            403,
            $solicitanteEsRh
                ? 'Solo Finanzas puede revisar permisos solicitados por RH.'
                : 'Solo Recursos Humanos puede revisar este permiso.'
        );

        $usuarioSolicitante = $permiso->personal?->usuario;

        if (
            $usuarioSolicitante
            && (int) $usuarioSolicitante->id === (int) $usuario->id
        ) {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'Otro responsable debe revisar tu propio permiso.');
        }

        if ($permiso->estatus !== 'pendiente') {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'Solo se pueden revisar permisos pendientes.');
        }

        if ($permiso->revisado_at) {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'Este permiso ya fue revisado.');
        }

        $validated = $request->validate([
            'comentario_revision' => ['nullable', 'string', 'max:5000'],
        ], [
            'comentario_revision.max' => 'El comentario de revisión no puede exceder 5000 caracteres.',
        ]);

        $permiso->update([
            'revisado_por_user_id' => $usuario->id,
            'revisado_at' => now(),
            'comentario_revision' => $validated['comentario_revision'] ?? null,
        ]);

        return redirect()
            ->route('permisos.index')
            ->with(
                'success',
                'La revisión quedó registrada. El permiso está pendiente de Dirección.'
            );
    }

    public function resolver(Request $request, PermisoPersonal $permiso)
    {
        abort_unless(
            $request->user()->canApproveRequests(),
            403,
            'Solo Dirección puede resolver solicitudes de permisos.'
        );

        if ($permiso->estatus !== 'pendiente') {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'Esta solicitud ya fue resuelta.');
        }

        if (! $permiso->revisado_at) {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'El permiso debe ser revisado antes de que Dirección lo resuelva.');
        }

        $validated = $request->validate([
            'estatus' => ['required', Rule::in(['aprobado', 'rechazado'])],
            'goce_sueldo' => [
                Rule::requiredIf($request->input('estatus') === 'aprobado'),
                'nullable',
                'boolean',
            ],
            'comentario_resolucion' => ['nullable', 'string', 'max:5000'],
        ], [
            'estatus.required' => 'Selecciona si el permiso se aprueba o rechaza.',
            'estatus.in' => 'La resolución seleccionada no es válida.',
            'goce_sueldo.required' => 'Al aprobar, indica si el permiso es con o sin goce de sueldo.',
        ]);

        $permiso->update([
            'estatus' => $validated['estatus'],
            'goce_sueldo' => $validated['estatus'] === 'aprobado'
                ? (bool) $validated['goce_sueldo']
                : null,
            'resuelto_por_user_id' => $request->user()->id,
            'resuelto_at' => now(),
            'comentario_resolucion' => $validated['comentario_resolucion'] ?? null,
        ]);

        return redirect()
            ->route('permisos.index')
            ->with('success', 'La solicitud de permiso fue resuelta por Dirección.');
    }

    public function pdf(Request $request, PermisoPersonal $permiso)
    {
        $usuario = $request->user();

        $permiso->load([
            'personal.usuario',
            'personal.datosLaborales.puesto',
            'revisadoPor',
            'resueltoPor',
        ]);

        $solicitanteEsRh = $this->solicitanteEsRh($permiso);

        $puedeVerTodas = $usuario->canManagePersonal()
            || $usuario->canApproveRequests()
            || ($usuario->role === 'finanzas' && $solicitanteEsRh);

        $esSuPermiso = $usuario->personal_id
            && (int) $permiso->personal_id === (int) $usuario->personal_id;

        abort_unless(
            $puedeVerTodas || $esSuPermiso,
            403,
            'No tienes permiso para descargar este documento.'
        );

        abort_unless(
            $permiso->estatus === 'aprobado',
            403,
            'El PDF estará disponible cuando Dirección apruebe el permiso.'
        );

        $firmaSolicitante = $this->obtenerFirmaDataUri(
            $permiso->personal?->usuario?->signature
        );

        $firmaRevisor = $this->obtenerFirmaDataUri(
            $permiso->revisadoPor?->signature
        );

        $firmaDireccion = $this->obtenerFirmaDataUri(
            $permiso->resueltoPor?->signature
        );

        $rolRevisor = $solicitanteEsRh
            ? 'Finanzas'
            : 'Recursos Humanos';

        $pdf = Pdf::loadView('permisos.pdf', [
            'permiso' => $permiso,
            'firmaSolicitante' => $firmaSolicitante,
            'firmaRh' => $firmaRevisor,
            'firmaDireccion' => $firmaDireccion,
            'rolRevisor' => $rolRevisor,
            'fechaGeneracion' => now()->format('d/m/Y'),
        ])->setPaper('letter', 'portrait');

        $nombrePersonal = implode(' ', array_filter([
            $permiso->personal?->ape_pat,
            $permiso->personal?->ape_mat,
            $permiso->personal?->nombre,
        ]));

        $nombreArchivo = Str::upper(
            'PERMISO_AUSENCIA_' .
            $permiso->id . '_' .
            (Str::slug($nombrePersonal, '_') ?: 'SIN_PERSONAL') .
            '.pdf'
        );

        return $pdf->download($nombreArchivo);
    }

    private function solicitanteEsRh(PermisoPersonal $permiso): bool
    {
        return in_array(
            $permiso->personal?->usuario?->role,
            ['rh', 'aux_rh'],
            true
        );
    }

    private function obtenerFirmaDataUri(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }

        $archivo = storage_path(
            'app/public/' . ltrim($ruta, '/')
        );

        if (! is_file($archivo)) {
            return null;
        }

        $mime = mime_content_type($archivo) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(
            file_get_contents($archivo)
        );
    }
}
