<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria',
        'nombre_producto',
        'medida',
        'existencia',
        'precio_total',
    ];

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    public function solicitudesMateriales()
    {
        return $this->hasMany(SolicitudMaterial::class);
    }

    public function prestamosMateriales()
    {
        return $this->hasMany(PrestamoMaterial::class);
    }

    public function inventarioBarcos()
    {
        return $this->hasMany(InventarioBarco::class);
    }

    public function getPrecioPromedio()
    {
        return $this->existencia > 0 ? $this->precio_total / $this->existencia : 0;
    }
}