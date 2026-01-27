<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valepp extends Model
{
    use HasFactory;

    protected $table = 'valepp';

    protected $fillable = [
        'numero_vale',
        'personal_id',
        'fecha_solicitud',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
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

    public function detalles()
    {
        return $this->hasMany(ValeppDetalle::class);
    }

    // Método para generar número de vale automáticamente
    public static function generarNumeroVale()
    {
        $ultimo = self::latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->numero_vale, 2)) + 1 : 1;
        return 'VP' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}