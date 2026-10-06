<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
     * Alias de variantes tipográficas / abreviaturas que deben
     * resolver a un destino canónico. Las claves deben estar YA
     * normalizadas (MAYÚSCULAS, sin acentos, espacios colapsados).
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
        'GRAN CANON'             => 'BMS Grand Canyon', // GRAN CAÑON normalizado
        'OCEAN INTREPID'         => 'BMS Ocean Intrepid',
        'OCIAN INTREPID'         => 'BMS Ocean Intrepid', // typo en BD
        'BMS OCEAN INTREPID'     => 'BMS Ocean Intrepid',
        'STIM STAR'              => 'BMS Stim Star',
        'BMS STIM STAR'          => 'BMS Stim Star',
        'ONEL A'                 => 'Onel A',
        'ONEL-A'                 => 'Onel A', // variante con guion
    ];

    /**
     * Destinos que son variantes conceptuales de un canónico y que,
     * tras el backfill, quedarán huérfanos. Se soft-deletean al final.
     */
    private const DESTINOS_HUERFANOS = [
        'ONEL-A',
        'GRAN CAÑON',
        'GRAN CANON',
        'Ocian Intrepid',
    ];

    public function up(): void
    {
        // 1) Agregar FK nullable a requisiciones
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->foreignId('destino_id')
                ->nullable()
                ->after('activo')
                ->constrained('destinos')
                ->nullOnDelete();
        });

        // 2) Asegurar catálogo canónico
        $this->sembrarDestinosCanonicos();

        // 3) Backfill desde embarcacion
        if (Schema::hasColumn('requisiciones', 'embarcacion')) {
            $this->backfillDesdeEmbarcacion();
        }

        // 4) Soft-deletear destinos huérfanos (creados por ejecuciones previas)
        $this->limpiarDestinosHuerfanos();

        // 5) Dropear embarcacion
        Schema::table('requisiciones', function (Blueprint $table) {
            if (Schema::hasColumn('requisiciones', 'embarcacion')) {
                $table->dropColumn('embarcacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            if (! Schema::hasColumn('requisiciones', 'embarcacion')) {
                $table->string('embarcacion', 255)
                    ->nullable()
                    ->after('plataforma');
            }
        });

        if (Schema::hasColumn('requisiciones', 'destino_id')) {
            DB::table('requisiciones')
                ->join(
                    'destinos',
                    'destinos.id',
                    '=',
                    'requisiciones.destino_id'
                )
                ->update([
                    'requisiciones.embarcacion' => DB::raw('destinos.nombre'),
                ]);

            Schema::table('requisiciones', function (Blueprint $table) {
                $table->dropConstrainedForeignId('destino_id');
            });
        }
    }

    private function sembrarDestinosCanonicos(): void
    {
        $ahora = now();

        foreach (self::DESTINOS_CANONICOS as $nombre) {
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

    private function backfillDesdeEmbarcacion(): void
    {
        $mapa = [];
        foreach (DB::table('destinos')->get(['id', 'nombre']) as $fila) {
            $mapa[$this->normalizar($fila->nombre)] = $fila->id;
        }

        $aliasMap = [];
        foreach (self::ALIAS as $alias => $canonico) {
            $claveCanonica = $this->normalizar($canonico);
            if (isset($mapa[$claveCanonica])) {
                $aliasMap[$alias] = $mapa[$claveCanonica];
            }
        }

        DB::table('requisiciones')
            ->select(['id', 'embarcacion'])
            ->whereNotNull('embarcacion')
            ->where('embarcacion', '!=', '')
            ->orderBy('id')
            ->chunk(500, function ($requisiciones) use (&$mapa, $aliasMap) {
                foreach ($requisiciones as $requisicion) {
                    $original = trim((string) $requisicion->embarcacion);
                    if ($original === '') {
                        continue;
                    }

                    $upper = mb_strtoupper($original, 'UTF-8');
                    if (in_array($upper, ['N/A', 'NA', 'NINGUNO'], true)) {
                        continue;
                    }

                    $normalizado = $this->normalizar($original);

                    $destinoId = $mapa[$normalizado]
                        ?? $aliasMap[$normalizado]
                        ?? null;

                    if (! $destinoId) {
                        $destinoId = DB::table('destinos')->insertGetId([
                            'nombre'     => $original,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);

                        $mapa[$normalizado] = $destinoId;
                    }

                    DB::table('requisiciones')
                        ->where('id', $requisicion->id)
                        ->update(['destino_id' => $destinoId]);
                }
            });
    }

    private function limpiarDestinosHuerfanos(): void
    {
        $ahora = now();

        foreach (self::DESTINOS_HUERFANOS as $nombre) {
            // Solo soft-deletea si NO tiene ningún uso en ninguna tabla
            $tieneUsos = DB::table('destinos')
                ->where('nombre', $nombre)
                ->where(function ($query) {
                    $query->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('solicitud_materiales')
                            ->whereColumn(
                                'solicitud_materiales.destino_id',
                                'destinos.id'
                            );
                    })->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('requisiciones')
                            ->whereColumn(
                                'requisiciones.destino_id',
                                'destinos.id'
                            );
                    });
                })
                ->exists();

            if ($tieneUsos) {
                continue;
            }

            DB::table('destinos')
                ->where('nombre', $nombre)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
        }
    }

    private function normalizar(?string $texto): string
    {
        $texto = trim((string) $texto);
        if ($texto === '') {
            return '';
        }

        $texto = preg_replace('/\s+/u', ' ', $texto);

        if (function_exists('iconv')) {
            $convertido = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
            if ($convertido !== false) {
                $texto = $convertido;
            }
        }

        return mb_strtoupper($texto, 'UTF-8');
    }
};