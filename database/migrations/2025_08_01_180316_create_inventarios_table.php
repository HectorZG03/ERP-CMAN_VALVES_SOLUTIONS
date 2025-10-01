<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('categoria');
            $table->string('nombre_producto');
            $table->string('economico');
            $table->string('medida');
            $table->integer('existencia')->default(0);
            $table->decimal('precio_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};