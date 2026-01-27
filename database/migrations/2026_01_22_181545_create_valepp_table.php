<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valepp', function (Blueprint $table) {
            $table->id();
            $table->string('numero_vale')->unique();
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->date('fecha_solicitud');
            $table->enum('estatus', ['pendiente', 'aprobado', 'entregado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valepp');
    }
};