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

        // Campo histórico con el nombre de la embarcación
        'destino',

        // Relación con el catálogo de embarcaciones
        'embarcacion_id',

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
     * Personal u operador asignado a la solicitud.
     */
    public function operadorPersonal()
    {
        return $this->belongsTo(
            Personal::class,
            'personal_id'
        );
    }

    /**
     * Usuario que realizó la solicitud.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Embarcación seleccionada desde el catálogo.
     *
     * Se utiliza embarcacionCatalogo porque la columna
     * histórica continúa llamándose destino.
     */
    public function embarcacionCatalogo()
    {
        return $this->belongsTo(
            Embarcacion::class,
            'embarcacion_id'
        );
    }

    /**
     * Detalles o productos incluidos en la solicitud.
     */
    public function detalles()
    {
        return $this->hasMany(
            SolicitudMaterialDetalle::class,
            'solicitud_material_id'
        );
    }

    /**
     * Calcula el valor económico estimado de la solicitud.
     */
    public function getTotalAttribute()
    {
        return $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad_solicitada
                * ($detalle->precio_unitario ?? 0);
        });
    }

    /**
     * Obtiene el número de productos diferentes.
     */
    public function getTotalProductosAttribute()
    {
        return $this->detalles->count();
    }

    /**
     * Obtiene el total de unidades solicitadas.
     */
    public function getTotalUnidadesAttribute()
    {
        return $this->detalles->sum('cantidad_solicitada');
    }

    /**
     * Filtra las solicitudes por estatus.
     */
    public function scopeEstatus($query, $estatus)
    {
        return $query->where('estatus', $estatus);
    }

    /**
     * Filtra las solicitudes pendientes.
     */
    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    /**
     * Filtra las solicitudes aprobadas.
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estatus', 'aprobado');
    }
}