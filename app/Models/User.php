<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'personal_id',
        'signature',
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

    // ✅ Verificar si puede aprobar requisiciones en finanzas
    public function canApproveFinanzas()
    {
        return in_array($this->role, ['finanzas', 'aux_finanzas']);
    }

    // Esto es para ver si puede aprobar o no las requisiciones (DIRECTOR)
    public function canApproveRequests()
    {
        return $this->role === 'direccion';
    }

    // ✅ CORREGIDO: Solo RH y Auxiliar RH
    /**
     * Verifica si el usuario puede gestionar personal (RH)
     */
    public function canManagePersonal()
    {
        return in_array($this->role, ['rh', 'aux_rh']);
    }

    // ✅ CORREGIDO: Solo HSE y Auxiliar HSE
    /**
     * Verifica si el usuario puede gestionar Vale EPP (HSE)
     */
    public function canManageValeEPP()
    {
        return in_array($this->role, ['hse', 'aux_hse']);
    }

    public function canManageInventory()
    {
        return in_array($this->role, ['almacen', 'aux_almacen']);
    }

    public function canManageInventoryadmin()
    {
        return in_array($this->role, ['direccion']);
    }

    public function ajustesInventario()
    {
        return $this->hasMany(AjusteInventario::class);
    }


    /** esta oparte es para que salga en la parte de la barre quien lo puede ver y quien no 
     * Verificar si el usuario puede gestionar finanzas
     */
    public function canManageFinanzas()
    {
        return in_array($this->role, ['finanzas', 'aux_finanzas']);
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



    /**
     * Obtener la URL de la firma
     */
    public function getSignatureUrlAttribute()
    {
        return $this->signature ? asset('storage/' . $this->signature) : null;
    }

    /**
     * Número laboral del expediente vinculado. Los campos heredados de la
     * cuenta se conservan durante la transición, sin utilizarlos como respaldo.
     */
    public function getNumeroEmpleadoLaboralAttribute(): ?string
    {
        $numero = trim((string) $this->personal?->datosLaborales?->employee_id);

        return $numero === '' || strtoupper($numero) === 'N/A' ? null : $numero;
    }

    /**
     * Foto del expediente vinculado o avatar cuando no hay foto registrada.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        $foto = trim((string) $this->personal?->foto);

        if ($foto === '' || strtoupper($foto) === 'N/A') {
            return asset('images/default-avatar.svg');
        }

        return asset('storage/' . ltrim($foto, '/'));
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    /**
     * Relación uno a muchos con los permisos del personal.
     */
    public function permisosRegistrados(): HasMany
    {
        return $this->hasMany(PermisoPersonal::class, 'registrado_por_user_id');
    }

    public function permisosRevisados(): HasMany
    {
        return $this->hasMany(PermisoPersonal::class, 'revisado_por_user_id');
    }

    /**
     * Relación uno a muchos con los permisos resueltos por el usuario.
     */
    public function permisosResueltos(): HasMany
    {
        return $this->hasMany(PermisoPersonal::class, 'resuelto_por_user_id');
    }



}
