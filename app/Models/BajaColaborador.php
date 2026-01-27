<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajaColaborador extends Model
{
    use HasFactory;

    protected $table = 'bajas_colaboradores';

    protected $fillable = [
        'personal_id',
        'fecha_baja',
        'motivo_baja',
        'user_id',
    ];

    protected $casts = [
        'fecha_baja' => 'date',
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