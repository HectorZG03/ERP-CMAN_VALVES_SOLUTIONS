<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Personal</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .titulo {
            margin-bottom: 10px;
            font-size: 18px;
            text-align: center;
        }

        .sub {
            margin-bottom: 20px;
            color: #666;
            text-align: center;
        }

        .texto-centro {
            text-align: center;
        }

        .texto-derecha {
            text-align: right;
        }

        .resumen td {
            width: 33.33%;
        }
    </style>
</head>

<body>
@php
    $nombreEstado = static function ($datosLaborales): string {
        return $datosLaborales?->estadoLaboral?->nombre
            ?? $datosLaborales?->estadoLaboral?->clave
            ?? 'N/A';
    };
@endphp

<div class="titulo">
    LISTADO DE PERSONAL CMAN VALVES SOLUTIONS
</div>

<div class="sub">
    Generado: {{ $fechaGeneracion->format('d/m/Y H:i') }}
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Área</th>
            <th>Departamento</th>
            <th>Puesto</th>
            <th>Sexo</th>
            <th>Estado laboral</th>
            <th>Sueldo</th>
        </tr>
    </thead>

    <tbody>
        @forelse($personal as $p)
            @php
                $datosLaborales = $p->datosLaborales;
                $puesto = $datosLaborales?->puesto;
                $departamento = $puesto?->departamento;
                $area = $departamento?->area;
            @endphp

            <tr>
                <td>{{ $datosLaborales?->employee_id ?? 'N/A' }}</td>
                <td>{{ $p->nombre_mostrar }}</td>
                <td>{{ $area?->nombre ?? 'N/A' }}</td>
                <td>{{ $departamento?->nombre ?? 'N/A' }}</td>
                <td>{{ $puesto?->nombre ?? 'N/A' }}</td>
                <td>{{ $p->sexo ?? 'N/A' }}</td>
                <td>{{ $nombreEstado($datosLaborales) }}</td>
                <td class="texto-derecha">
                    @if($datosLaborales?->sueldo !== null)
                        ${{ number_format((float) $datosLaborales->sueldo, 2) }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="texto-centro">
                    No hay personal para mostrar.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

<table class="resumen">
    <tr>
        <td><strong>Total activos:</strong> {{ $totalActivo }}</td>
        <td><strong>Total bajas:</strong> {{ $totalBaja }}</td>
        <td>
            <strong>Total sueldos:</strong>
            ${{ number_format((float) $totalSueldos, 2) }}
        </td>
    </tr>
</table>

</body>
</html>