<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Copia el estado existente antes de eliminar cancelada_en.
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->enum('estado', ['activa', 'anulada'])
                ->default('activa')
                ->after('motivo_baja');
        });

        DB::table('bajas_colaboradores')
            ->whereNotNull('cancelada_en')
            ->update(['estado' => 'anulada']);

        // Quita la llave foránea antes de renombrar la columna.
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->dropForeign(['cancelada_por']);
        });

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->renameColumn('motivo_cancelacion', 'motivo_anulacion');
            $table->renameColumn('cancelada_por', 'anulada_por');
            $table->dropColumn('cancelada_en');
        });

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->foreign('anulada_por')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->dropForeign(['anulada_por']);
        });

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->timestamp('cancelada_en')->nullable();
        });

        // Al revertir, reconstruye una fecha para las bajas que estaban anuladas.
        DB::table('bajas_colaboradores')
            ->where('estado', 'anulada')
            ->update(['cancelada_en' => DB::raw('updated_at')]);

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->renameColumn('anulada_por', 'cancelada_por');
            $table->renameColumn('motivo_anulacion', 'motivo_cancelacion');
            $table->dropColumn('estado');
        });

        Schema::table('bajas_colaboradores', function (Blueprint $table) {
            $table->foreign('cancelada_por')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
