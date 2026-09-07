<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValeppDetalle extends Model
{
    use HasFactory;

    protected $table = 'valepp_detalles';

    protected $fillable = [
        'valepp_id',
        'solicitud_material_detalle_id',
        'inventario_id',
        'cantidad',
        'fecha_entrega',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'cantidad' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Vale EPP al que pertenece la asignación.
     */
    public function valepp()
    {
        return $this->belongsTo(Valepp::class);
    }

    /**
     * Renglón de la solicitud EPP del cual proviene el equipo.
     */
    public function solicitudMaterialDetalle()
    {
        return $this->belongsTo(
            SolicitudMaterialDetalle::class,
            'solicitud_material_detalle_id'
        );
    }

    /**
     * Producto del inventario.
     *
     * Se conserva temporalmente para mantener compatibilidad con las
     * vistas y registros anteriores.
     */
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}