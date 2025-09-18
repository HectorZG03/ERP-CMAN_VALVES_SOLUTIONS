<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'area',
        'cedula',
        'nombre',
        'email',
    ];

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }
}