<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('producto');
            $table->string('economico')->nullable();
            $table->string('usuario_nombre');
            $table->string('tipo', 20);
            $table->integer('existencia_anterior');
            $table->integer('existencia_nueva');
            $table->integer('diferencia');
            $table->decimal('costo_promedio_anterior', 14, 4);
            $table->decimal('costo_unitario_ajuste', 14, 4)->nullable();
            $table->decimal('costo_promedio_nuevo', 14, 4);
            $table->decimal('valor_total_anterior', 14, 2);
            $table->decimal('valor_total_nuevo', 14, 2);
            $table->decimal('diferencia_valor', 14, 2);
            $table->text('motivo');
            $table->timestamps();

            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes_inventario');
    }
};
