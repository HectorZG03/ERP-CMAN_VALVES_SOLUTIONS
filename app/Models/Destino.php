<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    use HasFactory;

    protected $table = 'destinos';
    
    protected $fillable = [
        'nombre',
    ];

    public function requisiciones()
    {
        return $this->hasMany(Requisicion::class, 'destino_id');
    }

    public function solicitudesMaterial()
    {
        return $this->hasMany(SolicitudMaterial::class, 'destino_id');
    }
    
}
