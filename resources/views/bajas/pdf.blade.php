<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Bajas de Colaboradores</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2c5282;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #4a5568;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 9px;
            color: #718096;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            text-align: center;
        }
        .summary-item {
            padding: 10px;
        }
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c5282;
        }
        .summary-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #2c5282;
            color: white;
            text-align: left;
            padding: 8px;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .baja-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
        }
        .baja-activa {
            background-color: #feb2b2;
            color: #c53030;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #718096;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
        .signature {
            margin-top: 30px;
            text-align: right;
            padding-right: 50px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 40px;
            margin-left: auto;
            margin-right: 50px;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE BAJAS DE COLABORADORES</h1>
        <h2>CMAN VALVES SOLUTIONS</h2>
    </div>

    <div class="company-info">
        <strong>Sistema de Gestión de Recursos Humanos</strong><br>
        Generado el {{ $fechaGeneracion->format('d/m/Y H:i:s') }}
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $totalBajas }}</div>
                <div class="summary-label">Total de Bajas</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $bajas->where('personal.estatus', 'baja')->count() }}</div>
                <div class="summary-label">Colaboradores Inactivos</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $bajas->unique('personal_id')->count() }}</div>
                <div class="summary-label">Colaboradores Únicos</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ID Baja</th>
                <th>Colaborador</th>
                <th>Área / Departamento</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Baja</th>
                <th>Motivo</th>
                <th>Registrada por</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bajas as $index => $baja)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>B{{ str_pad($baja->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $baja->personal->nombre_completo }}</td>
                <td>{{ $baja->personal->area }}<br><small>{{ $baja->personal->departamento }}</small></td>
                <td>{{ $baja->personal->fecha_ingreso->format('d/m/Y') }}</td>
                <td>{{ $baja->fecha_baja->format('d/m/Y') }}</td>
                <td>
                    @php
                        $motivo = str_replace(["\r\n", "\r", "\n"], ' ', $baja->motivo_baja);
                        $motivo = wordwrap($motivo, 30, "<br>", true);
                    @endphp
                    {!! $motivo !!}
                </td>
                <td>{{ $baja->user->name ?? 'Sistema' }}</td>
                <td>
                    <span class="baja-status baja-activa">BAJA</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Resumen por Área</h3>
        <table>
            <thead>
                <tr>
                    <th>Área</th>
                    <th>Total Bajas</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $areas = $bajas->groupBy('personal.area')->map(function($items, $area) use ($totalBajas) {
                        return [
                            'count' => $items->count(),
                            'percentage' => $totalBajas > 0 ? ($items->count() / $totalBajas * 100) : 0
                        ];
                    })->sortByDesc('count');
                @endphp
                @foreach($areas as $area => $data)
                <tr>
                    <td>{{ $area }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ number_format($data['percentage'], 1) }}%</td>
                </tr>
                @endforeach
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td><strong>{{ $totalBajas }}</strong></td>
                    <td><strong>100%</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p style="margin-right: 50px; text-align: center; font-size: 9px;">Firma de Autorización</p>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de RH - CMAN VALVES SOLUTIONS</p>
        <p>Página 1 de 1 | Reporte de Bajas | Confidencial</p>
    </div>
</body>
</html>