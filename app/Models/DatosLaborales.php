<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatosLaborales extends Model
{
    use HasFactory;

    protected $table = 'datos_laborales';

    protected $fillable = [
        'personal_id',
        'puesto_id',
        'estado_laboral_id',
        'employee_id',
        'fecha_ingreso',
        'sueldo',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'sueldo' => 'decimal:2',
    ];

    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }

    public function estadoLaboral(): BelongsTo
    {
        return $this->belongsTo(EstadoLaboral::class, 'estado_laboral_id');
    }
}