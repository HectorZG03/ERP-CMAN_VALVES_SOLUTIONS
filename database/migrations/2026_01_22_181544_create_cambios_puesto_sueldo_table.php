<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cambios_puesto_sueldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->string('puesto_anterior');
            $table->string('puesto_nuevo');
            $table->decimal('sueldo_anterior', 10, 2);
            $table->decimal('sueldo_nuevo', 10, 2);
            $table->date('fecha_cambio');
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cambios_puesto_sueldo');
    }
};