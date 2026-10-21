<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });

        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')
                ->constrained('areas')
                ->restrictOnDelete();

            $table->string('nombre', 150);
            $table->timestamps();

            $table->unique(['area_id', 'nombre']);
        });

        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')
                ->constrained('departamentos')
                ->restrictOnDelete();

            $table->string('nombre', 150);
            $table->timestamps();

            $table->unique(['departamento_id', 'nombre']);
        });

        Schema::create('estados_laborales', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->string('nombre', 100);
            $table->boolean('es_final')->default(false);
            $table->timestamps();
        });

        Schema::create('datos_laborales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('personal_id')
                ->unique()
                ->constrained('personal')
                ->restrictOnDelete();

            // Se deja nullable durante la etapa de migración de datos.
            $table->foreignId('puesto_id')
                ->nullable()
                ->constrained('puestos')
                ->restrictOnDelete();

            $table->foreignId('estado_laboral_id')
                ->nullable()
                ->constrained('estados_laborales')
                ->restrictOnDelete();

            // Identificador laboral; la unicidad se definirá
            // después de limpiar los valores heredados.
            $table->string('employee_id', 50)->nullable();

            // Fecha de ingreso original, no fecha de reingreso.
            $table->date('fecha_ingreso')->nullable();

            $table->decimal('sueldo', 10, 2)->nullable();

            $table->timestamps();
        });

        $ahora = now();

        DB::table('areas')->insert([
            [
                'nombre' => 'Operativa',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Administrativa',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ]);

        DB::table('estados_laborales')->insert([
            [
                'clave' => 'activo',
                'nombre' => 'Activo',
                'es_final' => false,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'clave' => 'baja',
                'nombre' => 'Baja',
                'es_final' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'clave' => 'suspendido',
                'nombre' => 'Suspendido',
                'es_final' => false,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_laborales');
        Schema::dropIfExists('puestos');
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('estados_laborales');
        Schema::dropIfExists('areas');
    }
};