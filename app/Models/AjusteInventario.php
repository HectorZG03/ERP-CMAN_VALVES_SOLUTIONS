<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteInventario extends Model
{
    use HasFactory;

    protected $table = 'ajustes_inventario';

    protected $fillable = [
        'inventario_id',
        'user_id',
        'producto',
        'economico',
        'usuario_nombre',
        'tipo',
        'existencia_anterior',
        'existencia_nueva',
        'diferencia',
        'costo_promedio_anterior',
        'costo_unitario_ajuste',
        'costo_promedio_nuevo',
        'valor_total_anterior',
        'valor_total_nuevo',
        'diferencia_valor',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'costo_promedio_anterior' => 'decimal:4',
            'costo_unitario_ajuste' => 'decimal:4',
            'costo_promedio_nuevo' => 'decimal:4',
            'valor_total_anterior' => 'decimal:2',
            'valor_total_nuevo' => 'decimal:2',
            'diferencia_valor' => 'decimal:2',
        ];
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
