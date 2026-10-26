<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'enfermedad_alergia',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Nombre compuesto para mostrar, generado desde sus columnas.
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

    public function bajas()
    {
        return $this->hasMany(BajaColaborador::class);
    }

    public function valepp()
    {
        return $this->hasMany(Valepp::class);
    }

    public function ultimaBaja()
    {
        return $this->hasOne(BajaColaborador::class)->latestOfMany();
    }

    /**
     * Scope para personal con estado laboral activo.
     */
    public function scopeActivo($query)
    {
        return $query->whereHas(
            'datosLaborales.estadoLaboral',
            function ($estadoQuery) {
                $estadoQuery->where('clave', 'activo');
            }
        );
    }

    /**
     * Scope para personal con estado laboral de baja.
     */
    public function scopeBaja($query)
    {
        return $query->whereHas(
            'datosLaborales.estadoLaboral',
            function ($estadoQuery) {
                $estadoQuery->where('clave', 'baja');
            }
        );
    }

    /**
     * Calcular la edad a partir de la fecha de nacimiento.
     */
    public function getEdadCalculadaAttribute()
    {
        if ($this->fecha_nacimiento) {
            return Carbon::parse($this->fecha_nacimiento)->age;
        }

        return $this->edad ?? 'N/A';
    }

    /**
     * Guardar ciertos campos vacíos como N/A.
     */
    public function setAttribute($key, $value)
    {
        $naFields = [
            'foto',
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
        ];

        if (in_array($key, $naFields, true) && empty($value)) {
            $value = 'N/A';
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Relación uno a uno con los datos laborales.
     */
    public function datosLaborales()
    {
        return $this->hasOne(DatosLaborales::class, 'personal_id');
    }
}