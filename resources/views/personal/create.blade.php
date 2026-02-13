@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nuevo Colaborador</h1>
        <a href="{{ route('personal.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('personal.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- SECCIÓN: INFORMACIÓN BÁSICA -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-blue-500">
                    📋 Información Básica
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Foto -->
                    <div class="md:col-span-2">
                        <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fotografía
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
                               value="{{ old('employee_id') }}"
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
                               value="{{ old('nombre_completo') }}"
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
                               value="{{ old('fecha_nacimiento') }}"
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
                               value="{{ old('edad') }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               placeholder="30">
                        @error('edad')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label for="sexo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sexo <span class="text-red-500">*</span>
                        </label>
                        <select name="sexo" id="sexo" required
                                class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar...</option>
                            <option value="Masculino" {{ old('sexo') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('sexo') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Otro" {{ old('sexo') === 'Otro' ? 'selected' : '' }}>Otro</option>
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
                               value="{{ old('nacionalidad') }}"
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
                            <option value="Soltero(a)" {{ old('estado_civil') === 'Soltero(a)' ? 'selected' : '' }}>Soltero(a)</option>
                            <option value="Casado(a)" {{ old('estado_civil') === 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                            <option value="Divorciado(a)" {{ old('estado_civil') === 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                            <option value="Viudo(a)" {{ old('estado_civil') === 'Viudo(a)' ? 'selected' : '' }}>Viudo(a)</option>
                            <option value="Unión Libre" {{ old('estado_civil') === 'Unión Libre' ? 'selected' : '' }}>Unión Libre</option>
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
                            <option value="A+" {{ old('grupo_sanguineo') === 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('grupo_sanguineo') === 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('grupo_sanguineo') === 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('grupo_sanguineo') === 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('grupo_sanguineo') === 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('grupo_sanguineo') === 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('grupo_sanguineo') === 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('grupo_sanguineo') === 'O-' ? 'selected' : '' }}>O-</option>
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
                                  placeholder="Especificar cualquier enfermedad o alergia relevante">{{ old('enfermedad_alergia') }}</textarea>
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
                               value="{{ old('curp') }}"
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
                               value="{{ old('rfc') }}"
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
                               value="{{ old('nss') }}"
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
                               value="{{ old('clave_interbancaria') }}"
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
                                  placeholder="Calle, Número, Colonia, Ciudad, Estado, CP">{{ old('direccion') }}</textarea>
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
                               value="{{ old('correo_electronico') }}"
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
                               value="{{ old('numero_telefonico') }}"
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
                               value="{{ old('nombre_contacto_emergencia') }}"
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
                               value="{{ old('numero_telefonico_emergencia') }}"
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
                               value="{{ old('area') }}"
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
                               value="{{ old('departamento') }}"
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
                               value="{{ old('grado') }}"
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
                               value="{{ old('fecha_ingreso', date('Y-m-d')) }}"
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
                                   value="{{ old('sueldo') }}"
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
                                   value="{{ old('bonos', '0') }}"
                                   class="w-full pl-7 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                                   placeholder="0.00">
                        </div>
                        @error('bonos')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg transition-colors duration-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200">Información</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            Los campos marcados con <span class="text-red-500">*</span> son obligatorios. 
                            Todos los demás campos son opcionales. Si no se llenan, se registrarán como "N/A" automáticamente.
                            El colaborador quedará con estatus "Activo".
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('personal.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Guardar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>
@endsection