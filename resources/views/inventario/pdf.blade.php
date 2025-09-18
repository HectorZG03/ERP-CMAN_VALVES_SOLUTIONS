<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Completo - {{ $fechaGeneracion->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #3B82F6;
            font-size: 20px;
            margin: 0;
            font-weight: bold;
        }
        
        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
            font-weight: normal;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .company-info h3 {
            margin: 0;
            color: #3B82F6;
            font-size: 16px;
        }
        
        .company-info p {
            margin: 2px 0;
            font-size: 9px;
            color: #666;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .stat-box {
            flex: 1;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #3B82F6;
        }
        
        .stat-box h4 {
            margin: 0;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .stat-box p {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .inventory-table th,
        .inventory-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        
        .inventory-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #3B82F6;
            font-size: 8px;
            text-transform: uppercase;
        }
        
        .inventory-table .stock-high {
            color: #059669;
            font-weight: bold;
        }
        
        .inventory-table .stock-low {
            color: #D97706;
            font-weight: bold;
        }
        
        .inventory-table .stock-empty {
            color: #DC2626;
            font-weight: bold;
        }
        
        .categoria-badge {
            background-color: #DBEAFE;
            color: #1E40AF;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .section-title {
            background-color: #3B82F6;
            color: white;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-size: 12px;
            font-weight: bold;
        }
        
        .summary-section {
            background-color: #EFF6FF;
            padding: 15px;
            border-left: 4px solid #3B82F6;
            margin-bottom: 20px;
        }
        
        .summary-section h3 {
            margin: 0 0 10px 0;
            color: #1E40AF;
            font-size: 12px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: bold;
            color: #374151;
        }
        
        .summary-value {
            color: #1F2937;
        }
        
        @media print {
            body { margin: 0; padding: 10px; }
            .header { page-break-inside: avoid; }
            .summary-stats { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Información de la Empresa -->
    <div class="company-info">
        <h3>CMAN VALVES SOLUTIONS</h3>
        <p>Sistema de Control de Inventario</p>
        <p>Reporte generado el {{ $fechaGeneracion->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>INVENTARIO COMPLETO</h1>
        <h2>Reporte de productos al {{ $fechaGeneracion->format('d/m/Y') }}</h2>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="summary-section">
        <h3>RESUMEN EJECUTIVO</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total de Productos:</span>
                <span class="summary-value">{{ $totalProductos }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Productos en Stock:</span>
                <span class="summary-value">{{ $totalEnStock }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Productos sin Stock:</span>
                <span class="summary-value">{{ $totalSinStock }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Valor Total del Inventario:</span>
                <span class="summary-value">${{ number_format($valorTotal, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="summary-stats">
        <div class="stat-box">
            <h4>Total Productos</h4>
            <p>{{ $totalProductos }}</p>
        </div>
        <div class="stat-box">
            <h4>En Stock</h4>
            <p style="color: #059669;">{{ $totalEnStock }}</p>
        </div>
        <div class="stat-box">
            <h4>Sin Stock</h4>
            <p style="color: #DC2626;">{{ $totalSinStock }}</p>
        </div>
        <div class="stat-box">
            <h4>Valor Total</h4>
            <p style="color: #3B82F6;">${{ number_format($valorTotal, 2) }}</p>
        </div>
    </div>

    <!-- Sección de Inventario Detallado -->
    <div class="section-title">INVENTARIO DETALLADO</div>

    <table class="inventory-table">
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Producto</th>
                <th style="width: 15%;">Categoría</th>
                <th style="width: 10%;">Medida</th>
                <th style="width: 10%;">Existencia</th>
                <th style="width: 12%;">Precio Unit.</th>
                <th style="width: 12%;">Valor Total</th>
                <th style="width: 8%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $inventario)
            <tr>
                <td style="font-family: monospace;">#{{ str_pad($inventario->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td style="font-weight: bold;">{{ $inventario->nombre_producto }}</td>
                <td>
                    <span class="categoria-badge">{{ $inventario->categoria }}</span>
                </td>
                <td>{{ $inventario->medida }}</td>
                <td class="
                    @if($inventario->existencia > 10) stock-high
                    @elseif($inventario->existencia > 0) stock-low
                    @else stock-empty
                    @endif
                ">
                    {{ $inventario->existencia }}
                </td>
                <td>${{ number_format($inventario->getPrecioPromedio(), 2) }}</td>
                <td style="font-weight: bold;">${{ number_format($inventario->precio_total, 2) }}</td>
                <td style="font-size: 8px;">
                    @if($inventario->existencia > 10)
                        <span style="color: #059669;">●</span> NORMAL
                    @elseif($inventario->existencia > 0)
                        <span style="color: #D97706;">●</span> BAJO
                    @else
                        <span style="color: #DC2626;">●</span> AGOTADO
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Análisis por Categorías -->
    @if($inventarios->groupBy('categoria')->count() > 1)
    <div class="page-break"></div>
    <div class="section-title">ANÁLISIS POR CATEGORÍAS</div>

    <table class="inventory-table">
        <thead>
            <tr>
                <th style="width: 30%;">Categoría</th>
                <th style="width: 15%;">Productos</th>
                <th style="width: 15%;">En Stock</th>
                <th style="width: 15%;">Sin Stock</th>
                <th style="width: 25%;">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios->groupBy('categoria') as $categoria => $productos)
            <tr>
                <td style="font-weight: bold;">
                    <span class="categoria-badge">{{ $categoria }}</span>
                </td>
                <td>{{ $productos->count() }}</td>
                <td class="stock-high">{{ $productos->where('existencia', '>', 0)->count() }}</td>
                <td class="stock-empty">{{ $productos->where('existencia', '<=', 0)->count() }}</td>
                <td style="font-weight: bold;">${{ number_format($productos->sum('precio_total'), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Productos que requieren atención -->
    @php
        $productosAgotados = $inventarios->where('existencia', '<=', 0);
        $productosBajos = $inventarios->where('existencia', '>', 0)->where('existencia', '<=', 10);
    @endphp

    @if($productosAgotados->count() > 0 || $productosBajos->count() > 0)
    <div class="section-title">PRODUCTOS QUE REQUIEREN ATENCIÓN</div>

    @if($productosAgotados->count() > 0)
    <h4 style="color: #DC2626; margin: 15px 0 10px 0;">Productos Agotados ({{ $productosAgotados->count() }})</h4>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Última Existencia</th>
                <th>Valor Perdido</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productosAgotados->take(10) as $producto)
            <tr>
                <td style="font-family: monospace;">#{{ str_pad($producto->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $producto->nombre_producto }}</td>
                <td><span class="categoria-badge">{{ $producto->categoria }}</span></td>
                <td class="stock-empty">{{ $producto->existencia }}</td>
                <td>${{ number_format($producto->precio_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($productosBajos->count() > 0)
    <h4 style="color: #D97706; margin: 15px 0 10px 0;">Stock Bajo ({{ $productosBajos->count() }})</h4>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Existencia</th>
                <th>Recomendación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productosBajos->take(10) as $producto)
            <tr>
                <td style="font-family: monospace;">#{{ str_pad($producto->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $producto->nombre_producto }}</td>
                <td><span class="categoria-badge">{{ $producto->categoria }}</span></td>
                <td class="stock-low">{{ $producto->existencia }}</td>
                <td style="font-size: 8px;">Reabastecer pronto</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif

    <!-- Información adicional -->
    <div class="summary-section" style="margin-top: 20px;">
        <h3>INFORMACIÓN DEL REPORTE</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Fecha de Generación:</span>
                <span class="summary-value">{{ $fechaGeneracion->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Generado por:</span>
                <span class="summary-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total de Páginas:</span>
                <span class="summary-value">Este documento</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Período de Datos:</span>
                <span class="summary-value">Actualizado hasta {{ $fechaGeneracion->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Control de Inventario</p>
        <p>CMAN VALVES SOLUTIONS | {{ date('Y') }}</p>
        <p>Para uso interno únicamente - Información confidencial</p>
        <p>Reporte generado el {{ $fechaGeneracion->format('d/m/Y') }} a las {{ $fechaGeneracion->format('H:i:s') }}</p>
    </div>
</body>
</html>