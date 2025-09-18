<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_barcos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_barco');
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_barcos');
    }
};