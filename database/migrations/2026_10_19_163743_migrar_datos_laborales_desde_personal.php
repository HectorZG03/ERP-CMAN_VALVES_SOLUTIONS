<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $estados = DB::table('estados_laborales')
            ->pluck('id', 'clave');

        DB::transaction(function () use ($estados) {
            DB::table('personal')
                ->orderBy('id')
                ->chunk(100, function ($personas) use ($estados) {
                    foreach ($personas as $persona) {
                        $claveEstado = strtolower(trim(
                            (string) ($persona->estatus ?? '')
                        ));

                        $estadoId = $estados[$claveEstado] ?? null;

                        if ($estadoId === null) {
                            throw new RuntimeException(
                                "No hay un estado laboral configurado para el registro de personal ID {$persona->id}."
                            );
                        }

                        DB::table('datos_laborales')->insertOrIgnore([
                            'personal_id' => $persona->id,
                            'puesto_id' => null,
                            'estado_laboral_id' => $estadoId,
                            'employee_id' => $this->normalizarEmployeeId(
                                $persona->employee_id
                            ),
                            'fecha_ingreso' => $this->normalizarFechaIngreso(
                                $persona->fecha_ingreso,
                                $persona->id
                            ),
                            'sueldo' => $persona->sueldo,
                            'created_at' => $persona->created_at ?? now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        });
    }

    private function normalizarEmployeeId(?string $employeeId): ?string
    {
        $employeeId = trim((string) $employeeId);

        if (
            $employeeId === ''
            || in_array(strtoupper($employeeId), ['N/A', 'NA', 'NULL'], true)
        ) {
            return null;
        }

        return $employeeId;
    }

    private function normalizarFechaIngreso(
        ?string $fecha,
        int $personalId
    ): ?string {
        $fecha = trim((string) $fecha);

        if (
            $fecha === ''
            || in_array(strtoupper($fecha), ['N/A', 'NA', 'NULL'], true)
        ) {
            return null;
        }

        $formatos = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
        ];

        foreach ($formatos as $formato) {
            $fechaConvertida = DateTimeImmutable::createFromFormat(
                '!' . $formato,
                $fecha
            );

            $errores = DateTimeImmutable::getLastErrors();

            if (
                $fechaConvertida !== false
                && (
                    $errores === false
                    || (
                        $errores['warning_count'] === 0
                        && $errores['error_count'] === 0
                    )
                )
            ) {
                return $fechaConvertida->format('Y-m-d');
            }
        }

        throw new RuntimeException(
            "La fecha de ingreso del registro de personal ID {$personalId} no tiene un formato válido."
        );
    }

    public function down(): void
    {
        // No se borran los datos migrados durante el rollback.
        // Las columnas originales siguen siendo la fuente de respaldo.
    }
};