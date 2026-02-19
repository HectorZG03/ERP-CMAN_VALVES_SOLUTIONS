<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('nombre_completo')->nullable();
            $table->decimal('sueldo',10,2)->nullable();

            // Dividion (Operativa o Administrativa)
            $table->enum('division', ['Operativa','Administrativa'])->default('Operativa');

            // Información Personal
            $table->string('foto')->nullable();
            $table->string('employee_id')->nullable(); // CMAN-AMD-001
            $table->integer('edad')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['Masculino','Femenino','Otro','N/A'])->default('N/A');
            $table->string('estado_civil')->nullable();
            $table->string('grupo_sanguineo')->nullable();

            // Documentos Oficiales
            $table->string('curp')->nullable();
            $table->string('rfc')->nullable();
            $table->string('nss')->nullable();
            $table->string('clave_interbancaria')->nullable();

            // are area

            $table->string('area')->nullable();
            $table->string('departamento')->nullable();
            $table->string('grado')->nullable();
            $table->string('fecha_ingreso')->nullable();
            
            $table->enum('estatus', ['activo', 'baja'])->default('activo');

            // Contacto
            $table->text('direccion')->nullable();
            $table->string('correo_electronico')->nullable();
            $table->string('numero_telefonico')->nullable();

            // Emergencia
            $table->string('nombre_contacto_emergencia')->nullable();
            $table->string('numero_telefonico_emergencia')->nullable();

            // Laboral
            $table->string('bonos')->nullable();
            $table->text('enfermedad_alergia')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
