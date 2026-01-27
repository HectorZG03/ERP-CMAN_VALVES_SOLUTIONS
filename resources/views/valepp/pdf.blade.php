<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vale PP {{ $valepp->numero_vale }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c5282;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #4a5568;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 10px;
            color: #718096;
        }
        .vale-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .vale-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #4a5568;
        }
        .info-value {
            color: #2d3748;
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
        }
        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #718096;
            text-align: center;
        }
        .signatures {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        .page-break {
            page-break-before: always;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .badge-entregado {
            background-color: #c6f6d5;
            color: #22543d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VALE DE EQUIPO DE PROTECCIÓN PERSONAL</h1>
        <h2>Número: {{ $valepp->numero_vale }}</h2>
    </div>

    <div class="company-info">
        <strong>CMAN VALVES SOLUTIONS</strong><br>
        Sistema de Gestión de EPP
    </div>

    <div class="vale-info">
        <div class="vale-info-grid">
            <div class="info-item">
                <span class="info-label">Colaborador:</span><br>
                <span class="info-value">{{ $valepp->personal->nombre_completo ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Área/Departamento:</span><br>
                <span class="info-value">{{ $valepp->personal->area ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fecha de Solicitud:</span><br>
                <span class="info-value">{{ $valepp->fecha_solicitud->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Generado por:</span><br>
                <span class="info-value">{{ $valepp->user->name ?? 'Sistema' }}</span>
            </div>
        </div>
    </div>

    @if($valepp->observaciones)
    <div class="info-item">
        <span class="info-label">Observaciones:</span><br>
        <span class="info-value">{{ $valepp->observaciones }}</span>
    </div>
    @endif

    <h3>Materiales Entregados</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>Fecha Entrega</th>
            </tr>
        </thead>
        <tbody>
            @foreach($valepp->detalles as $index => $detalle)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detalle->inventario->nombre_producto ?? 'N/A' }}</td>
                <td>{{ $detalle->inventario->categoria ?? 'N/A' }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->fecha_entrega ? $detalle->fecha_entrega->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total de Materiales:</td>
                <td style="font-weight: bold;">{{ $valepp->detalles->sum('cantidad') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div>
            <div class="signature-line"></div>
            <strong>ENTREGADO POR</strong><br>
            (Almacén / HSE)
        </div>
        <div>
            <div class="signature-line"></div>
            <strong>RECIBIDO POR</strong><br>
            (Colaborador)
        </div>
        <div>
            <div class="signature-line"></div>
            <strong>AUTORIZADO POR</strong><br>
            (Supervisor / HSE)
        </div>
    </div>

    <div class="footer">
        <p>Documento generado el {{ $fechaGeneracion }} | CMAN VALVES SOLUTIONS - Sistema ERP</p>
        <p>Vale PP {{ $valepp->numero_vale }} | Página 1 de 1</p>
    </div>
</body>
</html>