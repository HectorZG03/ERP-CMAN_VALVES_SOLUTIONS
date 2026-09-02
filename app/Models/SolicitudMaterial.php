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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Operador o personal asignado a la solicitud.
     */
    public function operadorPersonal()
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    /**
     * Usuario que generó la solicitud.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Materiales incluidos en la solicitud.
     */
    public function detalles()
    {
        return $this->hasMany(SolicitudMaterialDetalle::class);
    }

    /**
     * Salidas de almacén generadas desde esta solicitud.
     */
    public function salidas()
    {
        return $this->hasMany(Salida::class, 'solicitud_material_id');
    }

    /**
     * Valor total estimado de la solicitud.
     */
    public function getTotalAttribute()
    {
        return $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad_solicitada * ($detalle->precio_unitario ?? 0);
        });
    }

    /**
     * Número de productos diferentes.
     */
    public function getTotalProductosAttribute()
    {
        return $this->detalles->count();
    }

    /**
     * Total de unidades solicitadas.
     */
    public function getTotalUnidadesAttribute()
    {
        return $this->detalles->sum('cantidad_solicitada');
    }

    /**
     * Filtrar solicitudes por estatus.
     */
    public function scopeEstatus($query, $estatus)
    {
        return $query->where('estatus', $estatus);
    }

    /**
     * Solicitudes pendientes.
     */
    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    /**
     * Solicitudes aprobadas.
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estatus', 'aprobado');
    }
}