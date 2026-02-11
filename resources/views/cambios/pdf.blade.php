<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambios de Puesto y Sueldo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ec4899;
        }

        .header h1 {
            color: #ec4899;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 9px;
        }

        .stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 8px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .stat-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #ec4899;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #ec4899;
            color: white;
        }

        thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody td {
            padding: 6px 5px;
            font-size: 9px;
        }

        .colaborador-info {
            font-weight: bold;
            color: #111827;
        }

        .area-info {
            color: #6b7280;
            font-size: 8px;
        }

        .puesto-anterior {
            color: #9ca3af;
            text-decoration: line-through;
            font-size: 8px;
        }

        .puesto-nuevo {
            color: #059669;
            font-weight: bold;
        }

        .sueldo-anterior {
            color: #9ca3af;
            text-decoration: line-through;
            font-size: 8px;
        }

        .sueldo-nuevo {
            color: #059669;
            font-weight: bold;
        }

        .porcentaje {
            font-size: 7px;
            color: #059669;
        }

        .porcentaje.negativo {
            color: #dc2626;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Reporte de Cambios de Puesto y Sueldo</h1>
        <p>Generado el {{ $fechaGeneracion->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Estadísticas -->
    <div class="stats">
        <div class="stat-item">
            <div class="stat-label">Total de Cambios</div>
            <div class="stat-value">{{ $totalCambios }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Este Mes</div>
            <div class="stat-value">
                {{ $cambios->filter(function($c) { 
                    return $c->fecha_cambio->month == date('m') && $c->fecha_cambio->year == date('Y'); 
                })->count() }}
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Este Año</div>
            <div class="stat-value">
                {{ $cambios->filter(function($c) { 
                    return $c->fecha_cambio->year == date('Y'); 
                })->count() }}
            </div>
        </div>
    </div>

    <!-- Tabla de Cambios -->
    @if($cambios->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Colaborador</th>
                    <th style="width: 20%;">Puesto</th>
                    <th style="width: 20%;">Sueldo</th>
                    <th style="width: 12%;">Fecha Cambio</th>
                    <th style="width: 23%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cambios as $index => $cambio)
                    <tr>
                        <!-- # -->
                        <td style="text-align: center;">{{ $index + 1 }}</td>

                        <!-- Colaborador -->
                        <td>
                            <div class="colaborador-info">{{ $cambio->personal->nombre_completo }}</div>
                            <div class="area-info">{{ $cambio->personal->area }}</div>
                        </td>

                        <!-- Puesto -->
                        <td>
                            <div class="puesto-anterior">{{ $cambio->puesto_anterior }}</div>
                            <div class="puesto-nuevo">→ {{ $cambio->puesto_nuevo }}</div>
                        </td>

                        <!-- Sueldo -->
                        <td>
                            <div class="sueldo-anterior">${{ number_format($cambio->sueldo_anterior, 2) }}</div>
                            <div class="sueldo-nuevo">→ ${{ number_format($cambio->sueldo_nuevo, 2) }}</div>
                            @php
                                $diferencia = $cambio->sueldo_nuevo - $cambio->sueldo_anterior;
                                $porcentaje = $cambio->sueldo_anterior > 0 ? ($diferencia / $cambio->sueldo_anterior * 100) : 0;
                            @endphp
                            <div class="porcentaje {{ $diferencia < 0 ? 'negativo' : '' }}">
                                {{ $diferencia > 0 ? '+' : '' }}{{ number_format($porcentaje, 1) }}%
                            </div>
                        </td>

                        <!-- Fecha Cambio -->
                        <td style="text-align: center;">
                            {{ $cambio->fecha_cambio->format('d/m/Y') }}
                        </td>

                        <!-- Observaciones -->
                        <td>
                            <div style="font-size: 8px; color: #6b7280;">
                                {{ $cambio->observaciones ? Str::limit($cambio->observaciones, 80) : '-' }}
                            </div>
                        </td>
                    </tr>

                    <!-- Salto de página cada 25 registros -->
                    @if(($index + 1) % 25 == 0 && !$loop->last)
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        
                        <!-- Repetir encabezado -->
                        <div class="header">
                            <h1>Reporte de Cambios de Puesto y Sueldo (continuación)</h1>
                            <p>Generado el {{ $fechaGeneracion->format('d/m/Y H:i:s') }}</p>
                        </div>
                        
                        <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">Colaborador</th>
                                <th style="width: 20%;">Puesto</th>
                                <th style="width: 20%;">Sueldo</th>
                                <th style="width: 12%;">Fecha Cambio</th>
                                <th style="width: 23%;">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No hay cambios de puesto y sueldo registrados.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Sistema de Gestión de Recursos Humanos - CMAN Valves Solutions</p>
        <p>Este documento contiene información confidencial</p>
    </div>
</body>
</html>