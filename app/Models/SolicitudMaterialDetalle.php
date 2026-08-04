<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudMaterialDetalle extends Model
{
    use HasFactory;

    protected $table = 'solicitud_material_detalles';

    protected $fillable = [
        'solicitud_material_id',
        'inventario_id',
        'cantidad_solicitada',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'integer',
        'precio_unitario' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Solicitud principal a la que pertenece el detalle.
     */
    public function solicitudMaterial()
    {
        return $this->belongsTo(
            SolicitudMaterial::class,
            'solicitud_material_id'
        );
    }

    /**
     * Producto relacionado con el inventario.
     */
    public function inventario()
    {
        return $this->belongsTo(
            Inventario::class,
            'inventario_id'
        );
    }

    /**
     * Subtotal del producto solicitado.
     */
    public function getSubtotalAttribute()
    {
        return $this->cantidad_solicitada
            * ($this->precio_unitario ?? 0);
    }

    /**
     * Filtrar detalles por producto.
     */
    public function scopeProducto($query, $inventarioId)
    {
        return $query->where(
            'inventario_id',
            $inventarioId
        );
    }

    /**
     * Verificar si existe suficiente disponibilidad.
     */
    public function verificarDisponibilidad()
    {
        if (!$this->inventario) {
            return false;
        }

        return $this->inventario->existencia
            >= $this->cantidad_solicitada;
    }
}