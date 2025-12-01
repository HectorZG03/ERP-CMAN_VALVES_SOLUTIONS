<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_nombre');
            $table->string('contrato');
            $table->string('convenio');
            $table->timestamps();
        });

        // 🔗 Agregar relación a requisiciones
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropForeign(['contrato_id']);
            $table->dropColumn('contrato_id');
        });

        Schema::dropIfExists('contratos');
    }
};
