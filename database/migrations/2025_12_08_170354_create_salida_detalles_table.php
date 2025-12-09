<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salida_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salida_id')->constrained('salidas')->onDelete('cascade');
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('precio_total', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total_con_iva', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salida_detalles');
    }
};