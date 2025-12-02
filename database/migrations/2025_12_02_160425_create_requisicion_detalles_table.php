<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla de detalles de requisiciones
        Schema::create('requisicion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisicion_id')->constrained('requisiciones')->onDelete('cascade');
            $table->integer('cantidad');
            $table->string('unidad');
            $table->string('material');
            $table->timestamps();
        });

        // Modificar tabla principal para quitar campos que ahora van en detalles
        Schema::table('requisiciones', function (Blueprint $table) {
            if (Schema::hasColumn('requisiciones', 'cantidad')) {
                $table->dropColumn('cantidad');
            }

            if (Schema::hasColumn('requisiciones', 'unidad')) {
                $table->dropColumn('unidad');
            }

            if (Schema::hasColumn('requisiciones', 'material')) {
                $table->dropColumn('material');
            }
        });
    }

    public function down(): void
    {
        // Restaurar estructura original
        Schema::table('requisiciones', function (Blueprint $table) {
            if (!Schema::hasColumn('requisiciones', 'cantidad')) {
                $table->integer('cantidad');
            }

            if (!Schema::hasColumn('requisiciones', 'unidad')) {
                $table->string('unidad');
            }

            if (!Schema::hasColumn('requisiciones', 'material')) {
                $table->string('material');
            }
        });

        Schema::dropIfExists('requisicion_detalles');
    }
};