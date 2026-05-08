<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_compras', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique(); // Folio autogenerado, ej: OC-2024-0001
            $table->string('nombre_proveedor');
            $table->string('razon_social_proveedor');
            $table->string('rfc_proveedor');
            $table->string('direccion_proveedor')->nullable();
            $table->string('telefono_proveedor')->nullable();
            $table->string('email_proveedor')->nullable();
            $table->decimal('envio', 10, 2)->default(0);
            $table->decimal('otros', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);  // suma de todos los totales de artículos
            $table->decimal('iva', 10, 2)->default(0);       // 16% del subtotal
            $table->decimal('total_general', 10, 2)->default(0); // subtotal + iva + envio + otros
            $table->text('comentarios')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_compras');
    }
};