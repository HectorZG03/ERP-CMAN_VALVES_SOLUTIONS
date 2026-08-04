<?php

namespace Database\Seeders;

use App\Models\Embarcacion;
use Illuminate\Database\Seeder;

class EmbarcacionSeeder extends Seeder
{
    /**
     * Ejecutar el seeder.
     */
    public function run(): void
    {
        $embarcaciones = [
            'BMS Capitán América',
            'Base Operativa',
            'BMS Maya',
            'BMS Iron Horse',
            'BMS Grand Canyon',
            'BMS Ocean Intrepid',
            'BMS Stim Star',
        ];

        foreach ($embarcaciones as $nombre) {
            Embarcacion::firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}