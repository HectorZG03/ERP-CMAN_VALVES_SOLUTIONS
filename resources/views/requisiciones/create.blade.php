@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Nueva Requisición de Material</h1>
        <a href="{{ route('requisiciones.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Volver
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('requisiciones.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre_solicitante" class="block text-sm font-medium text-gray-700">
                        Nombre del Solicitante
                    </label>
                    <input type="text" name="nombre_solicitante" id="nombre_solicitante" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('nombre_solicitante', auth()->user()->name) }}">
                </div>

                <div>
                    <label for="departamento" class="block text-sm font-medium text-gray-700">
                        Departamento
                    </label>
                    <select name="departamento" id="departamento" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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

                <div>
                    <label for="plataforma" class="block text-sm font-medium text-gray-700">
                        Plataforma
                    </label>
                    <input type="text" name="plataforma" id="plataforma" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('plataforma') }}" placeholder="Ej: Plataforma A, Oficinas, etc.">
                </div>

                <div>
                    <label for="embarcacion" class="block text-sm font-medium text-gray-700">
                        Embarcación/Barco
                    </label>
                    <input type="text" name="embarcacion" id="embarcacion" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('embarcacion') }}" placeholder="Nombre del barco o N/A">
                </div>

                <div>
                    <label for="material" class="block text-sm font-medium text-gray-700">
                        Material Solicitado
                    </label>
                    <input type="text" name="material" id="material" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('material') }}" placeholder="Descripción detallada del material">
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">
                        Cantidad
                    </label>
                    <input type="number" name="cantidad" id="cantidad" required min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('cantidad') }}">
                </div>

                <div>
                    <label for="unidad" class="block text-sm font-medium text-gray-700">
                        Unidad de Medida
                    </label>
                    <input type="text" name="unidad" id="unidad" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('unidad') }}" placeholder="Ej: Piezas, Kg, Litros, etc.">
                </div>

                <div>
                    <label for="tipo_requerimiento" class="block text-sm font-medium text-gray-700">
                        Tipo de Requerimiento
                    </label>
                    <select name="tipo_requerimiento" id="tipo_requerimiento" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar tipo</option>
                        <option value="interno">Interno</option>
                        <option value="externo">Externo</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="comentario" class="block text-sm font-medium text-gray-700">
                        Comentario/Justificación
                    </label>
                    <textarea name="comentario" id="comentario" rows="4" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Explica detalladamente la necesidad del material, uso específico, urgencia, etc.">{{ old('comentario') }}</textarea>
                </div>
            </div>

            <div class="mt-6 bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-purple-900 mb-2">Información sobre Requisiciones</h3>
                <div class="text-sm text-purple-700 space-y-1">
                    <p><strong>Interno:</strong> Material que se requiere de otros departamentos o almacén interno</p>
                    <p><strong>Externo:</strong> Material que debe ser comprado a proveedores externos</p>
                    <p><strong>Proceso:</strong> La requisición será revisada por Dirección y luego enviada a Almacén</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Enviar Requisición
                </button>
            </div>
        </form>
    </div>
</div>
@endsection