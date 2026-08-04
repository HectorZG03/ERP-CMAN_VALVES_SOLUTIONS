<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisicion extends Model
{
    use HasFactory;

    protected $table = 'requisiciones';

    public $timestamps = true;

    protected $fillable = [
        'nombre_solicitante',
        'departamento',
        'plataforma',

        // Campo de texto histórico
        'embarcacion',

        // Nueva relación con el catálogo
        'embarcacion_id',

        'proyecto',
        'sit',
        'partida',
        'area',
        'activo',
        'tipo_requerimiento',
        'comentario',
        'estatus',
        'estatus_finanzas',
        'aprobado_por_finanzas_id',
        'fecha_aprobacion_finanzas',
        'user_id',
        'contrato_id',
    ];

    protected $attributes = [
        'estatus' => 'pendiente',
        'estatus_finanzas' => 'pendiente',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'fecha_aprobacion_finanzas' => 'datetime',
    ];

    /**
     * Generar folio automáticamente.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($requisicion) {
            $requisicion->folio = self::generarFolio();
        });
    }

    /**
     * Generar folio de requisición.
     */
    public static function generarFolio()
    {
        $user = auth()->user();

        $rol = strtoupper($user->role ?? 'USER');
        $mes = date('m');
        $año = date('y');

        $ultimoId = self::max('id') ?? 0;
        $nuevoId = $ultimoId + 1;

        return "{$rol}/{$mes}/{$año}-{$nuevoId}";
    }

    /**
     * Usuario que creó la requisición.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Contrato relacionado.
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    /**
     * Embarcación seleccionada desde el catálogo.
     *
     * Se utiliza embarcacionCatalogo() porque ya existe
     * una columna llamada embarcacion.
     */
    public function embarcacionCatalogo()
    {
        return $this->belongsTo(
            Embarcacion::class,
            'embarcacion_id'
        );
    }

    /**
     * Usuario que aprobó en finanzas.
     */
    public function aprobadorFinanzas()
    {
        return $this->belongsTo(
            User::class,
            'aprobado_por_finanzas_id'
        );
    }

    /**
     * Materiales de la requisición.
     */
    public function detalles()
    {
        return $this->hasMany(
            RequisicionDetalle::class,
            'requisicion_id'
        );
    }

    /**
     * Número total de materiales diferentes.
     */
    public function getTotalMaterialesAttribute()
    {
        return $this->detalles->count();
    }

    /**
     * Total de unidades solicitadas.
     */
    public function getTotalUnidadesAttribute()
    {
        return $this->detalles->sum('cantidad');
    }

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

    public function scopePendientesFinanzas($query)
    {
        return $query->where('estatus_finanzas', 'pendiente');
    }

    public function scopeAprobadasPorFinanzas($query)
    {
        return $query->where('estatus_finanzas', 'aprobado');
    }

    public function scopeDenegadasPorFinanzas($query)
    {
        return $query->where('estatus_finanzas', 'denegado');
    }

    /**
     * Verificar si la requisición puede ser vista por Dirección.
     */
    public function puedeSerVistaPorDireccion()
    {
        return $this->estatus_finanzas === 'aprobado';
    }
}