<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'personal';

    protected $appends = ['nombre_mostrar'];

    protected $fillable = [
        'nombre',
        'ape_pat',
        'ape_mat',
        'foto',
        'employee_id',
        'edad',
        'nacionalidad',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'grupo_sanguineo',
        'curp',
        'rfc',
        'nss',
        'clave_interbancaria',
        'direccion',
        'correo_electronico',
        'numero_telefonico',
        'nombre_contacto_emergencia',
        'numero_telefonico_emergencia',
        'area',
        'departamento',
        'fecha_ingreso',
        'sueldo',
        'bonos',
        'grado',
        'estatus',
        'enfermedad_alergia',
        'division',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Nombre compuesto para mostrar, generado desde las columnas actuales.
     */
    public function getNombreMostrarAttribute(): string
    {
        $partes = array_filter(array_map(
            function ($campo) {
                return trim((string) ($this->attributes[$campo] ?? ''));
            },
            ['nombre', 'ape_pat', 'ape_mat']
        ));

        return implode(' ', $partes);
    }
    // Relaciones
    public function bajas()
    {
        return $this->hasMany(BajaColaborador::class);
    }

    public function cambiosPuestoSueldo()
    {
        return $this->hasMany(CambioPuestoSueldo::class);
    }

    public function valepp()
    {
        return $this->hasMany(Valepp::class);
    }

    // Obtener la última baja
    public function ultimaBaja()
    {
        return $this->hasOne(BajaColaborador::class)->latestOfMany();
    }

    // Obtener el último cambio de puesto/sueldo
    public function ultimoCambio()
    {
        return $this->hasOne(CambioPuestoSueldo::class)->latestOfMany();
    }

    // Scope para personal activo
    public function scopeActivo($query)
    {
        return $query->where('estatus', 'activo');
    }

    // Scope para personal dado de baja
    public function scopeBaja($query)
    {
        return $query->where('estatus', 'baja');
    }

    // Accessor para calcular edad automáticamente si hay fecha de nacimiento
    public function getEdadCalculadaAttribute()
    {
        if ($this->fecha_nacimiento && $this->fecha_nacimiento !== 'N/A') {
            return Carbon::parse($this->fecha_nacimiento)->age;
        }

        return $this->edad ?? 'N/A';
    }

    // Mutator para asegurar que los valores vacíos se guarden como N/A
    public function setAttribute($key, $value)
    {
        $naFields = [
            'foto',
            'employee_id',
            'nacionalidad',
            'estado_civil',
            'grupo_sanguineo',
            'curp',
            'rfc',
            'nss',
            'clave_interbancaria',
            'direccion',
            'correo_electronico',
            'numero_telefonico',
            'nombre_contacto_emergencia',
            'numero_telefonico_emergencia',
            'enfermedad_alergia',
            'grado',
        ];

        if (in_array($key, $naFields) && empty($value)) {
            $value = 'N/A';
        }

        parent::setAttribute($key, $value);
    }
}