<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';

    protected $fillable = [
        'empresa_nombre',
        'contrato',
        'convenio',
    ];

    public function requisiciones()
    {
        return $this->hasMany(Requisicion::class);
    }
}
