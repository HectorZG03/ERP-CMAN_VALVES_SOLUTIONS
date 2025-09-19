<?php

// 2. Modelo PrestamoMaterialDetalle.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoMaterialDetalle extends Model
{
    use HasFactory;

    protected $table = 'prestamo_material_detalles';

    protected $fillable = [
        'prestamo_material_id',
        'inventario_id',
        'cantidad_prestada',
        'cantidad_devuelta',
        'precio_unitario',
        'estado_devolucion',
        'condicion_devolucion',
    ];

    protected $casts = [
        'cantidad_prestada' => 'integer',
        'cantidad_devuelta' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    // Relación con el préstamo principal
    public function prestamo()
    {
        return $this->belongsTo(PrestamoMaterial::class, 'prestamo_material_id');
    }

    // Relación con el inventario
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    // Atributos calculados
    public function getCantidadPendienteAttribute()
    {
        return $this->cantidad_prestada - $this->cantidad_devuelta;
    }

    public function getSubtotalAttribute()
    {
        return $this->cantidad_prestada * ($this->precio_unitario ?? 0);
    }

    public function getEstaCompletoAttribute()
    {
        return $this->cantidad_devuelta >= $this->cantidad_prestada;
    }

    public function getEstadoTextoAttribute()
    {
        if ($this->cantidad_devuelta == 0) {
            return 'Prestado';
        } elseif ($this->cantidad_devuelta < $this->cantidad_prestada) {
            return 'Devuelto Parcialmente';
        } else {
            return 'Devuelto Completo';
        }
    }
}
