<?php

namespace Database\Seeders;

use App\Models\DatosLaborales;
use App\Models\EstadoLaboral;
use App\Models\Personal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PersonalExcelSeeder extends Seeder
{
    public function run(): void
    {
        $trabajadores = [
            // Nuevos del listado OP
            ['employee_id' => 'CMAN-OP-001', 'nombre' => 'JOSE LUIS. A.', 'ape_pat' => 'KOYOC', 'ape_mat' => 'PAT'],
            ['employee_id' => 'CMAN-OP-002', 'nombre' => 'GABRIEL ENRIQUE', 'ape_pat' => 'ARREDONDO', 'ape_mat' => 'VALDES'],
            ['employee_id' => 'CMAN-OP-003', 'nombre' => 'JOSE DEL CARMEN', 'ape_pat' => 'DE LA CRUZ', 'ape_mat' => 'ROLDAN'],
            ['employee_id' => 'CMAN-OP-005', 'nombre' => 'GILBERTO', 'ape_pat' => 'POOT', 'ape_mat' => 'RUFINO'],
            ['employee_id' => 'CMAN-OP-006', 'nombre' => 'ROGER', 'ape_pat' => 'SANCHEZ', 'ape_mat' => 'DE LA CRUZ'],
            ['employee_id' => 'CMAN-OP-010', 'nombre' => 'RAMON', 'ape_pat' => 'LOPEZ', 'ape_mat' => 'VEGA'],
            ['employee_id' => 'CMAN-OP-011', 'nombre' => 'JUAN ANTONIO', 'ape_pat' => 'VENTURA', 'ape_mat' => 'BAUTISTA'],
            ['employee_id' => 'CMAN-OP-016', 'nombre' => 'ALEJANDRO', 'ape_pat' => 'RAMOS', 'ape_mat' => 'AGUILAR'],
            ['employee_id' => 'CMAN-OP-018', 'nombre' => 'EDER DAVID', 'ape_pat' => 'LARA', 'ape_mat' => 'ZAPOT'],
            ['employee_id' => 'CMAN-OP-019', 'nombre' => 'FELIPE ROMAN', 'ape_pat' => 'CHAN', 'ape_mat' => 'HERRERA'],
            ['employee_id' => 'CMAN-OP-021', 'nombre' => 'ANTONINO', 'ape_pat' => 'DILIEGROS', 'ape_mat' => 'OCEJO'],
            ['employee_id' => 'CMAN-OP-024', 'nombre' => 'MARCOS ALEXIS', 'ape_pat' => 'AVALOS', 'ape_mat' => 'CORDOVA'],
            ['employee_id' => 'CMAN-OP-025', 'nombre' => 'EDDY WILLIAMS', 'ape_pat' => 'DELGADO', 'ape_mat' => 'HERRERA'],
            ['employee_id' => 'CMAN-OP-026', 'nombre' => 'JUAN CARLOS', 'ape_pat' => 'LOPEZ', 'ape_mat' => 'ORUETA'],
            ['employee_id' => 'CMAN-OP-027', 'nombre' => 'EDUARDO ALEJANDRO', 'ape_pat' => 'CRUZ', 'ape_mat' => null],
            ['employee_id' => 'CMAN-OP-028', 'nombre' => 'JOSE MANUEL', 'ape_pat' => 'TORREZ', 'ape_mat' => 'MARTINEZ'],
            ['employee_id' => 'CMAN-OP-030', 'nombre' => 'ISSAC ALEXANDER', 'ape_pat' => 'LOPEZ', 'ape_mat' => 'GUZMAN'],
            ['employee_id' => 'CMAN-OP-031', 'nombre' => 'SOL NAYELI', 'ape_pat' => 'TOTO', 'ape_mat' => 'LOPEZ'],
            ['employee_id' => 'CMAN-OP-033', 'nombre' => 'FREDY ALFONSO', 'ape_pat' => 'GARCIA', 'ape_mat' => 'TORCUATO'],
            ['employee_id' => 'CMAN-OP-038', 'nombre' => 'JIMMY', 'ape_pat' => 'MENDEZ', 'ape_mat' => 'GIL'],
            ['employee_id' => 'CMAN-OP-039', 'nombre' => 'RENE ALEJANDRO', 'ape_pat' => 'MALDONADO', 'ape_mat' => 'SALAS'],
            ['employee_id' => 'CMAN-OP-041', 'nombre' => 'CRUZ ANGEL', 'ape_pat' => 'VALENCIA', 'ape_mat' => 'VELAZQUEZ'],
            ['employee_id' => 'CMAN-OP-042', 'nombre' => 'WILLIAMS', 'ape_pat' => 'RODRIGUEZ', 'ape_mat' => 'JUAREZ'],
            ['employee_id' => 'CMAN-OP-043', 'nombre' => 'JESUS ENRIQUE', 'ape_pat' => 'SANTIAGO', 'ape_mat' => 'RAMOS'],
            ['employee_id' => 'CMAN-OP-044', 'nombre' => 'RAMON SANTIAGO', 'ape_pat' => 'HERNANDEZ', 'ape_mat' => 'DZIB'],
            ['employee_id' => 'CMAN-OP-045', 'nombre' => 'ALONDRA', 'ape_pat' => 'MARTINEZ', 'ape_mat' => 'CORDOVA'],
            ['employee_id' => 'CMAN-OP-046', 'nombre' => 'CRISTIAN TOMAS', 'ape_pat' => 'GOMEZ', 'ape_mat' => 'MONTEJO'],
            ['employee_id' => 'CMAN-OP-049', 'nombre' => 'JESUS CONRADO', 'ape_pat' => 'CHE', 'ape_mat' => 'JIMENEZ'],
            ['employee_id' => 'CMAN-OP-050', 'nombre' => 'MIGUEL ANGEL', 'ape_pat' => 'HERNANDEZ', 'ape_mat' => 'ROMERO'],
            ['employee_id' => 'CMAN-OP-055', 'nombre' => 'JULIAN', 'ape_pat' => 'MAGAÑA', 'ape_mat' => 'HERNANDEZ'],
            ['employee_id' => 'CMAN-OP-056', 'nombre' => 'ALEX MARIO', 'ape_pat' => 'JIMENEZ', 'ape_mat' => 'AGUILAR'],
            ['employee_id' => 'CMAN-OP-057', 'nombre' => 'ROBERTO', 'ape_pat' => 'MORALES', 'ape_mat' => 'PEREZ'],
            ['employee_id' => 'CMAN-OP-063', 'nombre' => 'MANUEL ANTONIO', 'ape_pat' => 'PEREZ', 'ape_mat' => 'VASCONCELOS'],
            ['employee_id' => 'CMAN-OP-067', 'nombre' => 'CELESTINO', 'ape_pat' => 'BALCAZAR', 'ape_mat' => 'DE LA CRUZ'],
            ['employee_id' => 'CMAN-OP-068', 'nombre' => 'ROBERTO', 'ape_pat' => 'GARCIA', 'ape_mat' => 'BAUTISTA'],

            // Nuevos del listado ADM
            ['employee_id' => 'CMAN-ADM-002', 'nombre' => 'IGNACIO ANTONIO', 'ape_pat' => 'RODRIGUEZ', 'ape_mat' => 'RAMON'],
            ['employee_id' => 'CMAN-ADM-006', 'nombre' => 'ONIKY', 'ape_pat' => 'GONZALEZ', 'ape_mat' => 'LOPEZ'],
            ['employee_id' => 'CMAN-ADM-009', 'nombre' => 'VICTOR', 'ape_pat' => 'RUIZ', 'ape_mat' => 'MENDEZ'],
            ['employee_id' => 'CMAN-ADM-011', 'nombre' => 'RAFAEL', 'ape_pat' => 'LARA', 'ape_mat' => 'AGUILERA'],
            ['employee_id' => 'CMAN-ADM-013', 'nombre' => 'JULIAN ELIAS', 'ape_pat' => 'HECHEM', 'ape_mat' => 'ZAPIEN'],
            ['employee_id' => 'CMAN-ADM-014', 'nombre' => 'OMAR', 'ape_pat' => 'ALCARAZ', 'ape_mat' => 'ROMERO'],
            ['employee_id' => 'CMAN-ADM-017', 'nombre' => 'ANA CRISTEL', 'ape_pat' => 'RAMOS', 'ape_mat' => 'JAVIER'],
            ['employee_id' => 'CMAN-ADM-018', 'nombre' => 'KEVIN KLISSMAN', 'ape_pat' => 'ALFARO', 'ape_mat' => 'MANTILLA'],
            ['employee_id' => 'CMAN-ADM-020', 'nombre' => 'CARLOS', 'ape_pat' => 'IZQUIERDO', 'ape_mat' => 'MARQUEZ'],
            ['employee_id' => 'CMAN-ADM-022', 'nombre' => 'ANA GABRIELA', 'ape_pat' => 'ABREU', 'ape_mat' => 'MARTINEZ'],
            ['employee_id' => 'CMAN-ADM-030', 'nombre' => 'HECTOR ALEXIS GERONIMO', 'ape_pat' => 'ZEPETA', 'ape_mat' => null],
        ];

        $estadoActivoId = EstadoLaboral::query()
            ->where('clave', 'activo')
            ->value('id');

        if (!$estadoActivoId) {
            throw new RuntimeException(
                'No existe el estado laboral "activo". Verifica que los estados laborales estén cargados.'
            );
        }

        $insertados = 0;
        $omitidos = 0;

        DB::transaction(function () use (
            $trabajadores,
            $estadoActivoId,
            &$insertados,
            &$omitidos
        ): void {
            $personasExistentes = Personal::query()
                ->with('datosLaborales')
                ->get(['id', 'nombre', 'ape_pat', 'ape_mat']);

            $personasPorNombre = [];

            foreach ($personasExistentes as $persona) {
                $personasPorNombre[$this->normalizarNombre(
                    $persona->nombre,
                    $persona->ape_pat,
                    $persona->ape_mat
                )] = $persona;
            }

            foreach ($trabajadores as $trabajador) {
                $claveNombre = $this->normalizarNombre(
                    $trabajador['nombre'],
                    $trabajador['ape_pat'],
                    $trabajador['ape_mat']
                );

                $employeeIdExistente = DatosLaborales::query()
                    ->with('personal')
                    ->where('employee_id', $trabajador['employee_id'])
                    ->first();

                if ($employeeIdExistente) {
                    $personalRelacionado = $employeeIdExistente->personal;

                    if (
                        !$personalRelacionado ||
                        $this->normalizarNombre(
                            $personalRelacionado->nombre,
                            $personalRelacionado->ape_pat,
                            $personalRelacionado->ape_mat
                        ) !== $claveNombre
                    ) {
                        throw new RuntimeException(
                            "El employee_id {$trabajador['employee_id']} ya está asociado a otra persona."
                        );
                    }

                    $omitidos++;
                    continue;
                }

                if (isset($personasPorNombre[$claveNombre])) {
                    // Conserva el registro y el ID que ya existan.
                    $omitidos++;
                    continue;
                }

                $personal = Personal::query()->create([
                    'nombre' => $trabajador['nombre'],
                    'ape_pat' => $trabajador['ape_pat'],
                    'ape_mat' => $trabajador['ape_mat'],
                ]);

                DatosLaborales::query()->create([
                    'personal_id' => $personal->id,
                    'puesto_id' => null,
                    'estado_laboral_id' => $estadoActivoId,
                    'employee_id' => $trabajador['employee_id'],
                    'fecha_ingreso' => null,
                    'sueldo' => null,
                ]);

                $personasPorNombre[$claveNombre] = $personal;
                $insertados++;
            }
        });

        $this->command?->info("Personal agregado: {$insertados}");
        $this->command?->info("Registros omitidos por existir previamente: {$omitidos}");
    }

    private function normalizarNombre(?string $nombre, ?string $apePat, ?string $apeMat): string
    {
        $nombreCompleto = implode(' ', array_filter(
            [$nombre, $apePat, $apeMat],
            fn ($parte) => filled($parte)
        ));

        $nombreCompleto = Str::ascii(mb_strtoupper($nombreCompleto, 'UTF-8'));
        $nombreCompleto = preg_replace('/[^A-Z0-9]+/', ' ', $nombreCompleto);

        return trim(preg_replace('/\s+/', ' ', $nombreCompleto));
    }
}