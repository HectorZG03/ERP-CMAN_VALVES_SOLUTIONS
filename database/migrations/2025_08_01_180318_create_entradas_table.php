<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique()->nullable();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->text('observaciones')->nullable();
            $table->date('fecha_entrada');
            
            // Campos calculados automáticamente
            $table->integer('cantidad_total')->default(0);
            $table->decimal('precio_unitario_promedio', 10, 2)->default(0);
            $table->decimal('precio_total', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total_con_iva', 10, 2)->default(0);
            
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};