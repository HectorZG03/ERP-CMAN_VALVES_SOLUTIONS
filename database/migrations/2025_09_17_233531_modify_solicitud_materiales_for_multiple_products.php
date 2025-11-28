<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla de detalles
        Schema::create('solicitud_material_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_material_id')->constrained('solicitud_materiales')->onDelete('cascade');
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->integer('cantidad_solicitada');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->timestamps();
        });

        // Modificar tabla principal
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            if (Schema::hasColumn('solicitud_materiales', 'inventario_id')) {
                $table->dropForeign(['inventario_id']);
                $table->dropColumn('inventario_id');
            }

            if (Schema::hasColumn('solicitud_materiales', 'cantidad_solicitada')) {
                $table->dropColumn('cantidad_solicitada');
            }
        });
    }

    public function down(): void
    {
        // Restaurar estructura original
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitud_materiales', 'inventario_id')) {
                $table->foreignId('inventario_id')->constrained('inventarios');
            }

            if (!Schema::hasColumn('solicitud_materiales', 'cantidad_solicitada')) {
                $table->integer('cantidad_solicitada');
            }
        });

        Schema::dropIfExists('solicitud_material_detalles');
    }
};
