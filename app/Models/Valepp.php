<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valepp extends Model
{
    use HasFactory;

    protected $table = 'valepp';

    protected $fillable = [
        'solicitud_material_id',
        'numero_vale',
        'personal_id',
        'fecha_solicitud',
        'estatus',
        'observaciones',
        'user_id',

    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Solicitud EPP que dio origen al vale.
     */
    public function solicitudMaterial()
    {
        return $this->belongsTo(
            SolicitudMaterial::class,
            'solicitud_material_id'
        );
    }

    /**
     * Trabajador que recibe el equipo.
     */
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    /**
     * Usuario de HSE que registró el vale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Equipos asignados en el vale.
     */
    public function detalles()
    {
        return $this->hasMany(ValeppDetalle::class);
    }

    /**
     * Generar el siguiente número de vale.
     */
    public static function generarNumeroVale(): string
    {
        $ultimoVale = self::query()
            ->latest('id')
            ->first();

        $numero = $ultimoVale
            ? (int) substr($ultimoVale->numero_vale, 2) + 1
            : 1;

        return 'VP' . str_pad(
            (string) $numero,
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}