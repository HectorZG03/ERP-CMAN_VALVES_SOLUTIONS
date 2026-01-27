<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Baja - {{ $baja->personal->nombre_completo }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
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
            margin-bottom: 30px;
            font-size: 10px;
            color: #718096;
        }
        .document-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .document-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }
        .info-label {
            font-weight: bold;
            color: #4a5568;
            font-size: 11px;
        }
        .info-value {
            color: #2d3748;
            font-size: 14px;
            font-weight: bold;
        }
        .section {
            margin: 25px 0;
        }
        .section-title {
            background-color: #2c5282;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 14px;
            border-radius: 4px 4px 0 0;
        }
        .section-content {
            border: 1px solid #e2e8f0;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 4px 4px;
        }
        .employee-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .item-label {
            font-weight: bold;
            color: #4a5568;
            font-size: 11px;
        }
        .item-value {
            color: #2d3748;
            font-size: 13px;
        }
        .reason-box {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 4px;
            padding: 15px;
            margin-top: 10px;
        }
        .reason-text {
            color: #2d3748;
            line-height: 1.6;
        }
        .signatures {
            margin-top: 50px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            width: 200px;
        }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #718096;
            text-align: center;
        }
        .baja-stamp {
            position: absolute;
            top: 150px;
            right: 100px;
            padding: 15px 25px;
            border: 3px solid #c53030;
            color: #c53030;
            font-weight: bold;
            font-size: 18px;
            transform: rotate(15deg);
            background-color: rgba(254, 215, 215, 0.1);
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Sello de BAJA -->
    <div class="baja-stamp">
        BAJA REGISTRADA
    </div>
    
    <div class="header">
        <h1>ACTA DE BAJA DE COLABORADOR</h1>
        <h2>CMAN VALVES SOLUTIONS</h2>
    </div>

    <div class="company-info">
        <strong>Sistema de Gestión de Recursos Humanos</strong><br>
        Documento generado el {{ $fechaGeneracion->format('d/m/Y H:i:s') }}
    </div>

    <div class="document-info">
        <div class="document-info-grid">
            <div class="info-item">
                <div class="info-label">NÚMERO DE BAJA</div>
                <div class="info-value">B{{ str_pad($baja->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">FECHA DE BAJA</div>
                <div class="info-value">{{ $baja->fecha_baja->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">ESTATUS</div>
                <div class="info-value" style="color: #c53030;">BAJA DEFINITIVA</div>
            </div>
        </div>
    </div>

    <!-- Información del Colaborador -->
    <div class="section">
        <div class="section-title">INFORMACIÓN DEL COLABORADOR</div>
        <div class="section-content">
            <div class="employee-info">
                <div class="info-item">
                    <div class="item-label">Nombre Completo</div>
                    <div class="item-value">{{ $baja->personal->nombre_completo }}</div>
                </div>
                <div class="info-item">
                    <div class="item-label">Área / Departamento</div>
                    <div class="item-value">{{ $baja->personal->area }} - {{ $baja->personal->departamento }}</div>
                </div>
                <div class="info-item">
                    <div class="item-label">Fecha de Ingreso</div>
                    <div class="item-value">{{ $baja->personal->fecha_ingreso->format('d/m/Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="item-label">Antigüedad</div>
                    <div class="item-value">
                        {{ $baja->personal->fecha_ingreso->diffForHumans($baja->fecha_baja, true) }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="item-label">Puesto / Grado</div>
                    <div class="item-value">{{ $baja->personal->grado ?? 'No especificado' }}</div>
                </div>
                <div class="info-item">
                    <div class="item-label">ID Colaborador</div>
                    <div class="item-value">{{ $baja->personal->id }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de la Baja -->
    <div class="section">
        <div class="section-title">DETALLES DE LA BAJA</div>
        <div class="section-content">
            <div class="info-item">
                <div class="item-label">Motivo de la Baja</div>
                <div class="reason-box">
                    <div class="reason-text">{{ $baja->motivo_baja }}</div>
                </div>
            </div>
            
            <div class="employee-info" style="margin-top: 20px;">
                <div class="info-item">
                    <div class="item-label">Registrado por</div>
                    <div class="item-value">{{ $baja->user->name ?? 'Sistema' }}</div>
                </div>
                <div class="info-item">
                    <div class="item-label">Fecha de Registro</div>
                    <div class="item-value">{{ $baja->created_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmas -->
    <div class="signatures">
        <div>
            <div class="signature-line"></div>
            <strong>COLABORADOR</strong><br>
            (Firma y nombre)
        </div>
        <div>
            <div class="signature-line"></div>
            <strong>JEFE INMEDIATO</strong><br>
            (Firma y nombre)
        </div>
        <div>
            <div class="signature-line"></div>
            <strong>RECURSOS HUMANOS</strong><br>
            (Firma y sello)
        </div>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de RH - CMAN VALVES SOLUTIONS</p>
        <p>Baja No. B{{ str_pad($baja->id, 4, '0', STR_PAD_LEFT) }} | Página 1 de 1 | Documento Confidencial</p>
    </div>
</body>
</html>