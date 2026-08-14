<?php

namespace App\Http\Controllers;

use App\Models\Embarcacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmbarcacionController extends Controller
{
    /**
     * Mostrar el listado de embarcaciones.
     */
    public function index()
    {
        $embarcaciones = Embarcacion::query()
            ->withCount([
                'requisiciones',
                'solicitudesMaterial as solicitudes_count',
            ])
            ->orderBy('nombre')
            ->paginate(15);

        return view(
            'embarcaciones.index',
            compact('embarcaciones')
        );
    }

    /**
     * Guardar una nueva embarcación.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                    'unique:embarcaciones,nombre',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre de la embarcación es obligatorio.',

                'nombre.string' =>
                    'El nombre de la embarcación no es válido.',

                'nombre.max' =>
                    'El nombre no puede tener más de 150 caracteres.',

                'nombre.unique' =>
                    'Ya existe una embarcación con este nombre.',
            ]
        );

        $embarcacion = Embarcacion::create([
            'nombre' => $this->limpiarNombre($validated['nombre']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Embarcación registrada correctamente.',
            'embarcacion' => $embarcacion,
        ], 201);
    }

    /**
     * Actualizar una embarcación existente.
     */
    public function update(
        Request $request,
        Embarcacion $embarcacion
    ): JsonResponse {
        $validated = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                    Rule::unique('embarcaciones', 'nombre')
                        ->ignore($embarcacion->id),
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre de la embarcación es obligatorio.',

                'nombre.string' =>
                    'El nombre de la embarcación no es válido.',

                'nombre.max' =>
                    'El nombre no puede tener más de 150 caracteres.',

                'nombre.unique' =>
                    'Ya existe una embarcación con este nombre.',
            ]
        );

        $embarcacion->update([
            'nombre' => $this->limpiarNombre($validated['nombre']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Embarcación actualizada correctamente.',
            'embarcacion' => $embarcacion->fresh(),
        ]);
    }

    /**
     * Eliminar una embarcación.
     */
    public function destroy(
        Embarcacion $embarcacion
    ): JsonResponse {
        $tieneRequisiciones = $embarcacion
            ->requisiciones()
            ->exists();

        $tieneSolicitudes = $embarcacion
            ->solicitudesMaterial()
            ->exists();

        if ($tieneRequisiciones || $tieneSolicitudes) {
            return response()->json([
                'success' => false,
                'message' =>
                    'No se puede eliminar esta embarcación porque está relacionada con solicitudes o requisiciones.',
            ], 409);
        }

        $embarcacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Embarcación eliminada correctamente.',
        ]);
    }

    /**
     * Limpiar espacios innecesarios del nombre.
     */
    private function limpiarNombre(string $nombre): string
    {
        return preg_replace(
            '/\s+/',
            ' ',
            trim($nombre)
        );
    }
}