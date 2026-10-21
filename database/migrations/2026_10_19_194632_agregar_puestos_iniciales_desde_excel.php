<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $puestos = [
        ['departamento' => 'OPERACIONES', 'nombre' => 'AYUDANTE TECNICO JR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'AYUDANTE TECNICO SR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'OPERADOR ESPECIALISTA TECNICO A'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'OPERADOR ESPECIALISTA TECNICO B'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'TECNICO A SENIOR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'TECNICO B SENIOR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'TECNICO ESPECIALISTA JR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'TECNICO ESPECIALISTA SEMI SR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'TECNICO ESPECIALISTA SR'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'TRAINEE'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'AUXILIAR DE OPERACIONES'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'COORDINADOR DE OPERACIONES'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'SUPERVISOR DE OPERACIONES'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'INTENDENCIA'],
        ['departamento' => 'OPERACIONES', 'nombre' => 'PENDIENTE DE DEFINIR'],

        ['departamento' => 'RECURSOS HUMANOS', 'nombre' => 'COORDINADORA DE RH'],
        ['departamento' => 'RECURSOS HUMANOS', 'nombre' => 'RECURSOS HUMANOS'],

        ['departamento' => 'ESTIMACIONES', 'nombre' => 'COORDINADORA DE ESTIMACIONES'],
        ['departamento' => 'FINANZAS', 'nombre' => 'GERENTE ADMO. Y FINANZAS'],
        ['departamento' => 'DIRECCION', 'nombre' => 'DIRECTOR'],
        ['departamento' => 'DIRECCION', 'nombre' => 'JURIDICO'],

        ['departamento' => 'CONTABILIDAD', 'nombre' => 'ANALISTA CONTABLE'],
        ['departamento' => 'CONTABILIDAD', 'nombre' => 'AUXILIAR CONTABLE'],
        ['departamento' => 'CONTABILIDAD', 'nombre' => 'CONTABILIDAD'],
        ['departamento' => 'CONTABILIDAD', 'nombre' => 'CONTADOR'],

        ['departamento' => 'SEGURIDAD', 'nombre' => 'SUPERVISORA DE HSE'],

        ['departamento' => 'CALIDAD', 'nombre' => 'COORDINADOR DE CONTROL DE CALIDAD'],
        ['departamento' => 'CALIDAD', 'nombre' => 'SUPERVISOR DE CALIDAD'],

        ['departamento' => 'ALMACEN', 'nombre' => 'ALMACEN'],

        ['departamento' => 'TECNOLOGIAS DE LA INFORMACION', 'nombre' => 'AUXILIAR DE T.I'],
        ['departamento' => 'TECNOLOGIAS DE LA INFORMACION', 'nombre' => 'COORDINADOR DE T.I.'],
        ['departamento' => 'TECNOLOGIAS DE LA INFORMACION', 'nombre' => 'T.I.'],

        ['departamento' => 'LOGISTICA', 'nombre' => 'COORDINADOR DE LOGISTICA'],
        ['departamento' => 'LOGISTICA', 'nombre' => 'LOGISTICA'],
    ];

    public function up(): void
    {
        $areaId = DB::table('areas')
            ->where('nombre', 'Operativo')
            ->value('id');

        if (!$areaId) {
            throw new RuntimeException('No se encontró el área Operativo.');
        }

        foreach ($this->puestos as $puesto) {
            $departamentoId = DB::table('departamentos')
                ->where('area_id', $areaId)
                ->where('nombre', $puesto['departamento'])
                ->value('id');

            if (!$departamentoId) {
                throw new RuntimeException(
                    "No se encontró el departamento {$puesto['departamento']} en el área Operativo."
                );
            }

            $existe = DB::table('puestos')
                ->where('departamento_id', $departamentoId)
                ->where('nombre', $puesto['nombre'])
                ->exists();

            if (!$existe) {
                DB::table('puestos')->insert([
                    'departamento_id' => $departamentoId,
                    'nombre' => $puesto['nombre'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->puestos as $puesto) {
            $departamentoId = DB::table('departamentos')
                ->where('nombre', $puesto['departamento'])
                ->value('id');

            if (!$departamentoId) {
                continue;
            }

            DB::table('puestos')
                ->where('departamento_id', $departamentoId)
                ->where('nombre', $puesto['nombre'])
                ->whereNotIn('id', function ($query) {
                    $query->select('puesto_id')
                        ->from('datos_laborales')
                        ->whereNotNull('puesto_id');
                })
                ->delete();
        }
    }
};