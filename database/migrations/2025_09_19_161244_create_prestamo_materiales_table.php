<?php

// 1. Migración principal: create_prestamo_materiales_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamo_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_esperada');
            $table->date('fecha_devolucion_real')->nullable();
            $table->string('destino');
            $table->enum('estatus', ['pendiente', 'aprobado', 'prestado', 'devuelto', 'denegado'])->default('pendiente');
            $table->text('comentario')->nullable();
            $table->text('observaciones_devolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamo_materiales');
    }
};
