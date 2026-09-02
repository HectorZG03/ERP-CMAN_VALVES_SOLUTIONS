<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'cliente_id',
        'solicitud_material_id',
        'fecha_salida',
        'observaciones',
        'cantidad_total',
        'precio_unitario_promedio',
        'precio_total',
        'iva',
        'total_con_iva',
        'user_id',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'cantidad_total' => 'integer',
        'precio_unitario_promedio' => 'decimal:2',
        'precio_total' => 'decimal:2',
        'iva' => 'decimal:2',
        'total_con_iva' => 'decimal:2',
    ];

    // Generar número de factura automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_factura)) {
                $year = date('Y');
                $count = Salida::whereYear('created_at', $year)->count() + 1;
                $model->numero_factura = 'SAL-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($model) {
            // Recalcular totales después de crear
            $model->calcularTotalesDesdeDetalles();
        });

        static::updated(function ($model) {
            // Si se actualizan los detalles, recalcular totales
            if ($model->isDirty() && $model->detalles()->exists()) {
                $model->calcularTotalesDesdeDetalles();
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function solicitudMaterial()
    {
        return $this->belongsTo(SolicitudMaterial::class, 'solicitud_material_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(SalidaDetalle::class);
    }

    // Calcular totales de la salida basado en detalles
    public function calcularTotalesDesdeDetalles()
    {
        $this->load('detalles'); // Asegurar que los detalles estén cargados
        
        if ($this->detalles->count() > 0) {
            // Calcular desde los detalles
            $this->cantidad_total = $this->detalles->sum('cantidad');
            $this->precio_total = $this->detalles->sum('precio_total');
            $this->iva = $this->detalles->sum('iva');
            $this->total_con_iva = $this->detalles->sum('total_con_iva');
            
            // Para precio unitario promedio
            if ($this->cantidad_total > 0) {
                $this->precio_unitario_promedio = $this->precio_total / $this->cantidad_total;
            } else {
                $this->precio_unitario_promedio = 0;
            }
        } else {
            // Si no hay detalles, poner valores en cero
            $this->cantidad_total = 0;
            $this->precio_unitario_promedio = 0;
            $this->precio_total = 0;
            $this->iva = 0;
            $this->total_con_iva = 0;
        }
        
        // Guardar sin disparar eventos para evitar bucle infinito
        $this->saveQuietly();
    }

    // Alias para compatibilidad con vistas existentes
    public function getCantidadAttribute()
    {
        return $this->cantidad_total;
    }

    public function getPrecioUnitarioAttribute()
    {
        return $this->precio_unitario_promedio;
    }

    // Obtener productos asociados
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

    // Obtener cantidad total de productos
    public function getCantidadTotalAttribute()
    {
        if ($this->relationLoaded('detalles') && $this->detalles->count() > 0) {
            return $this->detalles->sum('cantidad');
        } else {
            return $this->attributes['cantidad_total'] ?? 0;
        }
    }

    // Obtener cantidad de productos diferentes
    public function getCantidadProductosAttribute()
    {
        return $this->detalles->count();
    }

    // Accessor para precio_total que siempre se calcule desde detalles
    public function getPrecioTotalAttribute()
    {
        if ($this->relationLoaded('detalles') && $this->detalles->count() > 0) {
            return $this->detalles->sum('precio_total');
        } else {
            return $this->attributes['precio_total'] ?? 0;
        }
    }

    // Accessor para iva que siempre se calcule desde detalles
    public function getIvaAttribute()
    {
        if ($this->relationLoaded('detalles') && $this->detalles->count() > 0) {
            return $this->detalles->sum('iva');
        } else {
            return $this->attributes['iva'] ?? 0;
        }
    }

    // Accessor para total_con_iva que siempre se calcule desde detalles
    public function getTotalConIvaAttribute()
    {
        if ($this->relationLoaded('detalles') && $this->detalles->count() > 0) {
            return $this->detalles->sum('total_con_iva');
        } else {
            return $this->attributes['total_con_iva'] ?? 0;
        }
    }
}