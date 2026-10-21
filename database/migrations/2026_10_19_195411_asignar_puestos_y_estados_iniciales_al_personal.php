<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function asignaciones(): array
    {
        return [
            'CMAN-ADM-001' => ['DIRECCION', 'DIRECTOR', 'activo'],
            'CMAN-ADM-004' => ['CALIDAD', 'SUPERVISOR DE CALIDAD', 'activo'],
            'CMAN-ADM-005' => ['FINANZAS', 'GERENTE ADMO. Y FINANZAS', 'activo'],
            'CMAN-ADM-007' => ['ALMACEN', 'ALMACEN', 'activo'],
            'CMAN-ADM-008' => ['OPERACIONES', 'INTENDENCIA', 'activo'],
            'CMAN-ADM-010' => ['RECURSOS HUMANOS', 'RECURSOS HUMANOS', 'activo'],
            'CMAN-ADM-012' => ['DIRECCION', 'JURIDICO', 'baja'],
            'CMAN-ADM-015' => ['LOGISTICA', 'LOGISTICA', 'activo'],
            'CMAN-ADM-016' => ['OPERACIONES', 'COORDINADOR DE OPERACIONES', 'baja'],
            'CMAN-ADM-019' => ['CONTABILIDAD', 'CONTABILIDAD', 'activo'],
            'CMAN-ADM-021' => ['SEGURIDAD', 'SUPERVISORA DE HSE', 'activo'],
            'CMAN-ADM-023' => ['CONTABILIDAD', 'AUXILIAR CONTABLE', 'activo'],
            'CMAN-ADM-024' => ['ESTIMACIONES', 'COORDINADORA DE ESTIMACIONES', 'baja'],
            'CMAN-ADM-027' => ['OPERACIONES', 'AUXILIAR DE OPERACIONES', 'baja'],
            'CMAN-ADM-028' => ['TECNOLOGIAS DE LA INFORMACION', 'COORDINADOR DE T.I.', 'baja'],

            'CMAN-OP-004' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-007' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'baja'],
            'CMAN-OP-012' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'baja'],
            'CMAN-OP-014' => ['OPERACIONES', 'TECNICO ESPECIALISTA SR', 'activo'],
            'CMAN-OP-015' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-017' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-020' => ['OPERACIONES', 'TECNICO ESPECIALISTA SR', 'activo'],
            'CMAN-OP-022' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-023' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'baja'],
            'CMAN-OP-029' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-032' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-034' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-035' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-036' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-037' => ['OPERACIONES', 'TECNICO ESPECIALISTA SEMI SR', 'activo'],
            'CMAN-OP-040' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-047' => ['OPERACIONES', 'AYUDANTE TECNICO JR', 'activo'],
            'CMAN-OP-048' => ['OPERACIONES', 'TECNICO ESPECIALISTA SR', 'activo'],
            'CMAN-OP-052' => ['OPERACIONES', 'AYUDANTE TECNICO JR', 'activo'],
            'CMAN-OP-054' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'baja'],
            'CMAN-OP-058' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-059' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-060' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-061' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'activo'],
            'CMAN-OP-062' => ['OPERACIONES', 'AYUDANTE TECNICO JR', 'baja'],
            'CMAN-OP-070' => ['OPERACIONES', 'AYUDANTE TECNICO SR', 'activo'],
            'CMAN-OP-074' => ['OPERACIONES', 'TECNICO ESPECIALISTA JR', 'baja'],
            'CMAN-OP-075' => ['OPERACIONES', 'AYUDANTE TECNICO SR', 'activo'],
            'CMAN-OP-076' => ['OPERACIONES', 'AYUDANTE TECNICO SR', 'baja'],
        ];
    }

    public function up(): void
    {
        $asignaciones = $this->asignaciones();

        DB::transaction(function () use ($asignaciones) {
            $areaId = DB::table('areas')
                ->where('nombre', 'Operativo')
                ->value('id');

            if (!$areaId) {
                throw new RuntimeException('No se encontró el área Operativo.');
            }

            $departamentos = DB::table('departamentos')
                ->where('area_id', $areaId)
                ->pluck('id', 'nombre');

            $estados = DB::table('estados_laborales')
                ->pluck('id', 'clave');

            foreach ($asignaciones as $employeeId => [$nombreDepartamento, $nombrePuesto, $claveEstado]) {
                $datosLaborales = DB::table('datos_laborales')
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$datosLaborales) {
                    throw new RuntimeException(
                        "No existe el employee_id {$employeeId} en datos_laborales."
                    );
                }

                $departamentoId = $departamentos->get($nombreDepartamento);

                if (!$departamentoId) {
                    throw new RuntimeException(
                        "No existe el departamento {$nombreDepartamento} en Operativo."
                    );
                }

                $puestoId = DB::table('puestos')
                    ->where('departamento_id', $departamentoId)
                    ->where('nombre', $nombrePuesto)
                    ->value('id');

                if (!$puestoId) {
                    throw new RuntimeException(
                        "No existe el puesto {$nombrePuesto} en {$nombreDepartamento}."
                    );
                }

                $estadoId = $estados->get($claveEstado);

                if (!$estadoId) {
                    throw new RuntimeException(
                        "No existe el estado laboral {$claveEstado}."
                    );
                }

                DB::table('datos_laborales')
                    ->where('id', $datosLaborales->id)
                    ->update([
                        'puesto_id' => $puestoId,
                        'estado_laboral_id' => $estadoId,
                    ]);
            }
        });
    }

    public function down(): void
    {
        $employeeIds = array_keys($this->asignaciones());

        $estadoActivoId = DB::table('estados_laborales')
            ->where('clave', 'activo')
            ->value('id');

        if (!$estadoActivoId) {
            return;
        }

        DB::table('datos_laborales')
            ->whereIn('employee_id', $employeeIds)
            ->update([
                'puesto_id' => null,
                'estado_laboral_id' => $estadoActivoId,
            ]);
    }
};