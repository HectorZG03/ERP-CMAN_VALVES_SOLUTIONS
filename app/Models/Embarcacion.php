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

    public function requisiciones()
    {
        return $this->hasMany(Requisicion::class, 'embaracion_id');
    }

    public function solicitudesMaterial()
    {
        return $this->hasMany(SolicitudMaterial::class, 'embaracion_id');
    }
    
}
