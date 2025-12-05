<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->enum('estatus_finanzas', ['pendiente', 'aprobado', 'denegado'])
                  ->default('pendiente')
                  ->after('estatus');
            $table->foreignId('aprobado_por_finanzas_id')
                  ->nullable()
                  ->constrained('users')
                  ->after('estatus_finanzas');
            $table->timestamp('fecha_aprobacion_finanzas')
                  ->nullable()
                  ->after('aprobado_por_finanzas_id');
        });
    }

    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropForeign(['aprobado_por_finanzas_id']);
            $table->dropColumn([
                'estatus_finanzas',
                'aprobado_por_finanzas_id',
                'fecha_aprobacion_finanzas'
            ]);
        });
    }
};