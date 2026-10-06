<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('valepp', function (Blueprint $table) {
            if (Schema::hasColumn('valepp', 'embarcacion')) {
                $table->dropColumn('embarcacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('valepp', function (Blueprint $table) {
            if (! Schema::hasColumn('valepp', 'embarcacion')) {
                // La columna original no tenía longitud declarada,
                // Laravel la creó como VARCHAR(255) implícitamente.
                $table->string('embarcacion', 255)
                    ->nullable()
                    ->after('observaciones');
            }
        });
    }
}; 