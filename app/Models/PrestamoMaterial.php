<?php

// 1. Modelo PrestamoMaterial.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PrestamoMaterial extends Model
{
    use HasFactory;

    protected $table = 'prestamo_materiales';

    protected $fillable = [
        'user_id',
        'fecha_prestamo',
        'fecha_devolucion_esperada',
        'fecha_devolucion_real',
        'destino',
        'estatus',
        'comentario',
        'observaciones_devolucion',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_devolucion_esperada' => 'date',
        'fecha_devolucion_real' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el usuario que solicita el préstamo
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los detalles del préstamo
    public function detalles()
    {
        return $this->hasMany(PrestamoMaterialDetalle::class);
    }

    // Atributos calculados
    public function getTotalProductosAttribute()
    {
        return $this->detalles->count();
    }

    public function getTotalUnidadesPrestadasAttribute()
    {
        return $this->detalles->sum('cantidad_prestada');
    }

    public function getTotalUnidadesDevueltasAttribute()
    {
        return $this->detalles->sum('cantidad_devuelta');
    }

    public function getEstaVencidoAttribute()
    {
        if ($this->estatus === 'devuelto') {
            return false;
        }
        return Carbon::now()->isAfter($this->fecha_devolucion_esperada);
    }

    public function getDiasVencidoAttribute()
    {
        if (!$this->esta_vencido) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->fecha_devolucion_esperada);
    }

    public function getDiasPrestadosAttribute()
    {
        if ($this->fecha_devolucion_real) {
            return $this->fecha_prestamo->diffInDays($this->fecha_devolucion_real);
        }
        return $this->fecha_prestamo->diffInDays(Carbon::now());
    }

    public function getDevolucionCompletaAttribute()
    {
        return $this->detalles->every(function ($detalle) {
            return $detalle->cantidad_devuelta >= $detalle->cantidad_prestada;
        });
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    public function scopePrestados($query)
    {
        return $query->where('estatus', 'prestado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estatus', 'prestado')
                     ->whereDate('fecha_devolucion_esperada', '<', Carbon::now());
    }

    public function scopeProximos($query)
    {
        return $query->where('estatus', 'prestado')
                     ->whereBetween('fecha_devolucion_esperada', [Carbon::now(), Carbon::now()->addDays(3)]);
    }
}
