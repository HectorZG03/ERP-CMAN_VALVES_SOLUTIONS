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
        Schema::table('valepp_detalles', function (Blueprint $table) {
            $table->foreignId('solicitud_material_detalle_id')
                ->nullable()
                ->after('valepp_id')
                ->constrained('solicitud_material_detalles')
                ->nullOnDelete();

            $table->unique(
                [
                    'valepp_id',
                    'solicitud_material_detalle_id',
                ],
                'valepp_detalle_solicitud_unique'
            );
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('valepp_detalles', function (Blueprint $table) {
            $table->dropUnique(
                'valepp_detalle_solicitud_unique'
            );

            $table->dropConstrainedForeignId(
                'solicitud_material_detalle_id'
            );
        });
    }
};