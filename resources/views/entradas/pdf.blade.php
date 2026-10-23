<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrada {{ $entrada->numero_factura }}</title>

    <style>
        @page {
            size: letter portrait;
            margin: 30px 32px 38px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            margin-bottom: 12px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 20%;
            padding-right: 10px;
            text-align: left;
        }

        .company {
            width: 54%;
            padding-right: 8px;
        }

        .folio-cell {
            width: 26%;
            text-align: right;
        }

        .logo {
            display: block;
            width: 88px;
            max-width: 100%;
            max-height: 68px;
        }

        .company-name {
            margin: 0 0 4px;
            color: #102a43;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-address {
            margin: 0;
            color: #4b5563;
            font-size: 8px;
        }

        .folio-box {
            display: inline-block;
            min-width: 115px;
            padding: 8px 9px;
            border: 1px solid #19324d;
            text-align: center;
        }

        .folio-label {
            display: block;
            margin-bottom: 3px;
            color: #19324d;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .folio-value {
            color: #111827;
            font-size: 9px;
            font-weight: bold;
        }

        .section-band {
            margin-top: 8px;
            margin-bottom: 0;
            padding: 7px 8px;
            background: #19324d;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .details-table {
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .details-table td {
            padding: 6px 8px;
            border: 1px solid #9ca3af;
            overflow-wrap: break-word;
        }

        .details-label {
            background: #19324d;
            color: #fff;
            font-weight: bold;
        }

        .details-value {
            background: #fff;
        }

        .form-code {
            background: #19324d;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }

        .items-table {
            margin-top: 0;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table th {
            padding: 6px 4px;
            border: 1px solid #9ca3af;
            background: #e8edf2;
            color: #19324d;
            font-size: 7px;
            text-align: center;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 6px 4px;
            border: 1px solid #b7c0c9;
            font-size: 8px;
            vertical-align: top;
        }

        .items-table tbody tr {
            page-break-inside: avoid;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .product-name {
            font-weight: bold;
        }

        .muted {
            color: #6b7280;
        }

        .table-total td {
            background: #f3f4f6;
            font-weight: bold;
            vertical-align: middle;
        }

        .financial-wrapper {
            margin-top: 14px;
        }

        .notes-cell {
            width: 48%;
            padding-right: 10px;
            vertical-align: top;
        }

        .totals-cell {
            width: 52%;
            vertical-align: top;
        }

        .notes-box {
            min-height: 75px;
            padding: 8px;
            border: 1px solid #b7c0c9;
        }

        .notes-title {
            margin: 0 0 6px;
            color: #19324d;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .totals-table td {
            padding: 6px 7px;
            border: 1px solid #b7c0c9;
        }

        .totals-label {
            background: #f3f4f6;
            font-weight: bold;
        }

        .total-final td {
            background: #19324d;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
        }

        .signatures {
            margin-top: 350px;
            page-break-inside: avoid;
        }

        .signature-cell,
        .seal-cell {
            width: 50%;
            padding: 0 12px;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            width: 85%;
            height: 38px;
            margin: 0 auto;
            border-bottom: 1px solid #374151;
            text-align: center;
        }

        .signature-image {
            display: inline-block;
            max-width: 175px;
            max-height: 36px;
            vertical-align: bottom;
        }

        .signature-title {
            margin-top: 6px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-name {
            margin-top: 3px;
            font-size: 8px;
        }

        .seal-box {
            width: 160px;
            height: 100px;
            margin: 0 auto;
            border: 1px solid #9ca3af;
            color: #9ca3af;
            text-align: center;
            font-size: 8px;
        }

        .seal-box span {
            display: block;
            padding-top: 38px;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -20px;
            left: 0;
            padding-top: 5px;
            border-top: 1px solid #d1d5db;
            color: #6b7280;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $subtotal = (float) $entrada->precio_total;
        $descuento = (float) ($entrada->descuento_global ?? 0);
        $baseDescuento = (float) $entrada->subtotal_con_descuento;
        $iva = (float) $entrada->iva;
        $total = (float) $entrada->total_con_iva;
        $cantidadTotal = (int) $entrada->detalles->sum('cantidad');
    @endphp

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img
                    class="logo"
                    src="{{ public_path('img/logo/logo_cman.png') }}"
                    alt="CMAN"
                >
            </td>

            <td class="company">
                <p class="company-name">CMAN GLOBAL CONSTRUCTION S.A. DE C.V.</p>
                <p class="company-address">SISTEMA ERP</p>
            </td>

            <td class="folio-cell">
                <div class="folio-box">
                    <span class="folio-label">Entrada</span>
                    <span class="folio-value">
                        {{ $entrada->numero_factura ?? 'ENT-' . str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-band">Recepción de materiales y/o herramientas</div>

    <table class="details-table">
        <colgroup>
            <col style="width: 17%;">
            <col style="width: 29%;">
            <col style="width: 17%;">
            <col style="width: 37%;">
        </colgroup>

        <tr>
            <td class="details-label">Proveedor:</td>
            <td class="details-value" colspan="2">
                {{ $entrada->proveedor->proveedor ?? 'Proveedor no disponible' }}
            </td>
            <td class="form-code">FOR-01-PRO-ALM-01</td>
        </tr>

        <tr>
            <td class="details-label">No. factura:</td>
            <td class="details-value">
                {{ $entrada->numero_factura_proveedor ?: 'No proporcionado' }}
            </td>
            <td class="details-label">Fecha:</td>
            <td class="details-value">
                {{ $entrada->fecha_entrada ? $entrada->fecha_entrada->format('d/m/Y') : 'N/A' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 12%;">Código</th>
                <th style="width: 18%;">Categoría</th>
                <th style="width: 32%;">Producto</th>
                <th style="width: 10%;">Medida</th>
                <th style="width: 10%;">Cantidad</th>
                <th style="width: 18%;">Precio unitario</th>
            </tr>
        </thead>

        <tbody>
            @forelse($entrada->detalles as $detalle)
                <tr>
                    <td class="center">
                        {{ $detalle->inventario->economico ?? 'N/A' }}
                    </td>

                    <td>{{ $detalle->inventario->categoria ?? 'Sin categoría' }}</td>

                    <td class="product-name">
                        {{ $detalle->inventario->nombre_producto ?? 'Producto no disponible' }}
                    </td>

                    <td class="center">{{ $detalle->inventario->medida ?? 'N/A' }}</td>

                    <td class="center">{{ number_format($detalle->cantidad) }}</td>

                    <td class="right">
                        ${{ number_format($detalle->precio_unitario, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center muted">
                        No hay materiales registrados.
                    </td>
                </tr>
            @endforelse

            <tr class="table-total">
                <td colspan="4" class="right">Cantidad total recibida:</td>
                <td class="center">{{ number_format($cantidadTotal) }}</td>
                <td class="right">Subtotal: ${{ number_format($subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="financial-wrapper">
        <tr>
            <td class="notes-cell">
                <div class="notes-box">
                    <p class="notes-title">Observaciones</p>
                    {{ $entrada->observaciones ?: 'Sin observaciones.' }}
                </div>
            </td>

            <td class="totals-cell">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Subtotal de materiales</td>
                        <td class="right">${{ number_format($subtotal, 2) }}</td>
                    </tr>

                    <tr>
                        <td class="totals-label">Descuento global</td>
                        <td class="right">- ${{ number_format($descuento, 2) }}</td>
                    </tr>

                    <tr>
                        <td class="totals-label">Base con descuento</td>
                        <td class="right">${{ number_format($baseDescuento, 2) }}</td>
                    </tr>

                    <tr>
                        <td class="totals-label">IVA (16%)</td>
                        <td class="right">${{ number_format($iva, 2) }}</td>
                    </tr>

                    <tr class="total-final">
                        <td>Total de entrada</td>
                        <td class="right">${{ number_format($total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">
                    @if(!empty($firmaBase64) && !empty($firmaMime))
                        <img
                            class="signature-image"
                            src="data:{{ $firmaMime }};base64,{{ $firmaBase64 }}"
                            alt="Firma de {{ $entrada->user->name ?? 'usuario' }}"
                        >
                    @endif
                </div>

                <div class="signature-title">Recibido por</div>
                <div class="signature-name">
                    {{ $entrada->user->name ?? 'N/A' }}
                </div>
                <div class="signature-name">CMAN VALVES SOLUTIONS</div>
            </td>

            <td class="seal-cell">
                <div class="seal-box">
                    <span>ESPACIO PARA SELLO</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        CMAN GLOBAL CONSTRUCTION S.A. DE C.V. - Comprobante de recepción de materiales
    </div>
</body>
</html>