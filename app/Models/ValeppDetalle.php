<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValeppDetalle extends Model
{
    use HasFactory;

    protected $table = 'valepp_detalles';

    protected $fillable = [
        'valepp_id',
        'inventario_id',
        'cantidad',
        'fecha_entrega',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'cantidad' => 'integer',
    ];

    // Relaciones
    public function valepp()
    {
        return $this->belongsTo(Valepp::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}