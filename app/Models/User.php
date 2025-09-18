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
    return in_array($this->role, ['ti', 'direccion']);
}

public function canApproveRequests()
{
    return $this->role === 'direccion';
}

public function canManageInventory()
{
    return in_array($this->role, ['almacen', 'aux_almacen']);
}

/**
 * Verificar si el usuario tiene uno o más roles específicos
 *
 * @param string|array $roles
 * @return bool
 */
public function hasRole($roles)
{
    // Convertir a array si es string
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    // Verificar si el role del usuario está en el array de roles permitidos
    return in_array($this->role, $roles);
}


}