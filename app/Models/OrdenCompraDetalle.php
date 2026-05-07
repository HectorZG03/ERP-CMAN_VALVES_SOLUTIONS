<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraDetalle extends Model
{
    use HasFactory;

    protected $table = 'orden_compra_detalles';

    protected $fillable = [
        'orden_compra_id',
        'codigo',
        'descripcion',
        'cantidad',
        'unidad',
        'precio_unitario',
        'total',
    ];

    protected $casts = [
        'cantidad'       => 'float',
        'precio_unitario'=> 'float',
        'total'          => 'float',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }
}