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
        Schema::table('valepp', function (Blueprint $table) {
            $table->foreignId('solicitud_material_id')
                ->nullable()
                ->after('id')
                ->constrained('solicitud_materiales')
                ->nullOnDelete();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::table('valepp', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'solicitud_material_id'
            );
        });
    }
};