<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'numero_factura_proveedor',
        'proveedor_id',
        'fecha_entrada',
        'observaciones',
        'subtotal',
        'descuento_global',
        'iva',
        'total',
        'user_id',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'cantidad_total' => 'integer',
        'precio_unitario_promedio' => 'decimal:2',
        'precio_total' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'iva' => 'decimal:2',
        'total_con_iva' => 'decimal:2',
    ];

    private const TASA_IVA = 0.16;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_factura)) {
                $year = date('Y');
                $count = Entrada::whereYear('created_at', $year)->count() + 1;

                $model->numero_factura = 'ENT-'
                    . $year
                    . '-'
                    . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($model) {
            $model->calcularTotalesDesdeDetalles();
        });

        static::updated(function ($model) {
            if ($model->isDirty() && $model->detalles()->exists()) {
                $model->calcularTotalesDesdeDetalles();
            }
        });
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(EntradaDetalle::class);
    }

    /**
     * Calcula y guarda los totales de la cabecera.
     *
     * El descuento es global: no modifica precios ni subtotales
     * individuales de los detalles.
     */
    public function calcularTotalesDesdeDetalles(): void
    {
        $detalles = $this->detalles()->get();

        if ($detalles->isEmpty()) {
            $this->cantidad_total = 0;
            $this->precio_unitario_promedio = 0;
            $this->precio_total = 0;
            $this->iva = 0;
            $this->total_con_iva = 0;

            $this->saveQuietly();

            return;
        }

        $cantidadTotal = (int) $detalles->sum('cantidad');
        $subtotal = round((float) $detalles->sum('precio_total'), 2);
        $descuento = round((float) ($this->descuento_global ?? 0), 2);
        $subtotalConDescuento = round(max(0, $subtotal - $descuento), 2);

        /*
         * Sin descuento se conserva el IVA sumado desde los detalles
         * para mantener los importes actuales de entradas existentes.
         * Con descuento, se calcula el IVA sobre la base descontada.
         */
        $iva = $descuento > 0
            ? round($subtotalConDescuento * self::TASA_IVA, 2)
            : round((float) $detalles->sum('iva'), 2);

        $this->cantidad_total = $cantidadTotal;
        $this->precio_total = $subtotal;
        $this->iva = $iva;
        $this->total_con_iva = round($subtotalConDescuento + $iva, 2);

        // El descuento global no cambia el costo unitario de los productos.
        $this->precio_unitario_promedio = $cantidadTotal > 0
            ? round($subtotal / $cantidadTotal, 2)
            : 0;

        $this->saveQuietly();
    }

    /**
     * Base de la entrada después de restar el descuento global.
     */
    public function getSubtotalConDescuentoAttribute(): float
    {
        return round(
            max(0, (float) $this->precio_total - (float) $this->descuento_global),
            2
        );
    }

    // Alias para compatibilidad con vistas existentes.
    public function getCantidadAttribute()
    {
        return $this->cantidad_total;
    }

    public function getPrecioUnitarioAttribute()
    {
        return $this->precio_unitario_promedio;
    }

    public function getProductosAttribute()
    {
        return $this->detalles->map(function ($detalle) {
            return [
                'producto' => $detalle->inventario->nombre_producto ?? 'Producto',
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'precio_total' => $detalle->precio_total,
                'iva' => $detalle->iva,
                'total_con_iva' => $detalle->total_con_iva,
            ];
        });
    }

    public function getCantidadTotalAttribute()
    {
        if ($this->relationLoaded('detalles') && $this->detalles->count() > 0) {
            return $this->detalles->sum('cantidad');
        }

        return $this->attributes['cantidad_total'] ?? 0;
    }

    public function getCantidadProductosAttribute()
    {
        return $this->detalles->count();
    }

    /**
     * El subtotal de líneas se conserva sin restarle el descuento.
     */
    public function getPrecioTotalAttribute()
    {
        if ($this->relationLoaded('detalles') && $this->detalles->count() > 0) {
            return $this->detalles->sum('precio_total');
        }

        return $this->attributes['precio_total'] ?? 0;
    }

    /**
     * El IVA de cabecera puede diferir del IVA de los renglones
     * cuando existe descuento global.
     */
    public function getIvaAttribute()
    {
        return $this->attributes['iva'] ?? 0;
    }

    /**
     * Devuelve el total final guardado en la cabecera.
     */
    public function getTotalConIvaAttribute()
    {
        return $this->attributes['total_con_iva'] ?? 0;
    }
}