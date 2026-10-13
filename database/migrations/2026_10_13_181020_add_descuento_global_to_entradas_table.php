<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->decimal('descuento_global', 10, 2)
                ->default(0)
                ->after('precio_total');
        });
    }

    public function down(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('descuento_global');
        });
    }
};