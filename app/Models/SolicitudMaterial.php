<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudMaterial extends Model
{
    use HasFactory;

    protected $table = 'solicitud_materiales';

    protected $fillable = [
        'user_id',
        'personal_id',
        'destino',
        'estatus',
        'comentario',
        'operador',
        'categoria',
    ];

    // Relación con el operador (personal)
        public function operadorPersonal()
        {
            return $this->belongsTo(\App\Models\Personal::class, 'personal_id');
        }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el usuario que hizo la solicitud
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los detalles de la solicitud (múltiples productos)
    public function detalles()
    {
        return $this->hasMany(SolicitudMaterialDetalle::class);
    }

    // Método para obtener el total de la solicitud
    public function getTotalAttribute()
    {
        return $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad_solicitada * ($detalle->precio_unitario ?? 0);
        });
    }

    // Método para obtener el número total de productos diferentes
    public function getTotalProductosAttribute()
    {
        return $this->detalles->count();
    }

    // Método para obtener el total de unidades solicitadas
    public function getTotalUnidadesAttribute()
    {
        return $this->detalles->sum('cantidad_solicitada');
    }

    // Scope para filtrar por estatus
    public function scopeEstatus($query, $estatus)
    {
        return $query->where('estatus', $estatus);
    }

    // Scope para solicitudes pendientes
    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    // Scope para solicitudes aprobadas
    public function scopeAprobadas($query)
    {
        return $query->where('estatus', 'aprobado');
    }
}