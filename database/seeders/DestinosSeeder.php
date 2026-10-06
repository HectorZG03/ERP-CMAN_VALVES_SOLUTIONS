<?php

namespace Database\Seeders;

use App\Models\Destino;
use Illuminate\Database\Seeder;

class DestinosSeeder extends Seeder
{
    /**
     * Catálogo canónico. Debe coincidir con los arreglos
     * DESTINOS_CANONICOS de las migraciones de backfill.
     */
    public const DESTINOS = [
        'BMS Capitán América',
        'Base Operativa',
        'BMS Maya',
        'BMS Iron Horse',
        'BMS Grand Canyon',
        'BMS Ocean Intrepid',
        'BMS Stim Star',
        'Onel A',
    ];

    public function run(): void
    {
        foreach (self::DESTINOS as $nombre) {
            // withTrashed(): si existía y estaba soft-deleted, se restaura.
            Destino::withTrashed()->updateOrCreate(
                ['nombre' => $nombre],
                ['deleted_at' => null]
            );
        }
    }
}