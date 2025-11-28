<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique()->nullable(); // ✅ Agregar nullable
            $table->string('nombre_solicitante');
            $table->string('departamento');
            $table->string('proyecto');
            $table->string('sit');
            $table->string('partida');
            $table->string('plataforma');
            $table->string('area');
            $table->string('activo');
            $table->string('contrato');
            $table->string('combenio');
            $table->string('embarcacion');
            $table->integer('cantidad');
            $table->string('unidad');
            $table->string('material');
            $table->enum('tipo_requerimiento', ['interno', 'externo']);
            $table->text('comentario');
            $table->enum('estatus', ['pendiente', 'aprobado', 'denegado'])->default('pendiente');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisiciones');
    }
};