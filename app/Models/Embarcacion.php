<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embarcacion extends Model
{
    use HasFactory;

    protected $table = 'embarcaciones';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Requisiciones relacionadas con la embarcación.
     */
    public function requisiciones()
    {
        return $this->hasMany(
            Requisicion::class,
            'embarcacion_id'
        );
    }

    /**
     * Solicitudes de material relacionadas.
     */
    public function solicitudesMaterial()
    {
        return $this->hasMany(
            SolicitudMaterial::class,
            'embarcacion_id'
        );
    }
}