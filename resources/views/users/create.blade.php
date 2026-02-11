@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Crear Usuario</h1>
            <p class="mt-1 text-sm text-gray-600">Completa el formulario para registrar un nuevo usuario</p>
        </div>
        <a href="{{ route('users.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Información Personal y Fotos -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Información Personal e Imágenes
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Foto de Perfil -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto de Perfil (Opcional)
                        </label>
                        <div class="flex flex-col items-center">
                            <div class="w-40 h-40 bg-gray-200 rounded-full overflow-hidden border-4 border-white shadow-lg mb-3">
                                <img id="profile_preview" src="{{ asset('images/default-avatar.png') }}" 
                                     alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <label class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <input type="file" name="profile_photo" id="profile_photo" 
                                       accept="image/*" class="hidden" onchange="previewImage(this, 'profile_preview')">
                                Subir Foto
                            </label>
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG o GIF (Max. 2MB)</p>
                        </div>
                    </div>

                    <!-- Firma -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Firma Digital (Opcional)
                        </label>
                        <div class="flex flex-col items-center">
                            <div class="w-full h-40 bg-gray-100 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center mb-3">
                                <img id="signature_preview" src="" alt="Firma" 
                                     class="hidden max-w-full max-h-full object-contain">
                                <span id="signature_placeholder" class="text-gray-400 text-sm">Sin firma</span>
                            </div>
                            <label class="cursor-pointer bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <input type="file" name="signature" id="signature" 
                                       accept="image/*" class="hidden" onchange="previewSignature(this)">
                                Subir Firma
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Preferentemente PNG con fondo transparente</p>
                        </div>
                    </div>

                    <!-- Datos Básicos -->
                    <div class="lg:col-span-1 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre Completo *
                            </label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('name') }}" placeholder="Ej: Juan Pérez López">
                        </div>

                        <div>
                            <label for="num_empleado" class="block text-sm font-medium text-gray-700 mb-1">
                                Número de Empleado *
                            </label>
                            <input type="text" name="num_empleado" id="num_empleado" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('num_empleado') }}" placeholder="Ej: EMP-001">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos de Acceso -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Datos de Acceso
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email *
                        </label>
                        <input type="email" name="email" id="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                            Rol / Departamento *
                        </label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar rol</option>
                            <optgroup label="Dirección y TI">
                                <option value="direccion">Dirección</option>
                                <option value="ti">TI</option>
                                <option value="aux_ti">Auxiliar TI</option>
                            </optgroup>
                            <optgroup label="Almacén">
                                <option value="almacen">Almacén</option>
                                <option value="aux_almacen">Auxiliar Almacén</option>
                            </optgroup>
                            <optgroup label="Calidad">
                                <option value="calidad">Calidad</option>
                                <option value="aux_calidad">Auxiliar Calidad</option>
                            </optgroup>
                            <optgroup label="Contabilidad">
                                <option value="contabilidad">Contabilidad</option>
                                <option value="aux_contabilidad">Auxiliar Contabilidad</option>
                            </optgroup>
                            <optgroup label="Estimaciones">
                                <option value="estimaciones">Estimaciones</option>
                                <option value="aux_estimaciones">Auxiliar Estimaciones</option>
                            </optgroup>
                            <optgroup label="Finanzas">
                                <option value="finanzas">Finanzas</option>
                                <option value="aux_finanzas">Auxiliar Finanzas</option>
                            </optgroup>
                            <optgroup label="Logística">
                                <option value="logistica">Logística</option>
                                <option value="aux_logistica">Auxiliar Logística</option>
                            </optgroup>
                            <optgroup label="Recursos Humanos">
                                <option value="rh">Recursos Humanos</option>
                                <option value="aux_rh">Auxiliar RH</option>
                            </optgroup>
                            <optgroup label="Operaciones">
                                <option value="operaciones">Operaciones</option>
                                <option value="aux_operaciones">Auxiliar Operaciones</option>
                            </optgroup>
                            <optgroup label="HSE">
                                <option value="hse">HSE</option>
                                <option value="aux_hse">Auxiliar HSE</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña *
                        </label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Mínimo 6 caracteres">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Confirmar Contraseña *
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Repetir contraseña">
                    </div>
                </div>
            </div>

            <!-- Información de Permisos -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Permisos por Rol
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="font-semibold text-purple-700">🔐 Dirección</p>
                        <p class="text-gray-600 mt-1">Aprobación de solicitudes, requisiciones, gestión de usuarios e inventario</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="font-semibold text-blue-700">💻 TI</p>
                        <p class="text-gray-600 mt-1">Gestión de usuarios y acceso completo al sistema</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="font-semibold text-green-700">📦 Almacén / Aux Almacén</p>
                        <p class="text-gray-600 mt-1">Gestión de inventario, entradas y salidas</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="font-semibold text-gray-700">👥 Otros roles</p>
                        <p class="text-gray-600 mt-1">Creación de solicitudes de material y requisiciones</p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" 
                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-lg">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview de foto de perfil
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview de firma
function previewSignature(input) {
    const preview = document.getElementById('signature_preview');
    const placeholder = document.getElementById('signature_placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection