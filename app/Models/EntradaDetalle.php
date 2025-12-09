<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'inventario_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'iva',
        'total_con_iva',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
        'iva' => 'decimal:2',
        'total_con_iva' => 'decimal:2',
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    // Calcular totales automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            // Calcular precio total, IVA y total con IVA
            $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
            $detalle->iva = $detalle->precio_total * 0.16;
            $detalle->total_con_iva = $detalle->precio_total + $detalle->iva;
            
            // Actualizar inventario
            $inventario = $detalle->inventario;
            $inventario->existencia += $detalle->cantidad;
            $inventario->precio_total += $detalle->precio_total;
            $inventario->save();
        });

        static::created(function ($detalle) {
            // Recalcular totales de la entrada
            if ($detalle->entrada) {
                $detalle->entrada->calcularTotalesDesdeDetalles();
            }
        });

        static::updated(function ($detalle) {
            // Recalcular si cambia la cantidad o precio
            $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
            $detalle->iva = $detalle->precio_total * 0.16;
            $detalle->total_con_iva = $detalle->precio_total + $detalle->iva;
            $detalle->saveQuietly();
            
            // Recalcular totales de la entrada
            if ($detalle->entrada) {
                $detalle->entrada->calcularTotalesDesdeDetalles();
            }
        });

        static::deleting(function ($detalle) {
            // Revertir inventario si se elimina el detalle
            $inventario = $detalle->inventario;
            $inventario->existencia -= $detalle->cantidad;
            $inventario->precio_total -= $detalle->precio_total;
            $inventario->save();
        });

        static::deleted(function ($detalle) {
            // Recalcular totales de la entrada
            if ($detalle->entrada) {
                $detalle->entrada->calcularTotalesDesdeDetalles();
            }
        });
    }
}