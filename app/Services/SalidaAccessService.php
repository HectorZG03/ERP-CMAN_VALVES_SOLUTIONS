<?php

namespace App\Services;

use App\Models\Salida;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SalidaAccessService
{
    /**
     * Filtra el listado según el área del usuario asociado
     * a la solicitud que originó la salida.
     *
     * Almacén, Dirección y TI conservan acceso completo.
     */
    public function filtrarListado(Builder $query, User $usuario): Builder
    {
        $rolesPermitidos = $this->rolesDeAreaVisible($usuario);

        if ($rolesPermitidos === null) {
            return $query;
        }

        return $query->whereHas(
            'solicitudMaterial.user',
            fn (Builder $solicitante) => $solicitante
                ->whereIn('role', $rolesPermitidos)
        );
    }

    /**
     * Autoriza la consulta de una salida individual.
     */
    public function autorizarConsulta(User $usuario, Salida $salida): void
    {
        $rolesPermitidos = $this->rolesDeAreaVisible($usuario);

        if ($rolesPermitidos === null) {
            return;
        }

        $salida->loadMissing('solicitudMaterial.user');

        $rolSolicitante = $salida->solicitudMaterial?->user?->role;

        abort_unless(
            in_array($rolSolicitante, $rolesPermitidos, true),
            404
        );
    }

    /**
     * Determina qué roles puede consultar cada usuario.
     * null significa acceso completo al historial.
     */
    private function rolesDeAreaVisible(User $usuario): ?array
    {
        return match ($usuario->role) {
            'operaciones', 'aux_operaciones' => [
                'logistica',
                'aux_logistica',
            ],

            'logistica', 'aux_logistica' => [
                'operaciones',
                'aux_operaciones',
            ],

            default => null,
        };
    }
}