<?php

namespace App\Http\Requests;

use App\Models\SolicitudMaterial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitudMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $datos = [];
        foreach (['destino', 'comentario', 'operador', 'categoria'] as $campo) {
            $valor = $this->input($campo);
            if (is_string($valor)) {
                $datos[$campo] = mb_strtoupper($valor, 'UTF-8');
            }
        }
        $this->merge($datos);
    }

    public function rules(): array
    {
        return [
            'personal_id' => [
                'nullable',
                'exists:personal,id',
            ],

            'destino' => [
                'required',
                'string',
                'max:255',
            ],

            'comentario' => [
                'nullable',
                'string',
            ],

            'operador' => [
                'nullable',
                'string',
                'max:255',
            ],

            'categoria' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tipo_solicitud' => [
                'nullable',
                Rule::in(SolicitudMaterial::TIPOS_VALIDOS),
            ],

            'productos' => [
                'required',
                'array',
                'min:1',
            ],

            'productos.*.inventario_id' => [
                'required',
                'integer',
                'distinct',
                'exists:inventarios,id',
            ],

            'productos.*.cantidad_solicitada' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destino.required' => 'Debe indicar un destino.',
            'destino.string' => 'El destino debe ser un texto válido.',
            'destino.max' => 'El destino no puede superar los 255 caracteres.',

            'tipo_solicitud.in' => 'El tipo de solicitud no es válido.',

            'productos.required' => 'Debe agregar al menos un producto a la solicitud.',
            'productos.min' => 'Debe agregar al menos un producto a la solicitud.',

            'productos.*.inventario_id.required' => 'Debe seleccionar un producto válido.',
            'productos.*.inventario_id.distinct' => 'No puede agregar el mismo producto más de una vez.',
            'productos.*.inventario_id.exists' => 'Uno de los productos seleccionados no existe.',

            'productos.*.cantidad_solicitada.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad_solicitada.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor a cero.',
        ];
    }
}
