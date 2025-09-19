<?php

// 2. Migración de detalles: create_prestamo_material_detalles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamo_material_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_material_id')->constrained('prestamo_materiales')->onDelete('cascade');
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->integer('cantidad_prestada');
            $table->integer('cantidad_devuelta')->default(0);
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->enum('estado_devolucion', ['prestado', 'devuelto_completo', 'devuelto_parcial', 'pendiente'])->default('pendiente');
            $table->text('condicion_devolucion')->nullable(); // bueno, dañado, perdido, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamo_material_detalles');
    }
};