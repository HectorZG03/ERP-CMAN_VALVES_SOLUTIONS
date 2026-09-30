<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo canónico inicial.
     * Debe coincidir con DestinosSeeder::DESTINOS.
     */
    private const DESTINOS_CANONICOS = [
        'BMS Capitán América',
        'Base Operativa',
        'BMS Maya',
        'BMS Iron Horse',
        'BMS Grand Canyon',
        'BMS Ocean Intrepid',
        'BMS Stim Star',
        'Onel A',
    ];

    /**
     * Alias conocidos de variantes tipográficas que deben
     * resolver a un destino canónico. Las claves se expresan
     * en formato normalizado (MAYÚSCULAS, sin acentos).
     */
    private const ALIAS = [
        'CAPITAN AMERICA'        => 'BMS Capitán América',
        'BMS CAPITAN AMERICA'    => 'BMS Capitán América',
        'BASE OPERATIVA'         => 'Base Operativa',
        'MAYA'                   => 'BMS Maya',
        'BMS MAYA'               => 'BMS Maya',
        'IRON HORSE'             => 'BMS Iron Horse',
        'BMS IRON HORSE'         => 'BMS Iron Horse',
        'GRAND CANYON'           => 'BMS Grand Canyon',
        'BMS GRAND CANYON'       => 'BMS Grand Canyon',
        'OCEAN INTREPID'         => 'BMS Ocean Intrepid',
        'BMS OCEAN INTREPID'     => 'BMS Ocean Intrepid',
        'STIM STAR'              => 'BMS Stim Star',
        'BMS STIM STAR'          => 'BMS Stim Star',
        'ONEL A'                 => 'Onel A',
    ];

    public function up(): void
    {
        // 1) Agregar la FK nullable
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->foreignId('destino_id')
                ->nullable()
                ->after('personal_id')
                ->constrained('destinos')
                ->nullOnDelete();
        });

        // 2) Asegurar catálogo canónico ANTES del backfill
        $this->sembrarDestinosCanonicos();

        // 3) Backfill: texto libre -> destino_id
        $this->backfillDesdeColumnaDestino();

        // 4) ⚠️ OPCIONAL: comenta este bloque si quieres
        //    dejar la columna vieja como fallback temporal
        //    hasta que la Tanda 2 esté completa.
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            if (Schema::hasColumn('solicitud_materiales', 'destino')) {
                $table->dropColumn('destino');
            }
        });
    }

    public function down(): void
    {
        // Recrear la columna vieja
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            if (! Schema::hasColumn('solicitud_materiales', 'destino')) {
                $table->text('destino')->nullable()->after('personal_id');
            }
        });

        // Best-effort: reconstruir el texto a partir de la FK
        if (Schema::hasColumn('solicitud_materiales', 'destino_id')) {
            DB::table('solicitud_materiales')
                ->join(
                    'destinos',
                    'destinos.id',
                    '=',
                    'solicitud_materiales.destino_id'
                )
                ->update([
                    'solicitud_materiales.destino' => DB::raw('destinos.nombre'),
                ]);

            Schema::table('solicitud_materiales', function (Blueprint $table) {
                $table->dropConstrainedForeignId('destino_id');
            });
        }
    }

    private function sembrarDestinosCanonicos(): void
    {
        $ahora = now();

        foreach (self::DESTINOS_CANONICOS as $nombre) {
            // updateOrInsert es idempotente y no filtra soft-deleted,
            // por lo que revive registros previamente eliminados.
            DB::table('destinos')->updateOrInsert(
                ['nombre' => $nombre],
                [
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                    'deleted_at' => null,
                ]
            );
        }
    }

    private function backfillDesdeColumnaDestino(): void
    {
        // Mapa: nombre normalizado -> id (incluye soft-deleted,
        // porque los necesitamos para asignar la FK aunque estén "borrados")
        $mapa = [];
        foreach (DB::table('destinos')->get(['id', 'nombre']) as $fila) {
            $mapa[$this->normalizar($fila->nombre)] = $fila->id;
        }

        // Mapa de alias resueltos a id real
        $aliasMap = [];
        foreach (self::ALIAS as $alias => $canonico) {
            $claveCanonica = $this->normalizar($canonico);
            if (isset($mapa[$claveCanonica])) {
                $aliasMap[$alias] = $mapa[$claveCanonica];
            }
        }

        DB::table('solicitud_materiales')
            ->select(['id', 'destino'])
            ->whereNotNull('destino')
            ->where('destino', '!=', '')
            ->orderBy('id')
            ->chunk(500, function ($solicitudes) use (&$mapa, $aliasMap) {
                foreach ($solicitudes as $solicitud) {
                    $original = trim((string) $solicitud->destino);
                    if ($original === '') {
                        continue;
                    }

                    // Ignorar placeholders históricos
                    $upper = mb_strtoupper($original, 'UTF-8');
                    if (in_array($upper, ['N/A', 'NA', 'NINGUNO'], true)) {
                        continue;
                    }

                    $normalizado = $this->normalizar($original);

                    $destinoId = $mapa[$normalizado]
                        ?? $aliasMap[$normalizado]
                        ?? null;

                    // Si no hay match: crear destino nuevo preservando el texto
                    if (! $destinoId) {
                        $destinoId = DB::table('destinos')->insertGetId([
                            'nombre'     => $original,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);

                        $mapa[$normalizado] = $destinoId;
                    }

                    DB::table('solicitud_materiales')
                        ->where('id', $solicitud->id)
                        ->update(['destino_id' => $destinoId]);
                }
            });
    }

    private function normalizar(?string $texto): string
    {
        $texto = trim((string) $texto);
        if ($texto === '') {
            return '';
        }

        // Colapsar espacios internos
        $texto = preg_replace('/\s+/u', ' ', $texto);

        // Quitar acentos si es posible
        if (function_exists('iconv')) {
            $convertido = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
            if ($convertido !== false) {
                $texto = $convertido;
            }
        }

        return mb_strtoupper($texto, 'UTF-8');
    }
};