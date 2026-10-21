<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id',
                'sueldo',
                'division',
                'area',
                'departamento',
                'grado',
                'fecha_ingreso',
                'estatus',
                'bonos',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->decimal('sueldo', 10, 2)->nullable();

            $table->enum('division', ['Operativa', 'Administrativa'])
                ->default('Operativa');

            $table->string('employee_id', 255)->nullable();

            $table->string('area', 255)->nullable();
            $table->string('departamento', 255)->nullable();
            $table->string('grado', 255)->nullable();
            $table->string('fecha_ingreso', 255)->nullable();

            $table->enum('estatus', ['activo', 'baja'])
                ->default('activo');

            $table->string('bonos', 255)->nullable();
        });
    }
};