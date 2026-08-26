<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAjusteInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventory() ?? false;
    }

    public function rules(): array
    {
        return [
            'operacion' => ['required', Rule::in(['stock', 'revaluacion'])],
            'nueva_existencia' => ['nullable', 'required_if:operacion,stock', 'integer', 'min:0', 'max:2147483647'],
            'costo_unitario_ajuste' => ['nullable', 'numeric', 'max:99999999.99'],
            'nuevo_costo_unitario' => ['exclude_unless:operacion,revaluacion', 'required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'motivo' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'operacion.required' => 'Selecciona el tipo de ajuste.',
            'operacion.in' => 'El tipo de ajuste seleccionado no es válido.',
            'nueva_existencia.required_if' => 'Indica la nueva existencia física.',
            'nueva_existencia.integer' => 'La nueva existencia debe ser un número entero.',
            'nueva_existencia.min' => 'La existencia no puede ser negativa.',
            'costo_unitario_ajuste.numeric' => 'El costo de las unidades agregadas debe ser numérico.',
            'nuevo_costo_unitario.required' => 'Indica el nuevo costo unitario.',
            'nuevo_costo_unitario.numeric' => 'El nuevo costo unitario debe ser numérico.',
            'nuevo_costo_unitario.min' => 'El nuevo costo unitario debe ser mayor que cero.',
            'motivo.required' => 'El motivo del ajuste es obligatorio.',
            'motivo.min' => 'Explica el motivo del ajuste con al menos 5 caracteres.',
            'motivo.max' => 'El motivo no puede exceder 1000 caracteres.',
        ];
    }
}
