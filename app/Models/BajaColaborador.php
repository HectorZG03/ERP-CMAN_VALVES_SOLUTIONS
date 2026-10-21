<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class BajaColaborador extends Model
{
    use HasFactory;

    protected $table = 'bajas_colaboradores';

    protected $fillable = [
        'personal_id',
        'fecha_baja',
        'motivo_baja',
        'estado',
        'anulada_por',
        'user_id',
    ];

    protected $casts = [
        'fecha_baja' => 'date',
    ];

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    /**
     * Usuario que registró la baja.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario que anuló la baja.
     */
    public function usuarioAnulacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulada_por');
    }

    /**
     * Bajas que siguen activas.
     */
    public function scopeActiva(Builder $query): Builder
    {
        return $query->where('estado', 'activa');
    }

    /**
     * Bajas anuladas que se conservan como historial.
     */
    public function scopeAnulada(Builder $query): Builder
    {
        return $query->where('estado', 'anulada');
    }
}
