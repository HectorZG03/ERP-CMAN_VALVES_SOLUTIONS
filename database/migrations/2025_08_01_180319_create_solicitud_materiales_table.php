<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('cantidad_solicitada');
            $table->text('destino')->nullable();
            $table->enum('estatus', ['pendiente', 'aprobado', 'denegado'])->default('pendiente');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_materiales');
    }
};