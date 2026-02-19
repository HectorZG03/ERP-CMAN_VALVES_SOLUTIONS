<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->foreignId('personal_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('personal')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitud_materiales', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
            $table->dropColumn('personal_id');
        });
    }
};