<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'orden_compras';

    protected $fillable = [
        'folio',
        'nombre_proveedor',
        'direccion_proveedor',
        'telefono_proveedor',
        'email_proveedor',
        'envio',
        'otros',
        'subtotal',
        'iva',
        'total_general',
        'comentarios',
        'user_id',
    ];

    protected $casts = [
        'envio'         => 'float',
        'otros'         => 'float',
        'subtotal'      => 'float',
        'iva'           => 'float',
        'total_general' => 'float',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function detalles()
    {
        return $this->hasMany(OrdenCompraDetalle::class, 'orden_compra_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Genera el siguiente folio correlativo: OC-YYYY-XXXX
     */
    public static function generarFolio(): string
    {
        $year   = date('Y');
        $ultimo = self::whereYear('created_at', $year)->max('id');
        $numero = str_pad(($ultimo ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        return "OC-{$year}-{$numero}";
    }

    /**
     * Recalcula subtotal, IVA y total_general a partir de los detalles guardados.
     * Llama a este método tras insertar / actualizar detalles.
     */
    public function recalcularTotales(): void
    {
        $subtotal       = $this->detalles()->sum('total');
        $iva            = round($subtotal * 0.16, 2);
        $total_general  = $subtotal + $iva + $this->envio + $this->otros;

        $this->update([
            'subtotal'      => $subtotal,
            'iva'           => $iva,
            'total_general' => $total_general,
        ]);
    }
}