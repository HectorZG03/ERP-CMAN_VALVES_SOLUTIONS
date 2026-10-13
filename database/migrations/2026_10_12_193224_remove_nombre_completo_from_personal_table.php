<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('personal', 'nombre_completo')) {
            Schema::table('personal', function (Blueprint $table) {
                $table->dropColumn('nombre_completo');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('personal', 'nombre_completo')) {
            Schema::table('personal', function (Blueprint $table) {
                $table->string('nombre_completo')->nullable()->after('ape_mat');
            });
        }

        DB::table('personal')->update([
            'nombre_completo' => DB::raw(
                "TRIM(CONCAT_WS(' ', NULLIF(TRIM(nombre), ''), NULLIF(TRIM(ape_pat), ''), NULLIF(TRIM(ape_mat), '')))"
            ),
        ]);
    }
};