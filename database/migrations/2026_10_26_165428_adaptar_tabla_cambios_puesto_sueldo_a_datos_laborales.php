<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cambios_puesto_sueldo', function (Blueprint $table) {
            // Las filas existentes se consideran cambios de puesto y sueldo.
            $table->string('tipo_cambio', 30)
                ->default('puesto_sueldo')
                ->after('personal_id');

            // Referencias al catálogo nuevo. Los nombres de texto existentes
            // se conservan como fotografía histórica del movimiento.
            $table->foreignId('puesto_anterior_id')
                ->nullable()
                ->after('puesto_anterior')
                ->constrained('puestos')
                ->nullOnDelete();

            $table->foreignId('puesto_nuevo_id')
                ->nullable()
                ->after('puesto_nuevo')
                ->constrained('puestos')
                ->nullOnDelete();

            // Permite registrar solamente un cambio de puesto o solamente sueldo.
            $table->string('puesto_anterior')->nullable()->change();
            $table->string('puesto_nuevo')->nullable()->change();
            $table->decimal('sueldo_anterior', 10, 2)->nullable()->change();
            $table->decimal('sueldo_nuevo', 10, 2)->nullable()->change();

            // Conserva el historial al eliminar personal.
            $table->dropForeign(['personal_id']);
            $table->foreign('personal_id')
                ->references('id')
                ->on('personal')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cambios_puesto_sueldo', function (Blueprint $table) {
            $table->dropForeign(['puesto_anterior_id']);
            $table->dropForeign(['puesto_nuevo_id']);

            $table->dropColumn([
                'puesto_anterior_id',
                'puesto_nuevo_id',
                'tipo_cambio',
            ]);

            // El esquema anterior exigía estos valores.
            // El rollback puede fallar si ya se guardaron cambios parciales
            // con valores NULL; en ese caso, no ejecutes el rollback sin revisar
            // y respaldar primero esos registros.
            $table->string('puesto_anterior')->nullable(false)->change();
            $table->string('puesto_nuevo')->nullable(false)->change();
            $table->decimal('sueldo_anterior', 10, 2)->nullable(false)->change();
            $table->decimal('sueldo_nuevo', 10, 2)->nullable(false)->change();

            $table->dropForeign(['personal_id']);
            $table->foreign('personal_id')
                ->references('id')
                ->on('personal')
                ->cascadeOnDelete();
        });
    }
};