<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destino extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'destinos';

    protected $fillable = [
        'nombre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Requisiciones que apuntan a este destino.
     */
    public function requisiciones()
    {
        return $this->hasMany(Requisicion::class);
    }

    /**
     * Solicitudes de material que apuntan a este destino.
     */
    public function solicitudesMaterial()
    {
        return $this->hasMany(SolicitudMaterial::class);
    }
}