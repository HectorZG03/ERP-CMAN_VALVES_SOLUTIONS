@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('personal.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Personal
            </a>
            
            @if($personal->foto && $personal->foto !== 'N/A')
            <img src="{{ asset('storage/' . $personal->foto) }}" alt="Foto" class="h-16 w-16 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600">
            @else
            <div class="h-16 w-16 bg-blue-500 dark:bg-blue-600 rounded-full flex items-center justify-center border-2 border-gray-300 dark:border-gray-600">
                <span class="text-2xl font-bold text-white">
                    {{ substr($personal->nombre_completo, 0, 2) }}
                </span>
            </div>
            @endif

            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $personal->nombre_completo }}
                </h1>
                @if($personal->employee_id && $personal->employee_id !== 'N/A')
                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $personal->employee_id }}</p>
                @endif
            </div>
        </div>
        
        <div>
            @if($personal->estatus === 'activo')
                <span class="px-3 py-2 text-sm font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                    Activo
                </span>
            @else
                <span class="px-3 py-2 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                    Baja
                </span>
            @endif
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- SECCIÓN: INFORMACIÓN PERSONAL -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-green-500">
                        👤 Datos Personales
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($personal->fecha_nacimiento && $personal->fecha_nacimiento !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha de Nacimiento</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->fecha_nacimiento->format('d/m/Y') }}</dd>
                        </div>
                        @endif

                        @if($personal->edad && $personal->edad !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Edad</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->edad }} años</dd>
                        </div>
                        @endif

                        @if($personal->sexo && $personal->sexo !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sexo</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->sexo }}</dd>
                        </div>
                        @endif

                        @if($personal->nacionalidad && $personal->nacionalidad !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nacionalidad</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->nacionalidad }}</dd>
                        </div>
                        @endif

                        @if($personal->estado_civil && $personal->estado_civil !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado Civil</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->estado_civil }}</dd>
                        </div>
                        @endif

                        @if($personal->grupo_sanguineo && $personal->grupo_sanguineo !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Grupo Sanguíneo</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->grupo_sanguineo }}</dd>
                        </div>
                        @endif
                    </div>

                    @if($personal->enfermedad_alergia && $personal->enfermedad_alergia !== 'N/A')
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <dt class="text-xs font-medium text-red-700 dark:text-red-400 uppercase mb-1">⚕️ Enfermedades/Alergias</dt>
                        <dd class="text-sm text-red-900 dark:text-red-200">{{ $personal->enfermedad_alergia }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- SECCIÓN: DOCUMENTOS OFICIALES -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-purple-500">
                        📄 Documentos Oficiales
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($personal->curp && $personal->curp !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CURP</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $personal->curp }}</dd>
                        </div>
                        @endif

                        @if($personal->rfc && $personal->rfc !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">RFC</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $personal->rfc }}</dd>
                        </div>
                        @endif

                        @if($personal->nss && $personal->nss !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NSS</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $personal->nss }}</dd>
                        </div>
                        @endif

                        @if($personal->clave_interbancaria && $personal->clave_interbancaria !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CLABE Interbancaria</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $personal->clave_interbancaria }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: INFORMACIÓN DE CONTACTO -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-orange-500">
                        📞 Información de Contacto
                    </h3>
                    
                    @if($personal->direccion && $personal->direccion !== 'N/A')
                    <div class="mb-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dirección</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->direccion }}</dd>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($personal->correo_electronico && $personal->correo_electronico !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Correo Electrónico</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                <a href="mailto:{{ $personal->correo_electronico }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $personal->correo_electronico }}
                                </a>
                            </dd>
                        </div>
                        @endif

                        @if($personal->numero_telefonico && $personal->numero_telefonico !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Teléfono</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                <a href="tel:{{ $personal->numero_telefonico }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $personal->numero_telefonico }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: CONTACTO DE EMERGENCIA -->
            @if(($personal->nombre_contacto_emergencia && $personal->nombre_contacto_emergencia !== 'N/A') || 
                ($personal->numero_telefonico_emergencia && $personal->numero_telefonico_emergencia !== 'N/A'))
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-red-500">
                        🚨 Contacto de Emergencia
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($personal->nombre_contacto_emergencia && $personal->nombre_contacto_emergencia !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->nombre_contacto_emergencia }}</dd>
                        </div>
                        @endif

                        @if($personal->numero_telefonico_emergencia && $personal->numero_telefonico_emergencia !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Teléfono</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                <a href="tel:{{ $personal->numero_telefonico_emergencia }}" class="text-red-600 dark:text-red-400 hover:underline">
                                    {{ $personal->numero_telefonico_emergencia }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- SECCIÓN: INFORMACIÓN LABORAL -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-indigo-500">
                        💼 Información Laboral
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Área</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->area }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Departamento</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->departamento }}</dd>
                        </div>
                        @if($personal->grado && $personal->grado !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Grado/Puesto</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->grado }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Ingreso</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $personal->fecha_ingreso->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Antigüedad</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $personal->fecha_ingreso->diffForHumans(now(), true) }}
                            </dd>
                        </div>
                        @if($personal->sueldo && $personal->sueldo !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sueldo</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($personal->sueldo, 2) }}</dd>
                        </div>
                        @endif
                        @if($personal->bonos && $personal->bonos !== 'N/A' && $personal->bonos > 0)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Bonos</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($personal->bonos, 2) }}</dd>
                        </div>
                        @endif

                        @if($personal->division && $personal->division !== 'N/A')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">División</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($personal->division) }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: HISTORIAL DE VALES EPP -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Vales de EPP
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                            {{ $personal->valepp->count() }} vale(s)
                        </span>
                    </div>
                    
                    @if($personal->valepp->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vale</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Materiales</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($personal->valepp as $vale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $vale->numero_vale }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $vale->fecha_solicitud->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        @foreach($vale->detalles as $detalle)
                                            <div>{{ $detalle->inventario->nombre_producto }} ({{ $detalle->cantidad }})</div>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('valepp.show', $vale) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hay vales de EPP registrados</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="space-y-6">
            <!-- INFORMACIÓN DEL REGISTRO -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        Información del Registro
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                @if($personal->estatus === 'activo')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Baja
                                    </span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $personal->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Actualizado el</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $personal->updated_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- HISTORIAL DE CAMBIOS -->
            @if($personal->cambiosPuestoSueldo->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Cambios Recientes
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300">
                            {{ $personal->cambiosPuestoSueldo->count() }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($personal->cambiosPuestoSueldo->take(3) as $cambio)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="text-xs font-medium text-gray-900 dark:text-white">
                                {{ $cambio->fecha_cambio->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $cambio->puesto_anterior }} → {{ $cambio->puesto_nuevo }}
                            </div>
                        </div>
                        @endforeach
                        
                        @if($personal->cambiosPuestoSueldo->count() > 3)
                        <a href="{{ route('cambios.historial', $personal->id) }}" 
                           class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                            Ver historial completo ({{ $personal->cambiosPuestoSueldo->count() }})
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- HISTORIAL DE BAJAS -->
            @if($personal->bajas->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Bajas Registradas
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            {{ $personal->bajas->count() }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($personal->bajas->take(2) as $baja)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="text-xs font-medium text-gray-900 dark:text-white">
                                {{ $baja->fecha_baja->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                {{ Str::limit($baja->motivo_baja, 50) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- ACCIONES -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        Acciones
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('personal.edit', $personal) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Colaborador
                        </a>

                        @if($personal->estatus === 'activo')
                        <a href="{{ route('bajas.create') }}?personal_id={{ $personal->id }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            Registrar Baja
                        </a>

                        <a href="{{ route('cambios.create') }}?personal_id={{ $personal->id }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            Registrar Cambio
                        </a>
                        @endif

                        <a href="{{ route('valepp.create') }}?personal_id={{ $personal->id }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Crear Vale de EPP
                        </a>

                        <form method="POST" action="{{ route('personal.destroy', $personal) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors duration-200"
                                    onclick="return confirm('¿Eliminar este colaborador? Esta acción no se puede deshacer.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Colaborador
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection