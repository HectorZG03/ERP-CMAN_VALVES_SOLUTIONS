<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequisicionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $datos = [];
        foreach (['nombre_solicitante', 'departamento', 'proyecto', 'sit', 'partida', 'plataforma', 'area', 'activo', 'destino', 'comentario'] as $campo) {
            $valor = $this->input($campo);
            if (is_string($valor)) {
                $datos[$campo] = mb_strtoupper($valor, 'UTF-8');
            }
        }

        $materiales = $this->input('materiales');
        if (is_array($materiales)) {
            foreach ($materiales as &$material) {
                if (! is_array($material)) {
                    continue;
                }
                foreach (['unidad', 'material'] as $campo) {
                    if (isset($material[$campo]) && is_string($material[$campo])) {
                        $material[$campo] = mb_strtoupper($material[$campo], 'UTF-8');
                    }
                }
            }
            unset($material);
            $datos['materiales'] = $materiales;
        }
        $this->merge($datos);
    }

    public function rules(): array
    {
        return [
            'nombre_solicitante' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'proyecto' => 'nullable|string|max:255',
            'sit' => 'nullable|string|max:255',
            'partida' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'activo' => 'nullable|string|max:255',
            'plataforma' => 'nullable|string|max:255',
            'destino' => 'nullable|string|max:255',
            'tipo_requerimiento' => 'required|in:interno,externo',
            'comentario' => 'required|string',
            'contrato_id' => 'required|exists:contratos,id',
            'materiales' => 'required|array|min:1',
            'materiales.*.cantidad' => 'required|integer|min:1',
            'materiales.*.unidad' => 'required|string|max:255',
            'materiales.*.material' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_solicitante.required' => 'El nombre del solicitante es obligatorio',
            'departamento.required' => 'El departamento es obligatorio',
            'tipo_requerimiento.required' => 'Debes seleccionar un tipo de requerimiento',
            'tipo_requerimiento.in' => 'El tipo de requerimiento debe ser interno o externo',
            'comentario.required' => 'Debes proporcionar un comentario o justificación',
            'materiales.required' => 'Debes agregar al menos un material',
            'materiales.min' => 'Debes agregar al menos un material',
            'materiales.*.cantidad.required' => 'La cantidad es obligatoria',
            'materiales.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'materiales.*.cantidad.min' => 'La cantidad debe ser al menos 1',
            'materiales.*.unidad.required' => 'La unidad es obligatoria',
            'materiales.*.material.required' => 'La descripción del material es obligatoria',
            'contrato_id.required' => 'Debes seleccionar un contrato',
        ];
    }
}
