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

    // Relación con la solicitud principal
    public function solicitudMaterial()
    {
        return $this->belongsTo(SolicitudMaterial::class);
    }

    // Relación con el inventario/producto
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    // Accessor para obtener el subtotal
    public function getSubtotalAttribute()
    {
        return $this->cantidad_solicitada * ($this->precio_unitario ?? 0);
    }

    // Scope para filtrar por producto
    public function scopeProducto($query, $inventarioId)
    {
        return $query->where('inventario_id', $inventarioId);
    }

    // Método para verificar disponibilidad antes de guardar
    public function verificarDisponibilidad()
    {
        if (!$this->inventario) {
            return false;
        }

        return $this->inventario->existencia >= $this->cantidad_solicitada;
    }
}