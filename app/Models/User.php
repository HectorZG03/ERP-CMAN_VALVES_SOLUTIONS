<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canManageUsers()
    {
        return in_array($this->role, ['ti']);
    }


    // esto es para ver si puede aprobar o no los préstamos

    public function canApproveRequests()
    {
        return $this->role === 'direccion'; // Cambiado a 'administrador'
    }

    public function canManageInventory()
    {
        return in_array($this->role, ['almacen', 'aux_almacen']);
    }


    public function canManageInventoryadmin()
    {
        return in_array($this->role, ['direccion']);
    }

    /**
     * Verificar si el usuario tiene uno o más roles específicos
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($this->role, $roles);
    }

    /**
     * Relación con los préstamos de material
     */
    public function prestamosMateriales()
    {
        return $this->hasMany(PrestamoMaterial::class);
    }

    /**
     * Obtener préstamos activos del usuario
     */
    public function prestamosActivos()
    {
        return $this->prestamosMateriales()
                    ->where('estatus', 'prestado')
                    ->orderBy('fecha_devolucion_esperada', 'asc');
    }

    /**
     * Obtener préstamos vencidos del usuario
     */
    public function prestamosVencidos()
    {
        return $this->prestamosMateriales()
                    ->where('estatus', 'prestado')
                    ->whereDate('fecha_devolucion_esperada', '<', now())
                    ->orderBy('fecha_devolucion_esperada', 'asc');
    }

    /**
     * Verificar si el usuario tiene préstamos vencidos
     */
    public function tienePrestamosVencidos()
    {
        return $this->prestamosVencidos()->exists();
    }

    /**
     * Obtener el total de préstamos del usuario
     */
    public function getTotalPrestamosAttribute()
    {
        return $this->prestamosMateriales()->count();
    }

    /**
     * Obtener el total de préstamos activos
     */
    public function getTotalPrestamosActivosAttribute()
    {
        return $this->prestamosActivos()->count();
    }

    /**
     * Verificar si el usuario puede solicitar préstamos
     */
    public function puedeSolicitarPrestamo()
    {
        // No permitir nuevos préstamos si tiene préstamos vencidos
        if ($this->tienePrestamosVencidos()) {
            return false;
        }

        // Límite de préstamos activos simultáneos (máximo 5)
        if ($this->prestamosActivos()->count() >= 5) {
            return false;
        }

        return true;
    }
}