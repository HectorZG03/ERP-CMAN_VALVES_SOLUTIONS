<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', [
                'ti', 'aux_ti', 'direccion', 'almacen', 'aux_almacen',
                'aux_calidad', 'aux_contabilidad', 'aux_estimaciones', 
                'aux_finanzas', 'aux_logistica', 'aux_rh',
                'calidad', 'contabilidad', 'estimaciones', 'finanzas', 
                'logistica', 'rh', 'operaciones', 'aux_operaciones'
            ])->default('aux_almacen');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};