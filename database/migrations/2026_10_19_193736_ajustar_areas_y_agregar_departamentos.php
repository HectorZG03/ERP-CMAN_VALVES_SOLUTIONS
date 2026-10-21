<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $departamentos = [
        'OPERACIONES',
        'RECURSOS HUMANOS',
        'ESTIMACIONES',
        'FINANZAS',
        'DIRECCION',
        'CONTABILIDAD',
        'SEGURIDAD',
        'CALIDAD',
        'ALMACEN',
        'TECNOLOGIAS DE LA INFORMACION',
        'OPERATIVO',
        'LOGISTICA',
    ];

    public function up(): void
    {
        DB::table('areas')
            ->where('nombre', 'Operativa')
            ->update([
                'nombre' => 'Operativo',
                'updated_at' => now(),
            ]);

        DB::table('areas')
            ->where('nombre', 'Administrativa')
            ->update([
                'nombre' => 'Administrativo',
                'updated_at' => now(),
            ]);

        $areaOperativoId = DB::table('areas')
            ->where('nombre', 'Operativo')
            ->value('id');

        if (!$areaOperativoId) {
            throw new RuntimeException(
                'No se encontró el área Operativo en la tabla areas.'
            );
        }

        foreach ($this->departamentos as $nombre) {
            $existe = DB::table('departamentos')
                ->where('area_id', $areaOperativoId)
                ->where('nombre', $nombre)
                ->exists();

            if (!$existe) {
                DB::table('departamentos')->insert([
                    'area_id' => $areaOperativoId,
                    'nombre' => $nombre,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $areaOperativoId = DB::table('areas')
            ->where('nombre', 'Operativo')
            ->value('id');

        if ($areaOperativoId) {
            DB::table('departamentos')
                ->where('area_id', $areaOperativoId)
                ->whereIn('nombre', $this->departamentos)
                ->delete();
        }

        DB::table('areas')
            ->where('nombre', 'Operativo')
            ->update([
                'nombre' => 'Operativa',
                'updated_at' => now(),
            ]);

        DB::table('areas')
            ->where('nombre', 'Administrativo')
            ->update([
                'nombre' => 'Administrativa',
                'updated_at' => now(),
            ]);
    }
};