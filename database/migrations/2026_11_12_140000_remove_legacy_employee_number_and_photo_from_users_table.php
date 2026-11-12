<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo comprueba datos: no corrige números, asigna vínculos ni mueve fotos.
        $cuentas = DB::table('users as u')
            ->leftJoin('personal as p', 'p.id', '=', 'u.personal_id')
            ->leftJoin('datos_laborales as dl', 'dl.personal_id', '=', 'p.id')
            ->select([
                'u.id', 'u.personal_id', 'u.num_empleado', 'u.profile_photo',
                'p.id as expediente_id', 'p.foto', 'dl.employee_id',
            ])
            ->get();

        $inconsistencias = [];
        $normalizar = static function (?string $valor): ?string {
            $valor = trim((string) $valor);

            return $valor === '' || strtoupper($valor) === 'N/A' ? null : $valor;
        };

        foreach ($cuentas as $cuenta) {
            $numeroAnterior = $normalizar($cuenta->num_empleado);
            $numeroLaboral = $normalizar($cuenta->employee_id);
            $fotoAnterior = $normalizar($cuenta->profile_photo);
            $fotoExpediente = $normalizar($cuenta->foto);

            if ($cuenta->personal_id === null) {
                if ($numeroAnterior !== null || $fotoAnterior !== null) {
                    $inconsistencias[] = "Usuario {$cuenta->id}: tiene datos antiguos y no está vinculado a un expediente. Completa la vinculación desde TI.";
                }

                continue;
            }

            if ($cuenta->expediente_id === null) {
                $inconsistencias[] = "Usuario {$cuenta->id}: el expediente vinculado no existe.";

                continue;
            }

            // Un número antiguo distinto no impide la transición: el vínculo
            // explícito de TI determina qué expediente es la fuente vigente.
            if ($numeroAnterior !== null && $numeroLaboral === null) {
                $inconsistencias[] = "Usuario {$cuenta->id}: el expediente vinculado no tiene número laboral registrado.";
            }

            if ($fotoAnterior !== null && $fotoExpediente === null) {
                $inconsistencias[] = "Usuario {$cuenta->id}: hay una foto anterior sin fotografía registrada en el expediente.";
            }
        }

        if ($inconsistencias !== []) {
            throw new RuntimeException(
                "No se retiraron los campos antiguos de users. Revisa desde TI/RH:\n".
                implode("\n", $inconsistencias)
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['num_empleado', 'profile_photo']);
        });
    }

    public function down(): void
    {
        // Restaura la estructura, no los valores eliminados. Para recuperar
        // dichos valores debe utilizarse el respaldo previo a la migración.
        Schema::table('users', function (Blueprint $table) {
            $table->string('num_empleado')->nullable();
            $table->string('profile_photo')->nullable();
        });
    }
};
