<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_personal', function (Blueprint $table) {
            $table->id();

            // Persona que recibirá el permiso, tenga o no cuenta ERP.
            $table->foreignId('personal_id')
                ->constrained('personal')
                ->restrictOnDelete();

            // Usuario que creó la solicitud: el solicitante o alguien de RH.
            $table->foreignId('registrado_por_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Usuario de Dirección que la aprobó o rechazó.
            $table->foreignId('resuelto_por_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('fecha_permiso');
            $table->decimal('horas_solicitadas', 5, 2);
            $table->text('motivo');

            $table->string('estatus', 20)
                ->default('pendiente')
                ->index();


            $table->foreignId('revisado_por_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('revisado_at')->nullable();
            $table->text('comentario_revision')->nullable();

            // Nulo hasta que Dirección apruebe y tome la decisión.
            $table->boolean('goce_sueldo')->nullable();

            $table->timestamp('resuelto_at')->nullable();
            $table->text('comentario_resolucion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_personal');
    }
};