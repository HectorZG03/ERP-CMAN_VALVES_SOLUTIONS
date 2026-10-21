<?php

namespace App\Http\Controllers;

use App\Models\BajaColaborador;
use App\Models\DatosLaborales;
use App\Models\EstadoLaboral;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BajaColaboradorController extends Controller
{
    /**
     * Lista bajas activas y anuladas.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $estado = $request->query('estado', 'todas');

        if (!in_array($estado, ['todas', 'activa', 'anulada'], true)) {
            $estado = 'todas';
        }

        $query = BajaColaborador::query()
            ->with([
                'personal.datosLaborales.estadoLaboral',
                'personal.datosLaborales.puesto.departamento.area',
                'user',
                'usuarioAnulacion',
            ]);

        if ($estado !== 'todas') {
            $query->where('estado', $estado);
        }

        if ($search !== '') {
            $termino = '%' . $search . '%';

            $query->where(function ($bajasQuery) use ($termino) {
                $bajasQuery
                    ->where('motivo_baja', 'like', $termino)
                    ->orWhereHas('personal', function ($personalQuery) use ($termino) {
                        $personalQuery
                            ->whereRaw(
                                "CONCAT_WS(' ', nombre, ape_pat, ape_mat) LIKE ?",
                                [$termino]
                            )
                            ->orWhereHas('datosLaborales', function ($laboralQuery) use ($termino) {
                                $laboralQuery
                                    ->where('employee_id', 'like', $termino)
                                    ->orWhereHas('puesto', function ($puestoQuery) use ($termino) {
                                        $puestoQuery
                                            ->where('nombre', 'like', $termino)
                                            ->orWhereHas('departamento', function ($departamentoQuery) use ($termino) {
                                                $departamentoQuery
                                                    ->where('nombre', 'like', $termino)
                                                    ->orWhereHas('area', function ($areaQuery) use ($termino) {
                                                        $areaQuery->where('nombre', 'like', $termino);
                                                    });
                                            });
                                    });
                            });
                    });
            });
        }

        $bajas = $query
            ->orderByDesc('fecha_baja')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalBajasActivas = BajaColaborador::where('estado', 'activa')->count();
        $totalBajasAnuladas = BajaColaborador::where('estado', 'anulada')->count();

        return view('rh.bajas.index', compact(
            'bajas',
            'search',
            'estado',
            'totalBajasActivas',
            'totalBajasAnuladas'
        ));
    }

    /**
     * Registra una baja desde el modal del listado de personal.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'personal_id' => ['required', 'integer', 'exists:personal,id'],
            'fecha_baja' => ['required', 'date'],
            'motivo_baja' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($datos) {
            $personal = Personal::query()
                ->lockForUpdate()
                ->findOrFail($datos['personal_id']);

            $datosLaborales = DatosLaborales::query()
                ->where('personal_id', $personal->id)
                ->lockForUpdate()
                ->first();

            if (!$datosLaborales) {
                throw ValidationException::withMessages([
                    'personal_id' => 'El colaborador no tiene datos laborales registrados.',
                ]);
            }

            $estadoActual = EstadoLaboral::find($datosLaborales->estado_laboral_id);

            if ($estadoActual?->clave !== 'activo') {
                throw ValidationException::withMessages([
                    'personal_id' => 'Solo se puede registrar la baja de un colaborador activo.',
                ]);
            }

            $bajaActivaExiste = BajaColaborador::query()
                ->where('personal_id', $personal->id)
                ->where('estado', 'activa')
                ->exists();

            if ($bajaActivaExiste) {
                throw ValidationException::withMessages([
                    'personal_id' => 'El colaborador ya tiene una baja activa registrada.',
                ]);
            }

            $estadoBaja = EstadoLaboral::query()
                ->where('clave', 'baja')
                ->first();

            if (!$estadoBaja) {
                throw ValidationException::withMessages([
                    'personal_id' => 'No está configurado el estado laboral "Baja".',
                ]);
            }

            BajaColaborador::create([
                'personal_id' => $personal->id,
                'fecha_baja' => $datos['fecha_baja'],
                'motivo_baja' => $datos['motivo_baja'],
                'estado' => 'activa',
                'user_id' => auth()->id(),
            ]);

            $datosLaborales->update([
                'estado_laboral_id' => $estadoBaja->id,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'La baja del colaborador se registró correctamente.');
    }

    /**
     * Reactiva al colaborador y marca la baja como anulada.
     * El registro permanece como historial.
     */
    public function reactivar(BajaColaborador $baja)
    {
        DB::transaction(function () use ($baja) {
            $bajaBloqueada = BajaColaborador::query()
                ->lockForUpdate()
                ->findOrFail($baja->id);

            if ($bajaBloqueada->estado !== 'activa') {
                throw ValidationException::withMessages([
                    'error' => 'Esta baja ya está anulada.',
                ]);
            }

            $datosLaborales = DatosLaborales::query()
                ->where('personal_id', $bajaBloqueada->personal_id)
                ->lockForUpdate()
                ->first();

            if (!$datosLaborales) {
                throw ValidationException::withMessages([
                    'error' => 'El colaborador no tiene datos laborales registrados.',
                ]);
            }

            $estadoActual = EstadoLaboral::find($datosLaborales->estado_laboral_id);

            if ($estadoActual?->clave !== 'baja') {
                throw ValidationException::withMessages([
                    'error' => 'El estado laboral del colaborador no es Baja.',
                ]);
            }

            $estadoActivo = EstadoLaboral::query()
                ->where('clave', 'activo')
                ->first();

            if (!$estadoActivo) {
                throw ValidationException::withMessages([
                    'error' => 'No está configurado el estado laboral "Activo".',
                ]);
            }

            $bajaBloqueada->estado = 'anulada';
            $bajaBloqueada->anulada_por = auth()->id();
            $bajaBloqueada->save();

            $datosLaborales->update([
                'estado_laboral_id' => $estadoActivo->id,
            ]);
        });

        return redirect()
            ->route('bajas.index')
            ->with('success', 'El colaborador fue reactivado y la baja quedó anulada en el historial.');
    }
}
