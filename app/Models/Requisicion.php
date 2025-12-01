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
        // 'folio', // ❌ QUITA esto del fillable para que no se intente asignar manualmente
        'nombre_solicitante',
        'departamento',
        'plataforma',
        'embarcacion',
        'proyecto',
        'sit',
        'partida',
        'area',
        'activo',
        'cantidad',
        'unidad',
        'material',
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
        // Obtener el rol del usuario (asumiendo que tienes la relación user cargada)
        $user = auth()->user();
        $rol = strtoupper($user->role ?? 'USER'); // TI, ADMIN, etc.
        
        // Obtener mes y año actual
        $mes = date('m'); // 01-12
        $año = date('y'); // 25 para 2025
        
        // Obtener el último ID de la tabla para generar el consecutivo
        $ultimoId = self::max('id') ?? 0;
        $nuevoId = $ultimoId + 1;
        
        // Formato: ROL/MES/AÑO-ID
        return "{$rol}/{$mes}/{$año}-{$nuevoId}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

public function contrato()
{
    return $this->belongsTo(Contrato::class);
}



}