<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->foreignId('destino_id')
                ->nullable()
                ->after('destino')
                ->constrained('destinos')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('destino_id');
        });
    }
};