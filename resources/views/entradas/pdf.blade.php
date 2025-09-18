<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada de Material #{{ str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}</title>
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
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #059669;
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
            border-left: 4px solid #059669;
            font-size: 14px;
            color: #059669;
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
            color: #059669;
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
            color: #059669;
            font-size: 18px;
        }
        
        .company-info p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }
        
        .inventory-impact {
            background-color: #dcfce7;
            padding: 10px;
            border-left: 4px solid #059669;
            margin: 15px 0;
        }
        
        .inventory-impact h4 {
            margin: 0;
            color: #059669;
            font-size: 12px;
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
        <h1>ENTRADA DE MATERIAL</h1>
        <h2>#{{ str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <!-- Información General -->
    <div class="section">
        <h3>INFORMACIÓN GENERAL</h3>
        <div class="row">
            <div class="col-label">Fecha de Entrada:</div>
            <div class="col-value">
                {{ $entrada->created_at ? $entrada->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Registrado por:</div>
            <div class="col-value">
                {{ $entrada->user ? $entrada->user->name : 'Usuario no disponible' }} 
                ({{ $entrada->user ? $entrada->user->email : 'N/A' }})
            </div>
        </div>
        <div class="row">
            <div class="col-label">ID de Transacción:</div>
            <div class="col-value">#{{ str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- Información del Proveedor -->
    <div class="section">
        <h3>INFORMACIÓN DEL PROVEEDOR</h3>
        <div class="row">
            <div class="col-label">Nombre:</div>
            <div class="col-value">
                {{ $entrada->proveedor ? $entrada->proveedor->proveedor : 'Proveedor no disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Contacto:</div>
            <div class="col-value">
                {{ $entrada->proveedor && $entrada->proveedor->contacto ? $entrada->proveedor->contacto : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Email:</div>
            <div class="col-value">
                {{ $entrada->proveedor && $entrada->proveedor->email ? $entrada->proveedor->email : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Teléfono:</div>
            <div class="col-value">
                {{ $entrada->proveedor && $entrada->proveedor->telefono ? $entrada->proveedor->telefono : 'No disponible' }}
            </div>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="section">
        <h3>PRODUCTO RECIBIDO</h3>
        <div class="row">
            <div class="col-label">Nombre del Producto:</div>
            <div class="col-value">
                {{ $entrada->inventario ? $entrada->inventario->nombre_producto : 'Producto no disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Categoría:</div>
            <div class="col-value">
                {{ $entrada->inventario ? $entrada->inventario->categoria : 'No disponible' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Cantidad Recibida:</div>
            <div class="col-value">
                {{ $entrada->cantidad }} 
                {{ $entrada->inventario ? $entrada->inventario->medida : 'unidades' }}
            </div>
        </div>
        <div class="row">
            <div class="col-label">Precio Unitario:</div>
            <div class="col-value">${{ number_format($entrada->precio_unitario, 2) }}</div>
        </div>
    </div>

    <!-- Impacto en Inventario -->
    <div class="inventory-impact">
        <h4>IMPACTO EN INVENTARIO</h4>
        <p>Se agregaron <strong>{{ $entrada->cantidad }} 
        {{ $entrada->inventario ? $entrada->inventario->medida : 'unidades' }}</strong> 
        al inventario de {{ $entrada->inventario ? $entrada->inventario->nombre_producto : 'este producto' }}.</p>
        @if($entrada->inventario)
        <p>Existencia actual: <strong>{{ $entrada->inventario->existencia }} {{ $entrada->inventario->medida }}</strong></p>
        @endif
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
                    <td>{{ $entrada->inventario ? $entrada->inventario->nombre_producto : 'Producto' }}</td>
                    <td>{{ $entrada->cantidad }}</td>
                    <td>${{ number_format($entrada->precio_unitario, 2) }}</td>
                    <td>${{ number_format($entrada->precio_total, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td><strong>${{ number_format($entrada->precio_total, 2) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>IVA (16%):</strong></td>
                    <td><strong>${{ number_format($entrada->iva, 2) }}</strong></td>
                </tr>
                <tr class="final-total">
                    <td colspan="3" style="text-align: right; font-size: 14px;"><strong>TOTAL PAGADO:</strong></td>
                    <td style="font-size: 14px;"><strong>${{ number_format($entrada->total_con_iva, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Sección de Firmas -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Recibido por:</strong></p>
            <p>{{ $entrada->user ? $entrada->user->name : 'N/A' }}</p>
            <p>{{ $entrada->created_at ? $entrada->created_at->format('d/m/Y') : 'N/A' }}</p>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Entregado por:</strong></p>
            <p>{{ $entrada->proveedor ? $entrada->proveedor->proveedor : 'N/A' }}</p>
            <p>Fecha: _________________</p>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="section">
        <h3>INFORMACIÓN ADICIONAL</h3>
        <div class="row">
            <div class="col-label">Tipo de Transacción:</div>
            <div class="col-value">Entrada de Material / Compra</div>
        </div>
        <div class="row">
            <div class="col-label">Estado:</div>
            <div class="col-value">Procesada y aplicada al inventario</div>
        </div>
        <div class="row">
            <div class="col-label">Método de Pago:</div>
            <div class="col-value">____________________</div>
        </div>
        <div class="row">
            <div class="col-label">Observaciones:</div>
            <div class="col-value">____________________</div>
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