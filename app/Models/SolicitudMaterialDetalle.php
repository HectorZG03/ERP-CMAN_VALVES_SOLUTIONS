<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudMaterialDetalle extends Model
{
    use HasFactory;

    protected $table = 'solicitud_material_detalles';

    protected $fillable = [
        'solicitud_material_id',
        'inventario_id',
        'cantidad_solicitada',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'integer',
        'precio_unitario' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Solicitud principal.
     */
    public function solicitudMaterial()
    {
        return $this->belongsTo(SolicitudMaterial::class);
    }

    /**
     * Producto del inventario.
     */
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    /**
     * Asignaciones realizadas mediante vales EPP.
     */
    public function asignacionesValeEpp()
    {
        return $this->hasMany(
            ValeppDetalle::class,
            'solicitud_material_detalle_id'
        );
    }

    /**
     * Subtotal solicitado.
     */
    public function getSubtotalAttribute()
    {
        return $this->cantidad_solicitada
            * ($this->precio_unitario ?? 0);
    }

    /**
     * Cantidad asignada entre todos los vales EPP.
     */
    public function getCantidadAsignadaEppAttribute(): int
    {
        $atributoSuma = 'asignaciones_vale_epp_sum_cantidad';

        if (array_key_exists($atributoSuma, $this->attributes)) {
            return (int) ($this->attributes[$atributoSuma] ?? 0);
        }

        if ($this->relationLoaded('asignacionesValeEpp')) {
            return (int) $this->asignacionesValeEpp->sum(
                'cantidad'
            );
        }

        return (int) $this->asignacionesValeEpp()
            ->sum('cantidad');
    }

    /**
     * Cantidad que todavía puede asignarse a nuevos vales EPP.
     */
    public function getCantidadDisponibleEppAttribute(): int
    {
        return max(
            0,
            (int) $this->cantidad_solicitada
                - $this->cantidad_asignada_epp
        );
    }

    /**
     * Filtrar por producto del inventario.
     */
    public function scopeProducto($query, $inventarioId)
    {
        return $query->where(
            'inventario_id',
            $inventarioId
        );
    }

    /**
     * Verificar la existencia actual del inventario.
     */
    public function verificarDisponibilidad(): bool
    {
        if (!$this->inventario) {
            return false;
        }

        return $this->inventario->existencia
            >= $this->cantidad_solicitada;
    }
}