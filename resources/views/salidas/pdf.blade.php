<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Salida #{{ $salida->numero_factura ?? str_pad($salida->id, 6, '0', STR_PAD_LEFT) }}</title>
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
            border-bottom: 3px solid #dc2626;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
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
        
        /* Tabla de productos */
        .products-section {
            margin: 25px 0;
        }
        
        .section-title {
            background-color: #dc2626;
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
        
        .products-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
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
    </style>
</head>
<body>
    <!-- Encabezado de la empresa -->
    <div class="company-header">
        <h1 class="company-name">CMAN GLOBAL CONSTRUCTION</h1>
        <p class="company-tagline">Sistema de Control de Inventario</p>
        <div class="company-info">
            <p>Documento generado: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Información del documento -->
    <div class="document-info">
        <h2 class="document-title">FACTURA DE SALIDA</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Número de Factura:</span>
                <span class="info-value">{{ $salida->numero_factura ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fecha de Salida:</span>
                <span class="info-value">
                    @if($salida->fecha_salida)
                        {{ $salida->fecha_salida->format('d/m/Y') }}
                    @elseif($salida->created_at)
                        {{ $salida->created_at->format('d/m/Y') }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $salida->cliente->nombre ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Área:</span>
                <span class="info-value">{{ $salida->cliente->area ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Atendido por:</span>
                <span class="info-value">{{ $salida->user->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Productos:</span>
                <span class="info-value">{{ $salida->detalles->count() ?? 0 }}</span>
            </div>
        </div>
        @if($salida->observaciones)
        <div class="info-item">
            <span class="info-label">Observaciones:</span>
            <span class="info-value">{{ $salida->observaciones }}</span>
        </div>
        @endif
    </div>

    <!-- Tabla de productos -->
    <div class="products-section">
        <h3 class="section-title">DETALLE DE PRODUCTOS</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">PRODUCTO</th>
                    <th width="15%" class="number">CANTIDAD</th>
                    <th width="15%" class="currency">PRECIO UNIT.</th>
                    <th width="15%" class="currency">SUBTOTAL</th>
                    <th width="15%" class="currency">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salida->detalles as $index => $detalle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $detalle->inventario->nombre_producto ?? 'Producto' }}</strong><br>
                        <small>{{ $detalle->inventario->categoria ?? 'Sin categoría' }}</small>
                    </td>
                    <td class="number">
                        {{ $detalle->cantidad }} 
                        {{ $detalle->inventario->medida ?? 'unid' }}
                    </td>
                    <td class="currency">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="currency">${{ number_format($detalle->precio_total, 2) }}</td>
                    <td class="currency">${{ number_format($detalle->total_con_iva, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        No hay productos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="totals-section">
        <table class="totals-table">
            <tr class="subtotal-row">
                <td class="totals-label">Subtotal:</td>
                <td class="currency">${{ number_format($salida->precio_total, 2) }}</td>
            </tr>
            <tr class="iva-row">
                <td class="totals-label">IVA (16%):</td>
                <td class="currency">${{ number_format($salida->iva, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="totals-label">TOTAL:</td>
                <td class="currency">${{ number_format($salida->total_con_iva, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Firmas -->
    <div class="signatures-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">ENTREGADO POR</div>
            <div class="signature-name">{{ $salida->user->name ?? 'N/A' }}</div>
            <div class="signature-label">CMAN GLOBAL CONSTRUCTION</div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">RECIBIDO POR</div>
            <div class="signature-name">{{ $salida->cliente->nombre ?? 'N/A' }}</div>
            <div class="signature-label">{{ $salida->cliente->area ?? 'Cliente' }}</div>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Control de Inventario de CMAN GLOBAL CONSTRUCTION</p>
        <p>Este documento es válido como comprobante de salida de materiales</p>
        <p>Impreso el {{ date('d/m/Y') }} a las {{ date('H:i:s') }}</p>
    </div>
</body>
</html>