<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salida de Material #{{ str_pad($salida->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #DC2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #DC2626;
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        
        .header h2 {
            color: #666;
            font-size: 16px;
            margin: 5px 0;
            font-weight: normal;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h3 {
            background-color: #f8f9fa;
            padding: 8px 12px;
            margin: 0 0 15px 0;
            border-left: 4px solid #DC2626;
            font-size: 14px;
            color: #DC2626;
            font-weight: bold;
        }
        
        .row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .col-label {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        
        .col-value {
            width: 70%;
            color: #333;
        }
        
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .financial-table th,
        .financial-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .financial-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #DC2626;
        }
        
        .financial-table .total-row {
            background-color: #f0f9ff;
            font-weight: bold;
        }
        
        .financial-table .final-total {
            background-color: #dcfce7;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 50px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .company-info h3 {
            margin: 0;
            color: #DC2626;
            font-size: 18px;
        }
        
        .company-info p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }
        
        @media print {
            body { margin: 0; padding: 15px; }
            .header { page-break-inside: avoid; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Información de la Empresa -->
    <div class="company-info">
        <h3>CMAN VALVES SOLUTIONS</h3>
        <p>Sistema de Control de Inventario</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>SALIDA DE MATERIAL</h1>
        <h2>#{{ str_pad($salida->id, 6, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <!-- Información General -->
    <div class="section">
        <h3>INFORMACIÓN GENERAL</h3>
        <div class="row">
            <div class="col-label">Fecha de Salida:</div>
            <div class="col-value">
                {{ $salida->created_at ? $salida->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Registrado por:</div>
            <div class="col-value">
                {{ $salida->user ? $salida->user->name : 'Usuario no disponible' }} 
                ({{ $salida->user ? $salida->user->email : 'N/A' }})
            </div>
        </div>
        <div class="row">
            <div class="col-label">ID de Transacción:</div>
            <div class="col-value">#{{ str_pad($salida->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- Información del Cliente -->
    <div class="section">
        <h3>INFORMACIÓN DEL CLIENTE</h3>
        <div class="row">
            <div class="col-label">Nombre:</div>
            <div class="col-value">
                {{ $salida->cliente ? $salida->cliente->nombre : 'Cliente no disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Área:</div>
            <div class="col-value">
                {{ $salida->cliente ? $salida->cliente->area : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Email:</div>
            <div class="col-value">
                {{ $salida->cliente && $salida->cliente->email ? $salida->cliente->email : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Teléfono:</div>
            <div class="col-value">
                {{ $salida->cliente && $salida->cliente->telefono ? $salida->cliente->telefono : 'No disponible' }}
            </div>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="section">
        <h3>PRODUCTO VENDIDO</h3>
        <div class="row">
            <div class="col-label">Nombre del Producto:</div>
            <div class="col-value">
                {{ $salida->inventario ? $salida->inventario->nombre_producto : 'Producto no disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Categoría:</div>
            <div class="col-value">
                {{ $salida->inventario ? $salida->inventario->categoria : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Cantidad Vendida:</div>
            <div class="col-value">
                {{ $salida->cantidad }} 
                {{ $salida->inventario ? $salida->inventario->medida : 'unidades' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Precio Unitario:</div>
            <div class="col-value">${{ number_format($salida->precio_unitario, 2) }}</div>
        </div>
    </div>

    <!-- Detalles Financieros -->
    <div class="section">
        <h3>DETALLES FINANCIEROS</h3>
        
        <table class="financial-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $salida->inventario ? $salida->inventario->nombre_producto : 'Producto' }}</td>
                    <td>{{ $salida->cantidad }}</td>
                    <td>${{ number_format($salida->precio_unitario, 2) }}</td>
                    <td>${{ number_format($salida->precio_total, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td><strong>${{ number_format($salida->precio_total, 2) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>IVA (16%):</strong></td>
                    <td><strong>${{ number_format($salida->iva, 2) }}</strong></td>
                </tr>
                <tr class="final-total">
                    <td colspan="3" style="text-align: right; font-size: 14px;"><strong>TOTAL FINAL:</strong></td>
                    <td style="font-size: 14px;"><strong>${{ number_format($salida->total_con_iva, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Sección de Firmas -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Entregado por:</strong></p>
            <p>{{ $salida->user ? $salida->user->name : 'N/A' }}</p>
            <p>{{ $salida->created_at ? $salida->created_at->format('d/m/Y') : 'N/A' }}</p>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Recibido por:</strong></p>
            <p>{{ $salida->cliente ? $salida->cliente->nombre : 'N/A' }}</p>
            <p>Fecha: _________________</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Este documento fue generado automáticamente por el Sistema de Control de Inventario</p>
        <p>CMAN VALVES SOLUTIONS | {{ date('Y') }}</p>
        <p>Documento generado el {{ date('d/m/Y') }} a las {{ date('H:i:s') }}</p>
    </div>
</body>
</html>