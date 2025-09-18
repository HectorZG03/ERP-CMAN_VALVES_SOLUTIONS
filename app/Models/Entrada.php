<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventario_id',
        'proveedor_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'iva',
        'total_con_iva',
        'user_id',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}