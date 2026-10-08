<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * ============================================================
         * SOLICITUDES DE MATERIAL
         * ============================================================
         *
         * Restauramos destino como texto libre y conservamos
         * la información que actualmente está relacionada mediante
         * destino_id.
         */

        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->text('destino')->nullable()->after('personal_id');
        });

        DB::statement('
            UPDATE solicitud_materiales sm
            INNER JOIN destinos d ON d.id = sm.destino_id
            SET sm.destino = d.nombre
            WHERE sm.destino_id IS NOT NULL
        ');

        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->dropForeign(['destino_id']);
            $table->dropColumn('destino_id');
        });


        /*
         * ============================================================
         * REQUISICIONES
         * ============================================================
         *
         * Restauramos el concepto de destino como texto libre.
         * El campo anterior era "embarcacion", pero ahora se utilizará
         * "destino" para permitir embarcaciones, bases, oficinas, etc.
         */

        Schema::table('requisiciones', function (Blueprint $table) {
            $table->string('destino')->nullable()->after('activo');
        });

        DB::statement('
            UPDATE requisiciones r
            INNER JOIN destinos d ON d.id = r.destino_id
            SET r.destino = d.nombre
            WHERE r.destino_id IS NOT NULL
        ');

        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropForeign(['destino_id']);
            $table->dropColumn('destino_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * ============================================================
         * REQUISICIONES
         * ============================================================
         */

        Schema::table('requisiciones', function (Blueprint $table) {
            $table->foreignId('destino_id')
                ->nullable()
                ->after('activo')
                ->constrained('destinos')
                ->nullOnDelete();
        });

        DB::statement('
            UPDATE requisiciones r
            INNER JOIN destinos d ON d.nombre = r.destino
            SET r.destino_id = d.id
            WHERE r.destino IS NOT NULL
        ');

        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropColumn('destino');
        });


        /*
         * ============================================================
         * SOLICITUDES DE MATERIAL
         * ============================================================
         */

        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->foreignId('destino_id')
                ->nullable()
                ->after('personal_id')
                ->constrained('destinos')
                ->nullOnDelete();
        });

        DB::statement('
            UPDATE solicitud_materiales sm
            INNER JOIN destinos d ON d.nombre = sm.destino
            SET sm.destino_id = d.id
            WHERE sm.destino IS NOT NULL
        ');

        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->dropColumn('destino');
        });
    }
};
