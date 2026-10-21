<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
            $table->dropColumn('motivo_anulacion');
        });

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->foreign('personal_id')
                ->references('id')
                ->on('personal')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
            $table->text('motivo_anulacion')->nullable();
        });

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->foreign('personal_id')
                ->references('id')
                ->on('personal')
                ->cascadeOnDelete();
        });
    }
};
