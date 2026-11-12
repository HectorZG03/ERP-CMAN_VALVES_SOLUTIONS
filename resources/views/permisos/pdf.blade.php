<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de permiso de ausencia</title>

    <style>
        @page {
            size: letter portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 8.5in;
            height: 11in;
            margin: 0;
            padding: 0;
            color: #272727;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .membrete {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 0;
            width: 8.5in;
            height: 11in;
        }

        .contenido {
            position: relative;
            z-index: 1;
            width: 7.2in;
            margin: 0 auto;
            padding: 1.25in 0 0.85in;
        }

        .titulo {
            margin: 0;
            color: #555;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .codigo-formato {
            margin-top: 4px;
            color: #777;
            font-size: 8px;
            text-align: center;
        }

        .fecha-documento {
            width: 100%;
            margin: 22px 0 24px;
            color: #555;
            font-size: 10px;
            text-align: right;
            white-space: nowrap;
        }

        .datos-permiso {
            margin-top: 4px;
        }

        .dato-linea {
            margin: 0 0 12px;
        }

        .datos-doble {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .datos-doble td {
            width: 50%;
            padding: 0 0 12px;
            vertical-align: top;
        }

        .motivo {
            overflow-wrap: break-word;
        }

        .declaracion {
            margin: 20px 0 16px;
            font-size: 10px;
            text-align: justify;
        }

        .opciones-goce {
            width: 100%;
            margin: 10px 0 22px;
            border-collapse: collapse;
        }

        .opciones-goce td {
            width: 50%;
            padding: 6px 10px;
            text-align: center;
        }

        .casilla {
            display: inline-block;
            width: 13px;
            height: 13px;
            margin-right: 5px;
            border: 1px solid #555;
            line-height: 12px;
            text-align: center;
            vertical-align: middle;
        }

        .firmas {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .firmas td {
            width: 50%;
            padding: 0 14px;
            vertical-align: top;
        }

        .encabezado-firma {
            min-height: 18px;
            margin-bottom: 4px;
            font-size: 10px;
            text-align: center;
        }

        .espacio-firma {
            height: 74px;
            text-align: center;
        }

        .imagen-firma {
            max-width: 165px;
            max-height: 62px;
        }

        .linea-firma {
            border-top: 1px solid #555;
            padding-top: 5px;
            text-align: center;
        }

        .nombre-firmante {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .puesto-firmante {
            margin-top: 3px;
            font-size: 9px;
        }

        .firma-direccion {
            width: 60%;
            margin: 24px auto 0;
        }

        .firma-direccion .espacio-firma {
            height: 74px;
        }
    </style>
</head>
<body>
@php
    $rutasMembrete = [
        public_path('img/membretado_cman.jpg'),
        public_path('img/logo/membretado_cman.jpg'),
    ];

    $membreteDataUri = null;

    foreach ($rutasMembrete as $rutaMembrete) {
        if (! is_file($rutaMembrete)) {
            continue;
        }

        $mimeMembrete = mime_content_type($rutaMembrete) ?: 'image/jpeg';
        $membreteDataUri = 'data:' . $mimeMembrete . ';base64,' . base64_encode(
            file_get_contents($rutaMembrete)
        );
        break;
    }

    $personal = $permiso->personal;
    $datosLaborales = $personal?->datosLaborales;

    $nombrePersonal = $personal?->nombre_mostrar ?? 'Personal no disponible';
    $numeroEmpleado = $datosLaborales?->employee_id ?? '—';
    $puesto = $datosLaborales?->puesto?->nombre ?? '—';

    $horas = rtrim(
        rtrim(number_format((float) $permiso->horas_solicitadas, 2, '.', ''), '0'),
        '.'
    );

    $tieneGoce = (bool) $permiso->goce_sueldo;
@endphp

@if($membreteDataUri)
    <img class="membrete" src="{{ $membreteDataUri }}" alt="">
@endif

<main class="contenido">
    <h1 class="titulo">Solicitud de permiso de ausencia</h1>
    <div class="codigo-formato">FOR-21-PRO-RH-02</div>

    <p class="fecha-documento">Villahermosa, Tabasco, a {{ $fechaGeneracion }}</p>

    <div class="datos-permiso">
        <p class="dato-linea"><strong>Nombre:</strong>&nbsp;{{ $nombrePersonal }}</p>

        <table class="datos-doble">
            <tr>
                <td><strong>No. de empleado:</strong>&nbsp;{{ $numeroEmpleado }}</td>
                <td><strong>Puesto:</strong>&nbsp;{{ $puesto }}</td>
            </tr>
        </table>

        <p class="dato-linea"><strong>Vigencia del permiso:</strong>&nbsp;{{ $permiso->fecha_permiso?->format('d/m/Y') ?? '—' }}</p>

        <p class="dato-linea"><strong>Total de horas autorizadas:</strong>&nbsp;{{ $horas }} {{ $horas == 1 ? 'hora' : 'horas' }}</p>

        <p class="dato-linea motivo"><strong>Motivo de ausencia:</strong>&nbsp;{{ $permiso->motivo }}</p>
    </div>

    <p class="declaracion">
        Con relación al permiso de ausencia que se me otorga, deslindo a la empresa
        CMAN GLOBAL CONSTRUCTION, S.A. de C.V. de toda responsabilidad por los actos
        que realice durante este periodo de ausencia.
    </p>

    <table class="opciones-goce">
        <tr>
            <td>
                <span class="casilla">{{ $tieneGoce ? 'X' : '' }}</span>
                Con goce de sueldo
            </td>
            <td>
                <span class="casilla">{{ $tieneGoce ? '' : 'X' }}</span>
                Sin goce de sueldo
            </td>
        </tr>
    </table>

    <table class="firmas">
        <tr>
            <td>
                <div class="encabezado-firma"><strong>SOLICITA</strong></div>
                <div class="espacio-firma">
                    @if($firmaSolicitante)
                        <img
                            class="imagen-firma"
                            src="{{ $firmaSolicitante }}"
                            alt="Firma de quien solicita"
                        >
                    @endif
                </div>
                <div class="linea-firma">
                    <div class="nombre-firmante">{{ $nombrePersonal }}</div>
                    <div class="puesto-firmante">{{ $puesto }}</div>
                </div>
            </td>

            <td>
                <div class="encabezado-firma"><strong>Vo. Bo.</strong></div>
                <div class="espacio-firma">
                    @if($firmaRh)
                        <img
                            class="imagen-firma"
                            src="{{ $firmaRh }}"
                            alt="Firma de {{ $rolRevisor }}"
                        >
                    @endif
                </div>
                <div class="linea-firma">
                    <div class="nombre-firmante">{{ $permiso->revisadoPor?->name ?? $rolRevisor }}</div>
                    <div class="puesto-firmante">{{ $rolRevisor }}</div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="firma-direccion">
                    <div class="encabezado-firma"><strong>AUTORIZA</strong></div>
                    <div class="espacio-firma">
                        @if($firmaDireccion)
                            <img
                                class="imagen-firma"
                                src="{{ $firmaDireccion }}"
                                alt="Firma de Dirección"
                            >
                        @endif
                    </div>
                    <div class="linea-firma">
                        <div class="nombre-firmante">{{ $permiso->resueltoPor?->name ?? 'Dirección' }}</div>
                        <div class="puesto-firmante">Director</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</main>
</body>
</html>
