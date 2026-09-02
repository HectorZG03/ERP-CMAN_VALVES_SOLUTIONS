<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->foreignId('solicitud_material_id')
                ->nullable()
                ->after('id')
                ->constrained('solicitud_materiales')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->dropForeign(['solicitud_material_id']);
            $table->dropColumn('solicitud_material_id');
        });
    }
};