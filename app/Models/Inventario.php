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
        'economico',
        'medida',
        'ubicacion',
        'existencia',
        'precio_total',
    ];

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'inventario_id');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'inventario_id');
    }

    public function solicitudesMateriales()
    {
        // La columna inventario_id está correcta según tu migración
        return $this->hasMany(SolicitudMaterial::class, 'inventario_id');
    }

    public function prestamosMateriales()
    {
        return $this->hasMany(PrestamoMaterial::class, 'inventario_id');
    }

    public function inventarioBarcos()
    {
        return $this->hasMany(InventarioBarco::class, 'inventario_id');
    }

    public function getPrecioPromedio()
    {
        return $this->existencia > 0 ? $this->precio_total / $this->existencia : 0;
    }


    
    // Nueva relación con Valepp
    public function valeppDetalles()
    {
        return $this->hasMany(ValeppDetalle::class, 'inventario_id');
    }


}