@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Requisición de Material</h1>
        <a href="{{ route('requisiciones.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">
        <form method="POST" action="{{ route('requisiciones.store') }}">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN DEL SOLICITANTE -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Solicitante
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre_solicitante" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre del Solicitante <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre_solicitante" id="nombre_solicitante" required
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('nombre_solicitante', auth()->user()->name) }}">
                    </div>

                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Departamento <span class="text-red-500">*</span>
                        </label>
                        <select name="departamento" id="departamento" required
                                class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar departamento</option>
                            <option value="Almacén">Almacén</option>
                            <option value="Calidad">Calidad</option>
                            <option value="Contabilidad">Contabilidad</option>
                            <option value="Estimaciones">Estimaciones</option>
                            <option value="Finanzas">Finanzas</option>
                            <option value="Logística">Logística</option>
                            <option value="Recursos Humanos">Recursos Humanos</option>
                            <option value="TI">Tecnología</option>
                            <option value="Dirección">Dirección</option>
                            <option value="Operaciones">Operaciones</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL PROYECTO -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Proyecto
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="proyecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Proyecto
                        </label>
                        <input type="text" name="proyecto" id="proyecto"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('proyecto') }}" placeholder="Nombre del proyecto o N/A">
                    </div>

                    <div>
                        <label for="sit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            SIT (Sistema de Identificación de Trabajo)
                        </label>
                        <input type="text" name="sit" id="sit"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('sit') }}" placeholder="Código SIT o N/A">
                    </div>

                    <div>
                        <label for="partida" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Partida
                        </label>
                        <input type="text" name="partida" id="partida"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('partida') }}" placeholder="Número de partida o N/A">
                    </div>

                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Área
                        </label>
                        <input type="text" name="area" id="area"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('area') }}" placeholder="Área específica o N/A">
                    </div>

                    <div>
                        <label for="activo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Activo
                        </label>
                        <input type="text" name="activo" id="activo"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('activo') }}" placeholder="Número de activo o N/A">
                    </div>

                    <div>
                        <label for="contrato" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Contrato
                        </label>
                        <input type="text" name="contrato" id="contrato"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('contrato') }}" placeholder="Número de contrato o N/A">
                    </div>

                    <div>
                        <label for="combenio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Convenio
                        </label>
                        <input type="text" name="combenio" id="combenio"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('combenio') }}" placeholder="Número de convenio o N/A">
                    </div>

                    <div>
                        <label for="plataforma" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Plataforma <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="plataforma" id="plataforma" required
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('plataforma') }}" placeholder="Ej: Plataforma A, Oficinas, etc.">
                    </div>

                    <div>
                        <label for="embarcacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Embarcación/Barco <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="embarcacion" id="embarcacion" required
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('embarcacion') }}" placeholder="Nombre del barco o N/A">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: INFORMACIÓN DEL MATERIAL -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Material
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="material" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Material Solicitado <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="material" id="material" required
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('material') }}" placeholder="Descripción detallada del material">
                    </div>

                    <div>
                        <label for="cantidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="cantidad" id="cantidad" required min="1"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('cantidad') }}">
                    </div>

                    <div>
                        <label for="unidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Unidad de Medida <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unidad" id="unidad" required
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                               value="{{ old('unidad') }}" placeholder="Ej: Piezas, Kg, Litros, P/Z, etc.">
                    </div>

                    <div>
                        <label for="tipo_requerimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tipo de Requerimiento <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo_requerimiento" id="tipo_requerimiento" required
                                class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                            <option value="">Seleccionar tipo</option>
                            <option value="interno">Interno</option>
                            <option value="externo">Externo</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="comentario" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Comentario/Justificación <span class="text-red-500">*</span>
                        </label>
                        <textarea name="comentario" id="comentario" rows="4" required
                                  class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                                  placeholder="Explica detalladamente la necesidad del material, uso específico, urgencia, etc.">{{ old('comentario') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg transition-colors duration-200">
                <h3 class="text-lg font-medium text-purple-900 dark:text-purple-200 mb-2">Información sobre Requisiciones</h3>
                <div class="text-sm text-purple-700 dark:text-purple-300 space-y-1">
                    <p><strong>Interno:</strong> Material que se requiere de otros departamentos o almacén interno</p>
                    <p><strong>Externo:</strong> Material que debe ser comprado a proveedores externos</p>
                    <p><strong>Proceso:</strong> La requisición será revisada por Dirección y luego enviada a Almacén</p>
                    <p><strong>Nota:</strong> Los campos sin asterisco (*) son opcionales y se registrarán como "N/A" si no se llenan</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Enviar Requisición
                </button>
            </div>
        </form>
    </div>
</div>
@endsection