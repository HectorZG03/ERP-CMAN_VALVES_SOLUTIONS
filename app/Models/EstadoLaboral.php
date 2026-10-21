<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoLaboral extends Model
{
    use HasFactory;

    protected $table = 'estados_laborales';

    protected $fillable = [
        'clave',
        'nombre',
        'es_final',
    ];

    protected $casts = [
        'es_final' => 'boolean',
    ];

    public function datosLaborales(): HasMany
    {
        return $this->hasMany(DatosLaborales::class, 'estado_laboral_id');
    }
}