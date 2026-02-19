@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Colaborador</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $personal->nombre_completo }}</p>
        </div>
        <a href="{{ route('personal.show', $personal) }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('personal.update', $personal) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- SECCIÓN: INFORMACIÓN BÁSICA -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-blue-500">
                    📋 Información Básica
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Foto Actual -->
                    @if($personal->foto && $personal->foto !== 'N/A')
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Foto Actual
                        </label>
                        <img src="{{ asset('storage/' . $personal->foto) }}" alt="Foto" class="h-32 w-32 object-cover rounded-lg">
                    </div>
                    @endif

                    <!-- Nueva Foto -->
                    <div class="md:col-span-2">
                        <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $personal->foto && $personal->foto !== 'N/A' ? 'Cambiar Fotografía' : 'Fotografía' }}
                        </label>
                        <input type="file" name="foto" id="foto" accept="image/*"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        @error('foto')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ID Empleado -->
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ID de Empleado
                        </label>
                        <input type="text" name="employee_id" id="employee_id"
                               value="{{ old('employee_id', $personal->employee_id) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="CMAN-AMD-001">
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre Completo -->
                    <div>
                        <label for="nombre_completo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre_completo" id="nombre_completo" required
                               value="{{ old('nombre_completo', $personal->nombre_completo) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Juan Pérez García">
                        @error('nombre_completo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: DATOS PERSONALES -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-green-500">
                    👤 Datos Personales
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha de Nacimiento
                        </label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                               value="{{ old('fecha_nacimiento', $personal->fecha_nacimiento ? $personal->fecha_nacimiento->format('Y-m-d') : '') }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        @error('fecha_nacimiento')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Edad -->
                    <div>
                        <label for="edad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Edad
                        </label>
                        <input type="number" name="edad" id="edad" min="18" max="100"
                               value="{{ old('edad', $personal->edad) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="30">
                        @error('edad')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label for="sexo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sexo
                        </label>
                        <select name="sexo" id="sexo"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar...</option>
                            <option value="Masculino" {{ old('sexo', $personal->sexo) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('sexo', $personal->sexo) === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Otro" {{ old('sexo', $personal->sexo) === 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('sexo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nacionalidad -->
                    <div>
                        <label for="nacionalidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nacionalidad
                        </label>
                        <input type="text" name="nacionalidad" id="nacionalidad"
                               value="{{ old('nacionalidad', $personal->nacionalidad) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Mexicana">
                        @error('nacionalidad')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado Civil -->
                    <div>
                        <label for="estado_civil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Estado Civil
                        </label>
                        <select name="estado_civil" id="estado_civil"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar...</option>
                            <option value="Soltero(a)" {{ old('estado_civil', $personal->estado_civil) === 'Soltero(a)' ? 'selected' : '' }}>Soltero(a)</option>
                            <option value="Casado(a)" {{ old('estado_civil', $personal->estado_civil) === 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                            <option value="Divorciado(a)" {{ old('estado_civil', $personal->estado_civil) === 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                            <option value="Viudo(a)" {{ old('estado_civil', $personal->estado_civil) === 'Viudo(a)' ? 'selected' : '' }}>Viudo(a)</option>
                            <option value="Unión Libre" {{ old('estado_civil', $personal->estado_civil) === 'Unión Libre' ? 'selected' : '' }}>Unión Libre</option>
                        </select>
                        @error('estado_civil')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grupo Sanguíneo -->
                    <div>
                        <label for="grupo_sanguineo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Grupo Sanguíneo
                        </label>
                        <select name="grupo_sanguineo" id="grupo_sanguineo"
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar...</option>
                            <option value="A+" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('grupo_sanguineo', $personal->grupo_sanguineo) === 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('grupo_sanguineo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enfermedad/Alergia -->
                    <div class="md:col-span-3">
                        <label for="enfermedad_alergia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Enfermedades / Alergias
                        </label>
                        <textarea name="enfermedad_alergia" id="enfermedad_alergia" rows="3"
                                  class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                                  placeholder="Especificar cualquier enfermedad o alergia relevante">{{ old('enfermedad_alergia', $personal->enfermedad_alergia) }}</textarea>
                        @error('enfermedad_alergia')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: DOCUMENTOS OFICIALES -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-purple-500">
                    📄 Documentos Oficiales
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- CURP -->
                    <div>
                        <label for="curp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            CURP
                        </label>
                        <input type="text" name="curp" id="curp" maxlength="18"
                               value="{{ old('curp', $personal->curp) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="AAAA000000AAAAAA00">
                        @error('curp')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RFC -->
                    <div>
                        <label for="rfc" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            RFC
                        </label>
                        <input type="text" name="rfc" id="rfc" maxlength="13"
                               value="{{ old('rfc', $personal->rfc) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="AAAA000000AAA">
                        @error('rfc')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NSS -->
                    <div>
                        <label for="nss" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            NSS (Número de Seguro Social)
                        </label>
                        <input type="text" name="nss" id="nss" maxlength="11"
                               value="{{ old('nss', $personal->nss) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="00000000000">
                        @error('nss')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Clave Interbancaria -->
                    <div>
                        <label for="clave_interbancaria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            CLABE Interbancaria
                        </label>
                        <input type="text" name="clave_interbancaria" id="clave_interbancaria" maxlength="18"
                               value="{{ old('clave_interbancaria', $personal->clave_interbancaria) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="000000000000000000">
                        @error('clave_interbancaria')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: CONTACTO -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-orange-500">
                    📞 Información de Contacto
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dirección Completa
                        </label>
                        <textarea name="direccion" id="direccion" rows="2"
                                  class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                                  placeholder="Calle, Número, Colonia, Ciudad, Estado, CP">{{ old('direccion', $personal->direccion) }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Correo Electrónico -->
                    <div>
                        <label for="correo_electronico" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Correo Electrónico
                        </label>
                        <input type="email" name="correo_electronico" id="correo_electronico"
                               value="{{ old('correo_electronico', $personal->correo_electronico) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="correo@ejemplo.com">
                        @error('correo_electronico')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Número Telefónico -->
                    <div>
                        <label for="numero_telefonico" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Número Telefónico
                        </label>
                        <input type="tel" name="numero_telefonico" id="numero_telefonico"
                               value="{{ old('numero_telefonico', $personal->numero_telefonico) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="(000) 000-0000">
                        @error('numero_telefonico')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: CONTACTO DE EMERGENCIA -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-red-500">
                    🚨 Contacto de Emergencia
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre Contacto Emergencia -->
                    <div>
                        <label for="nombre_contacto_emergencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre Completo
                        </label>
                        <input type="text" name="nombre_contacto_emergencia" id="nombre_contacto_emergencia"
                               value="{{ old('nombre_contacto_emergencia', $personal->nombre_contacto_emergencia) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="María García López">
                        @error('nombre_contacto_emergencia')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono Emergencia -->
                    <div>
                        <label for="numero_telefonico_emergencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teléfono de Emergencia
                        </label>
                        <input type="tel" name="numero_telefonico_emergencia" id="numero_telefonico_emergencia"
                               value="{{ old('numero_telefonico_emergencia', $personal->numero_telefonico_emergencia) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="(000) 000-0000">
                        @error('numero_telefonico_emergencia')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: INFORMACIÓN LABORAL -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-indigo-500">
                    💼 Información Laboral
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Área -->
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Área <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="area" id="area" required
                               value="{{ old('area', $personal->area) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Recursos Humanos">
                        @error('area')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Departamento <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="departamento" id="departamento" required
                               value="{{ old('departamento', $personal->departamento) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Administración">
                        @error('departamento')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grado/Puesto -->
                    <div>
                        <label for="grado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Grado / Puesto
                        </label>
                        <input type="text" name="grado" id="grado"
                               value="{{ old('grado', $personal->grado) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="Coordinador">
                        @error('grado')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Ingreso -->
                    <div>
                        <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_ingreso" id="fecha_ingreso" required
                               value="{{ old('fecha_ingreso', $personal->fecha_ingreso->format('Y-m-d')) }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                        @error('fecha_ingreso')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sueldo -->
                    <div>
                        <label for="sueldo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sueldo
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">$</span>
                            <input type="number" step="0.01" name="sueldo" id="sueldo"
                                   value="{{ old('sueldo', $personal->sueldo) }}"
                                   class="w-full pl-7 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                                   placeholder="15000.00">
                        </div>
                        @error('sueldo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bonos -->
                    <div>
                        <label for="bonos" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Bonos
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">$</span>
                            <input type="number" step="0.01" name="bonos" id="bonos"
                                   value="{{ old('bonos', $personal->bonos) }}"
                                   class="w-full pl-7 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                                   placeholder="0.00">
                        </div>
                        @error('bonos')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- División -->
                    <!-- División -->
                    <div>
                        <label for="division" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            División
                        </label>

                        <select name="division" id="division"
                            class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">

                            <option value="">Seleccionar...</option>

                            <option value="Operativa"
                                {{ old('division', $personal->division) === 'Operativa' ? 'selected' : '' }}>
                                Operativa
                            </option>

                            <option value="Administrativa"
                                {{ old('division', $personal->division) === 'Administrativa' ? 'selected' : '' }}>
                                Administrativa
                            </option>

                        </select>

                        @error('division')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Estatus -->
                    <div class="md:col-span-2">
                        <label for="estatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Estatus <span class="text-red-500">*</span>
                        </label>
                        <select name="estatus" id="estatus" required
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="activo" {{ old('estatus', $personal->estatus) === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="baja" {{ old('estatus', $personal->estatus) === 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                        @error('estatus')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-200">Importante</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Cambiar el estatus a "Baja" solo actualiza el estado del colaborador. 
                            Para registrar una baja formal con motivo y fecha específica, debes usar la sección de "Bajas".
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('personal.show', $personal) }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Actualizar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>
@endsection