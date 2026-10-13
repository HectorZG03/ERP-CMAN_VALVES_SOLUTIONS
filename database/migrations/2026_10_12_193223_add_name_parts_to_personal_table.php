<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $partes = [
            2  => ['HECTOR MANUEL', 'DE LA CRUZ', 'DEL ANGEL'],
            3  => ['FELIPE DEL JESUS', 'MENDOZA', 'MORALES'],
            4  => ['ASUNCION', 'JIMENEZ', 'ARIAS'],
            5  => ['FELIPE', 'SIERRA', 'SANCHEZ'],
            6  => ['CARLOS', 'DE LA TORRE', 'MANUEL'],
            7  => ['RICARDO', 'MONTEJO', 'GARCIA'],
            8  => ['FELIPE', 'HERNANDEZ', 'QUEZADA'],
            9  => ['CRISTHIAN', 'DOMINGUEZ', 'BOUCHOTL'],
            10 => ['ORLANDO', 'MORALES', 'JIMENEZ'],
            11 => ['JESUS', 'AREVALO', 'MENDOZA'],
            12 => ['CARLOS MARIO', 'MORENO', 'GARCIA'],
            13 => ['CARLOS REYNALDO', 'COCOM', 'VIDAL'],
            14 => ['FERNANDO', 'RAMON', 'GARCIA'],
            15 => ['JORGE LUIS', 'RAMIREZ', 'ALOR'],
            16 => ['JORGE ALBERTO', 'GARCIA', 'CHABLE'],
            17 => ['EDVAR GEOVANI', 'MISS', 'ACOSTA'],
            18 => ['DANIEL', 'CARRILLO', 'LOPEZ'],
            19 => ['MOISES ROMAN', 'NAVA', 'ROSAS'],
            20 => ['HECTOR ANDREI', 'ORTEGA', 'PEREZ'],
            21 => ['JOSE MANUEL', 'LIÑAN', 'ALVAREZ'],
            22 => ['MIGUEL ANGEL', 'CARDENAS', 'RODRIGUEZ'],
            23 => ['CANDELARIO', 'RUIZ', 'SALABARRIA'],
            24 => ['FERNANDO', 'SOLANO', 'GUTIERREZ'],
            25 => ['RAFAEL', 'AREVALO', 'MORENO'],
            26 => ['LUIS ALONSO', 'REYES', 'JIMENEZ'],
            27 => ['JUAN LUIS', 'SANCHEZ', 'FELIX'],
            28 => ['PEDRO', 'SANDOVAL', 'CENTENO'],
            30 => ['ERNESTO ALONSO', 'UC', 'GUTIERREZ'],
            31 => ['IVAN DE JESUS', 'MARTINEZ', 'REYES'],
            32 => ['FRANCISCO', 'MAGAÑA', 'FIGUEROA'],
            33 => ['JORGE EDUARDO', 'RIVERA', 'RODRIGUEZ'],
            34 => ['CARLOS ALBERTO', 'CHAVEZ', 'OZUNA'],
            35 => ['JOSE JAVIER', 'PERERA', 'DE LA CRUZ'],
            36 => ['NORMA ALICIA', 'SANCHEZ', 'DE LA CRUZ'],
            37 => ['MARIA JESUS', 'ZAPATA', 'VERTIZ'],
            38 => ['MANUEL', 'ESPINOSA', 'REYES'],
            39 => ['DAVID CONCEPCION', 'HERNANDEZ', 'CAMPOS'],
            40 => ['ANGEL OSVALDO', 'QUINTANA', 'PAIZ'],
            41 => ['ELIZABETH', 'MAZARIEGO', 'DIAZ'],
            42 => ['WENDY GABRIELA', 'VERRA', 'GARCIA'],
            43 => ['EMILY PAOLA', 'HERNANDEZ', 'SOTO'],
            44 => ['CINTHYA YESSENIA', 'HERNADEZ', 'CABRERA'],
            45 => ['GRIMALDO DE JESUS', 'RAMIREZ', 'ESCUDERO'],
            46 => ['RODRIGO ANTONIO', 'VAZQUEZ', 'PEREZ'],
        ];

        $totalRegistros = DB::table('personal')->count();

        if ($totalRegistros !== count($partes)) {
            throw new RuntimeException(
                'La cantidad de registros de personal no coincide con el mapeo esperado.'
            );
        }

        foreach (array_keys($partes) as $id) {
            if (! DB::table('personal')->where('id', $id)->exists()) {
                throw new RuntimeException(
                    "No se encontró el registro de personal con ID {$id}."
                );
            }
        }

        Schema::table('personal', function (Blueprint $table) {
            $table->string('nombre', 100)
                ->nullable()
                ->after('nombre_completo');

            $table->string('ape_pat', 100)
                ->nullable()
                ->after('nombre');

            $table->string('ape_mat', 100)
                ->nullable()
                ->after('ape_pat');
        });

        foreach ($partes as $id => [$nombre, $apePat, $apeMat]) {
            DB::table('personal')
                ->where('id', $id)
                ->update([
                    'nombre' => $nombre,
                    'ape_pat' => $apePat,
                    'ape_mat' => $apeMat,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'ape_pat', 'ape_mat']);
        });
    }
};