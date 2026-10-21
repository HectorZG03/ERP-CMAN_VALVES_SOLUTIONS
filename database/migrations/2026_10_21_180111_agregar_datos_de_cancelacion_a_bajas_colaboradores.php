<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->timestamp('cancelada_en')
                ->nullable()
                ->after('motivo_baja');

            $table->text('motivo_cancelacion')
                ->nullable()
                ->after('cancelada_en');

            $table->foreignId('cancelada_por')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->dropForeign(['cancelada_por']);
            $table->dropColumn([
                'cancelada_por',
                'motivo_cancelacion',
                'cancelada_en',
            ]);
        });
    }
};