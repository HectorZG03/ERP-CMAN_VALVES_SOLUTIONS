<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisicion extends Model
{
    use HasFactory;

    protected $table = 'requisiciones';
    
    public $timestamps = true;

    protected $fillable = [
        'nombre_solicitante',
        'departamento',
        'plataforma',
        'embarcacion',
        'proyecto',
        'sit',
        'partida',
        'area',
        'activo',
        'tipo_requerimiento',
        'comentario',
        'estatus',
        'user_id',
        'contrato_id',
    ];

    protected $attributes = [
        'estatus' => 'pendiente'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔹 GENERAR FOLIO AUTOMÁTICAMENTE
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($requisicion) {
            $requisicion->folio = self::generarFolio($requisicion);
        });
    }

    public static function generarFolio($requisicion)
    {
        $user = auth()->user();
        $rol = strtoupper($user->role ?? 'USER');
        $mes = date('m');
        $año = date('y');
        $ultimoId = self::max('id') ?? 0;
        $nuevoId = $ultimoId + 1;
        
        return "{$rol}/{$mes}/{$año}-{$nuevoId}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    // ✅ Relación con los detalles (múltiples materiales)
    public function detalles()
    {
        return $this->hasMany(RequisicionDetalle::class);
    }

    // Método para obtener el número total de materiales diferentes
    public function getTotalMaterialesAttribute()
    {
        return $this->detalles->count();
    }

    // Método para obtener el total de unidades solicitadas
    public function getTotalUnidadesAttribute()
    {
        return $this->detalles->sum('cantidad');
    }

    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estatus', 'aprobado');
    }

    public function scopeDenegadas($query)
    {
        return $query->where('estatus', 'denegado');
    }
}