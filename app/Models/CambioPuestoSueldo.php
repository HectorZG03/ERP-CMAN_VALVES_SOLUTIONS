<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CambioPuestoSueldo extends Model
{
    use HasFactory;

    protected $table = 'cambios_puesto_sueldo';

    protected $fillable = [
        'personal_id',
        'puesto_anterior',
        'puesto_nuevo',
        'sueldo_anterior',
        'sueldo_nuevo',
        'fecha_cambio',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha_cambio' => 'date',
        'sueldo_anterior' => 'decimal:2',
        'sueldo_nuevo' => 'decimal:2',
    ];

    // Relaciones
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}