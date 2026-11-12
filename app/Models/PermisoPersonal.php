<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermisoPersonal extends Model
{
    protected $table = 'permisos_personal';

    protected $fillable = [
        'personal_id',
        'registrado_por_user_id',
        'revisado_por_user_id',
        'resuelto_por_user_id',
        'fecha_permiso',
        'horas_solicitadas',
        'motivo',
        'estatus',
        'goce_sueldo',
        'revisado_at',
        'comentario_revision',
        'resuelto_at',
        'comentario_resolucion',
    ];

    protected $casts = [
        'fecha_permiso' => 'date',
        'horas_solicitadas' => 'decimal:2',
        'goce_sueldo' => 'boolean',
        'revisado_at' => 'datetime',
        'resuelto_at' => 'datetime',
    ];

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_user_id');
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por_user_id');
    }

    public function resueltoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resuelto_por_user_id');
    }
}