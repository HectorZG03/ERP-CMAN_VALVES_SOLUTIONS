<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('personal_id')
                ->nullable()
                ->unique()
                ->after('num_empleado')
                ->constrained('personal')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
            $table->dropUnique(['personal_id']);
            $table->dropColumn('personal_id');
        });
    }
};