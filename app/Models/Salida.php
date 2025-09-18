<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventario_id',
        'cliente_id',
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

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}