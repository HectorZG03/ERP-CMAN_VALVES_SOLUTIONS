<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'personal';

    protected $fillable = [
        'nombre_completo',
        'area',
        'departamento',
        'fecha_ingreso',
        'sueldo',
        'grado',
        'estatus',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'sueldo' => 'decimal:2',
    ];

    // Relaciones
    public function bajas()
    {
        return $this->hasMany(BajaColaborador::class);
    }

    public function cambiosPuestoSueldo()
    {
        return $this->hasMany(CambioPuestoSueldo::class);
    }

    public function valepp()
    {
        return $this->hasMany(Valepp::class);
    }

    // Obtener la última baja
    public function ultimaBaja()
    {
        return $this->hasOne(BajaColaborador::class)->latestOfMany();
    }

    // Obtener el último cambio de puesto/sueldo
    public function ultimoCambio()
    {
        return $this->hasOne(CambioPuestoSueldo::class)->latestOfMany();
    }

    // Scope para personal activo
    public function scopeActivo($query)
    {
        return $query->where('estatus', 'activo');
    }

    // Scope para personal dado de baja
    public function scopeBaja($query)
    {
        return $query->where('estatus', 'baja');
    }
}