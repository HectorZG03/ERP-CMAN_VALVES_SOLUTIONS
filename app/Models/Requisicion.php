<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisicion extends Model
{
    use HasFactory;

    protected $table = 'requisiciones';
    
    // Asegúrate de que los timestamps estén habilitados
    public $timestamps = true;

    protected $fillable = [
        'nombre_solicitante',
        'departamento',
        'plataforma',
        'embarcacion',
        'cantidad',
        'unidad',
        'material',
        'tipo_requerimiento',
        'comentario',
        'estatus',
        'user_id',
    ];

    // Definir valores por defecto
    protected $attributes = [
        'estatus' => 'pendiente'
    ];

    // Asegurar que las fechas se traten como Carbon instances
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes útiles
    public function scopePendientes($query)
    {
        return $query->where('estatus', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estatus', 'aprobado');
    }

    public function scopeDenegadas($query)
    {
        return $query->where('estatus', 'denegado');
    }
}