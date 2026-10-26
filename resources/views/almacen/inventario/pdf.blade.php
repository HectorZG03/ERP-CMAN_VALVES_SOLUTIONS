<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - {{ $fechaGeneracion->format('d/m/Y') }}</title>
    <style>

.kpi-bar {
    display: flex;
    justify-content: space-between;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 20px;
}
.kpi-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    font-size: 10px;
    border-right: 1px solid #e5e7eb;
}
.kpi-item:last-child {
    border-right: none;
}
.kpi-label {
    font-size: 8px;
    color: #6b7280;
    text-transform: uppercase;
}
.kpi-value {
    font-weight: bold;
    font-size: 12px;
}

body {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #333;
    margin: 0;
    padding: 15px;
}
.header {
    text-align: center;
    border-bottom: 2px solid #3B82F6;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.header h1 {
    margin: 0;
    font-size: 18px;
    color: #3B82F6;
}
.company-info {
    text-align: center;
    margin-bottom: 15px;
    font-size: 9px;
    color: #666;
}
.summary-stats {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.stat-box {
    flex: 1;
    background: #f8f9fa;
    border-left: 4px solid #3B82F6;
    padding: 8px;
    text-align: center;
    border-radius: 4px;
}
.stat-box h4 {
    margin: 0;
    font-size: 9px;
    color: #666;
    text-transform: uppercase;
}
.stat-box p {
    margin: 3px 0 0;
    font-size: 14px;
    font-weight: bold;
}
.section-title {
    background: #3B82F6;
    color: #fff;
    padding: 6px 10px;
    margin: 20px 0 10px;
    font-size: 11px;
    font-weight: bold;
}
.inventory-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}
.inventory-table th,
.inventory-table td {
    border: 1px solid #ddd;
    padding: 5px;
    font-size: 9px;
}
.inventory-table th {
    background: #f1f5f9;
    color: #3B82F6;
    text-transform: uppercase;
    font-size: 8px;
}
.stock-high { color: #059669; font-weight: bold; }
.stock-low { color: #D97706; font-weight: bold; }
.stock-empty { color: #DC2626; font-weight: bold; }

.categoria-badge {
    background: #DBEAFE;
    color: #1E40AF;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 8px;
    font-weight: bold;
}

.estado-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 8px;
    font-weight: bold;
    color: #fff;
}
.estado-normal { background: #059669; }
.estado-bajo { background: #f4f734; }
.estado-agotado { background: #DC2626; }

.estado-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #fff;
}

.footer {
    margin-top: 25px;
    border-top: 1px solid #ddd;
    padding-top: 10px;
    text-align: center;
    font-size: 8px;
    color: #666;
}
</style>
</head>
<body>
    <div class="header">
        <h1>INVENTARIO COMPLETO</h1>
        <p class="company-info">CMAN VALVES SOLUTIONS - {{ $fechaGeneracion->format('d/m/Y H:i') }}</p>
    </div>

    <div class="kpi-bar">
        <div class="kpi-item">
            <span class="kpi-label">Total Productos</span>
            <span class="kpi-value">{{ $totalProductos }}</span>
        </div>
        <div class="kpi-item">
            <span class="kpi-label">En Stock</span>
            <span class="kpi-value" style="color:#059669;">{{ $totalEnStock }}</span>
        </div>
        <div class="kpi-item">
            <span class="kpi-label">Sin Stock</span>
            <span class="kpi-value" style="color:#DC2626;">{{ $totalSinStock }}</span>
        </div>
        <div class="kpi-item">
            <span class="kpi-label">Valor Total</span>
            <span class="kpi-value" style="color:#3B82F6;">${{ number_format($valorTotal, 2) }}</span>
        </div>
    </div>

    <div class="section-title">INVENTARIO DETALLADO</div>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Ubicación</th>
                <th>Medida</th>
                <th>Existencia</th>
                <th>Precio Unit.</th>
                <th>Valor Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $inventario)
            <tr>
                <td style="font-family: monospace;">#{{ str_pad($inventario->id,4,'0',STR_PAD_LEFT) }}</td>
                <td style="font-weight: bold;">{{ $inventario->nombre_producto }}</td>
                <td><span class="categoria-badge">{{ $inventario->categoria }}</span></td>
                <td>{{ $inventario->ubicacion ?? 'N/A' }}</td>
                <td>{{ $inventario->medida }}</td>
                <td class="
                    @if($inventario->existencia > 10) stock-high
                    @elseif($inventario->existencia > 0) stock-low
                    @else stock-empty
                    @endif
                ">{{ $inventario->existencia }}</td>
                <td>${{ number_format($inventario->getPrecioPromedio(),2) }}</td>
                <td style="font-weight:bold">${{ number_format($inventario->precio_total,2) }}</td>
                <td>
                    @if($inventario->existencia > 10)
                        <span class="estado-badge estado-normal"><span class="dot"></span> Normal</span>
                    @elseif($inventario->existencia > 0)
                        <span class="estado-badge estado-bajo"><span class="dot"></span> Bajo</span>
                    @else
                        <span class="estado-badge estado-agotado"><span class="dot"></span> Agotado</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>CMAN VALVES SOLUTIONS | {{ date('Y') }}</p>
        <p>Reporte generado automáticamente - Uso interno</p>
    </div>
</body>
</html>
