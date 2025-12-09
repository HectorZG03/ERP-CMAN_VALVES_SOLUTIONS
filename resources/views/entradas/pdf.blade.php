<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada de Material #{{ str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        
        /* Encabezado de empresa */
        .company-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #059669;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-tagline {
            font-size: 14px;
            color: #666;
            margin: 5px 0 15px 0;
        }
        
        .company-info {
            font-size: 11px;
            color: #555;
            margin-top: 10px;
        }
        
        /* Información del documento */
        .document-info {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        
        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 15px 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #ddd;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        /* Tabla de producto */
        .products-section {
            margin: 25px 0;
        }
        
        .section-title {
            background-color: #059669;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
            margin: 0;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        
        .products-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            font-size: 11px;
        }
        
        .products-table td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            font-size: 11px;
        }
        
        /* Totales */
        .totals-section {
            margin-top: 30px;
            width: 100%;
        }
        
        .totals-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            text-align: right;
        }
        
        .totals-label {
            font-weight: bold;
            background-color: #f3f4f6;
        }
        
        .subtotal-row {
            font-weight: bold;
        }
        
        .iva-row {
            font-weight: bold;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #dcfce7;
            color: #065f46;
            font-size: 13px;
        }
        
        /* Firmas */
        .signatures-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            padding-top: 40px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            width: 80%;
            margin: 0 auto 5px auto;
            height: 40px;
        }
        
        .signature-label {
            font-size: 11px;
            color: #555;
            margin-top: 5px;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 5px;
        }
        
        /* Pie de página */
        .footer {
            margin-top: 60px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 15px; 
                font-size: 11px;
            }
            .signatures-section { page-break-inside: avoid; }
            .totals-section { page-break-inside: avoid; }
        }
        
        /* Estilo para números */
        .number {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        /* Impacto en inventario */
        .inventory-impact {
            background-color: #dcfce7;
            padding: 10px 15px;
            border: 1px solid #059669;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 11px;
        }
        
        .inventory-impact strong {
            color: #065f46;
        }
    </style>
</head>
<body>
    <!-- Encabezado de la empresa -->
    <div class="company-header">
        <h1 class="company-name">CMAN VALVES SOLUTIONS</h1>
        <p class="company-tagline">Sistema de Control de Inventario</p>
        <div class="company-info">
            <p>Documento generado: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Información del documento -->
    <div class="document-info">
        <h2 class="document-title">ENTRADA DE MATERIAL</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Número de Entrada:</span>
                <span class="info-value">#{{ str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fecha de Entrada:</span>
                <span class="info-value">
                    {{ $entrada->created_at ? $entrada->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Registrado por:</span>
                <span class="info-value">{{ $entrada->user->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Proveedor:</span>
                <span class="info-value">{{ $entrada->proveedor->proveedor ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Contacto:</span>
                <span class="info-value">{{ $entrada->proveedor->contacto ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Teléfono:</span>
                <span class="info-value">{{ $entrada->proveedor->telefono ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Impacto en inventario -->
    <div class="inventory-impact">
        <strong>IMPACTO EN INVENTARIO:</strong> Se agregaron {{ $entrada->cantidad }} 
        {{ $entrada->inventario->medida ?? 'unidades' }} al inventario de 
        <strong>{{ $entrada->inventario->nombre_producto ?? 'producto' }}</strong>.
        @if($entrada->inventario)
        Existencia actual: <strong>{{ $entrada->inventario->existencia }} {{ $entrada->inventario->medida }}</strong>
        @endif
    </div>

    <!-- Tabla de producto -->
    <div class="products-section">
        <h3 class="section-title">DETALLE DEL PRODUCTO</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th width="40%">PRODUCTO</th>
                    <th width="15%" class="number">CANTIDAD</th>
                    <th width="15%" class="currency">PRECIO UNIT.</th>
                    <th width="15%" class="currency">SUBTOTAL</th>
                    <th width="15%" class="currency">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $entrada->inventario->nombre_producto ?? 'Producto' }}</strong><br>
                        <small>{{ $entrada->inventario->categoria ?? 'Sin categoría' }}</small>
                    </td>
                    <td class="number">
                        {{ $entrada->cantidad }} 
                        {{ $entrada->inventario->medida ?? 'unid' }}
                    </td>
                    <td class="currency">${{ number_format($entrada->precio_unitario, 2) }}</td>
                    <td class="currency">${{ number_format($entrada->precio_total, 2) }}</td>
                    <td class="currency">${{ number_format($entrada->total_con_iva, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="totals-section">
        <table class="totals-table">
            <tr class="subtotal-row">
                <td class="totals-label">Subtotal:</td>
                <td class="currency">${{ number_format($entrada->precio_total, 2) }}</td>
            </tr>
            <tr class="iva-row">
                <td class="totals-label">IVA (16%):</td>
                <td class="currency">${{ number_format($entrada->iva, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="totals-label">TOTAL:</td>
                <td class="currency">${{ number_format($entrada->total_con_iva, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Firmas -->
    <div class="signatures-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">ENTREGADO POR</div>
            <div class="signature-name">{{ $entrada->proveedor->proveedor ?? 'Proveedor' }}</div>
            <div class="signature-label">Proveedor</div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">RECIBIDO POR</div>
            <div class="signature-name">{{ $entrada->user->name ?? 'N/A' }}</div>
            <div class="signature-label">CMAN VALVES SOLUTIONS</div>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Control de Inventario de CMAN VALVES SOLUTIONS</p>
        <p>Este documento es válido como comprobante de entrada de materiales</p>
        <p>Impreso el {{ date('d/m/Y') }} a las {{ date('H:i:s') }}</p>
    </div>
</body>
</html>