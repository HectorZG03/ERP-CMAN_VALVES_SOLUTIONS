<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cambios_puesto_sueldo');
    }

    public function down(): void
    {
        // Recrea la estructura adaptada, pero no recupera los registros eliminados.
        Schema::create('cambios_puesto_sueldo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('personal_id')
                ->constrained('personal')
                ->restrictOnDelete();

            $table->string('tipo_cambio', 30)->default('puesto_sueldo');
            $table->string('puesto_anterior')->nullable();
            $table->string('puesto_nuevo')->nullable();

            $table->foreignId('puesto_anterior_id')
                ->nullable()
                ->constrained('puestos')
                ->nullOnDelete();

            $table->foreignId('puesto_nuevo_id')
                ->nullable()
                ->constrained('puestos')
                ->nullOnDelete();

            $table->decimal('sueldo_anterior', 10, 2)->nullable();
            $table->decimal('sueldo_nuevo', 10, 2)->nullable();
            $table->date('fecha_cambio');
            $table->text('observaciones')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }
};