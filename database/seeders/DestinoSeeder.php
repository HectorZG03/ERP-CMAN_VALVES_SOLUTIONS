<?php

namespace Database\Seeders;

use App\Models\Destino;
use Illuminate\Database\Seeder;

class DestinoSeeder extends Seeder
{
    /**
     * Ejecutar el seeder.
     */
    public function run(): void
    {
        $destinos = [
            'BMS Capitán América',
            'Base Operativa',
            'BMS Maya',
            'BMS Iron Horse',
            'BMS Grand Canyon',
            'BMS Ocean Intrepid',
            'BMS Stim Star',
        ];

        foreach ($destinos as $nombre) {
            Destino::firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}