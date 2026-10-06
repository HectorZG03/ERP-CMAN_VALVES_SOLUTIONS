<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->string('tipo_solicitud', 20)
                ->default('estandar')
                ->after('categoria')
                ->index();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->dropIndex(['tipo_solicitud']);
            $table->dropColumn('tipo_solicitud');
        });
    }
};