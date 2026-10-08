<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vale de salida {{ $salida->numero_factura ?? $salida->id }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111827;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7.5pt;
            line-height: 1.25;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo-cell {
            width: 20%;
            vertical-align: middle !important;
        }

        .logo {
            width: 118px;
            max-height: 58px;
        }

        .title-cell {
            width: 52%;
            padding: 2px 8px 0;
            text-align: center;
        }

        .company-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-address {
            margin-top: 2px;
            color: #4b5563;
            font-size: 6.2pt;
        }

        .document-title {
            margin-top: 7px;
            font-size: 8.2pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta-cell {
            width: 28%;
        }

        .meta-table {
            border: 1px solid #111827;
        }

        .meta-table th {
            padding: 3px;
            border-bottom: 1px solid #111827;
            background: #1f2937;
            color: #ffffff;
            font-size: 6.8pt;
            text-transform: uppercase;
        }

        .meta-table td {
            padding: 3px 5px;
            border-bottom: 1px solid #d1d5db;
            font-size: 6.8pt;
        }

        .meta-table tr:last-child td {
            border-bottom: 0;
        }

        .meta-label {
            width: 48%;
            font-weight: bold;
        }

        .form-code {
            margin-top: 2px;
            text-align: right;
            font-size: 5.8pt;
            font-weight: bold;
        }

        .section {
            margin-top: 6px;
            border: 1px solid #111827;
        }

        .section-title {
            padding: 3px 6px;
            background: #1f2937;
            color: #ffffff;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .data-table td {
            width: 50%;
            padding: 3px 6px;
            border-right: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            vertical-align: top;
        }

        .data-table tr:last-child td {
            border-bottom: 0;
        }

        .data-table td:last-child {
            border-right: 0;
        }

        .label {
            font-weight: bold;
        }

        .items {
            margin-top: 6px;
            table-layout: fixed;
        }

        .items thead {
            display: table-header-group;
        }

        .items th {
            padding: 4px 3px;
            border: 1px solid #111827;
            background: #1f2937;
            color: #ffffff;
            font-size: 6.5pt;
            text-align: center;
            text-transform: uppercase;
        }

        .items td {
            height: 22px;
            padding: 3px 4px;
            border: 1px solid #111827;
            font-size: 6.5pt;
            vertical-align: middle;
        }

        .items tr {
            page-break-inside: avoid;
        }

        .col-code {
            width: 13%;
            text-align: center;
        }

        .col-description {
            width: 43%;
        }

        .col-unit {
            width: 10%;
            text-align: center;
        }

        .col-quantity {
            width: 9%;
            text-align: center;
        }

        .col-price,
        .col-total {
            width: 12.5%;
            text-align: right;
            white-space: nowrap;
        }

        .summary-table {
            margin-top: 5px;
        }

        .observations-cell {
            width: 72%;
            border: 1px solid #111827;
            vertical-align: top;
        }

        .summary-space {
            width: 2%;
        }

        .total-cell {
            width: 26%;
            border: 1px solid #111827;
            vertical-align: top;
        }

        .small-header {
            padding: 3px 6px;
            background: #1f2937;
            color: #ffffff;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .observations-body {
            min-height: 35px;
            padding: 5px 6px;
        }

        .total-value {
            padding: 10px 6px;
            font-size: 10pt;
            font-weight: bold;
            text-align: right;
        }

        .obligations {
            margin-top: 6px;
            border: 1px solid #111827;
            page-break-inside: avoid;
        }

        .obligations p {
            margin: 0;
            padding: 5px 6px;
            font-size: 6.2pt;
            font-weight: bold;
            text-align: justify;
        }

        .signatures {
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .signature-cell {
            width: 33.33%;
            padding: 0 4px;
            vertical-align: top;
        }

        .signature-box {
            height: 102px;
            border: 1px solid #111827;
        }

        .signature-title {
            padding: 3px;
            border-bottom: 1px solid #111827;
            font-size: 6.3pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .signature-space {
            height: 58px;
            padding: 2px 5px 0;
            text-align: center;
            overflow: hidden;
        }

        .signature-image {
            display: block;
            width: 145px;
            height: 50px;
            margin: 0 auto;
        }

        .signature-line {
            height: 1px;
            margin: 0 5px;
            border-top: 1px solid #111827;
            font-size: 0;
            line-height: 0;
        }

        .signature-name {
            margin: 0 5px;
            padding: 3px 2px 0;
            font-size: 5.7pt;
            font-weight: bold;
            line-height: 1.2;
            text-align: center;
            text-transform: uppercase;
            word-wrap: break-word;
        }

        .footer {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #111827;
            color: #4b5563;
            font-size: 6pt;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $solicitud = $salida->solicitudMaterial;
    $usuarioSolicitante = $solicitud?->user;
    $operadorAsignado = $solicitud?->operadorPersonal;

    $solicitanteNombre = $usuarioSolicitante?->name
        ?? $salida->cliente?->nombre
        ?? 'NO DISPONIBLE';
    $solicitanteId = $usuarioSolicitante?->num_empleado ?? 'N/A';
    $solicitanteEmail = $usuarioSolicitante?->email
        ?? $salida->cliente?->email
        ?? 'N/A';
    $areaSolicitante = $usuarioSolicitante?->role
        ?? $salida->cliente?->area
        ?? 'N/A';
    $destino = $solicitud?->destino
        ?? $salida->cliente?->area
        ?? 'N/A';
    $operadorNombre = $operadorAsignado?->nombre_completo
        ?? $solicitud?->operador
        ?? 'NO ASIGNADO';
    $folio = $salida->numero_factura
        ?? 'SAL-' . str_pad($salida->id, 6, '0', STR_PAD_LEFT);
    $fechaSalida = $salida->fecha_salida?->format('d/m/Y')
        ?? $salida->created_at?->format('d/m/Y')
        ?? 'N/A';
@endphp

<table class="header-table">
    <tr>
        <td class="logo-cell">
            @if(file_exists(public_path('img/logo/logo_cman.png')))
                <img class="logo"
                    src="{{ public_path('img/logo/logo_cman.png') }}"
                    alt="CMAN">
            @elseif(file_exists(public_path('img/logo/logo.png')))
                <img class="logo"
                    src="{{ public_path('img/logo/logo.png') }}"
                    alt="CMAN">
            @endif
        </td>
        <td class="title-cell">
            <div class="company-name">CMAN GLOBAL CONSTRUCTION S.A. DE C.V.</div>
            <div class="company-address">
                ANACLETO CANABAL 1RA SECCIÓN - C.P. 86103 - VILLAHERMOSA, TABASCO
            </div>
            <div class="document-title">
                VALE DE SALIDA DE MATERIALES, HERRAMIENTAS Y/O EQUIPO
            </div>
        </td>
        <td class="meta-cell">
            <table class="meta-table">
                <tr>
                    <th colspan="2">Datos de salida</th>
                </tr>
                <tr>
                    <td class="meta-label">FECHA:</td>
                    <td>{{ $fechaSalida }}</td>
                </tr>
                <tr>
                    <td class="meta-label">FOLIO:</td>
                    <td>{{ $folio }}</td>
                </tr>
                <tr>
                    <td class="meta-label">SOLICITUD:</td>
                    <td>{{ $solicitud ? '#' . $solicitud->id : 'N/A' }}</td>
                </tr>
            </table>
            <div class="form-code">FOR-04-PRO-ALM 01</div>
        </td>
    </tr>
</table>

<div class="section">
    <div class="section-title">Datos del remitente</div>
    <table class="data-table">
        <tr>
            <td><span class="label">DEPARTAMENTO:</span> ALMACÉN GENERAL</td>
            <td><span class="label">TELÉFONO:</span> 993 175 5082</td>
        </tr>
        <tr>
            <td><span class="label">EMAIL:</span> almacengeneral@cman.com.mx</td>
            <td><span class="label">DIRECCIÓN:</span> TECNO PARQUE - BODEGA #2</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Datos de la solicitud y del solicitante</div>
    <table class="data-table">
        <tr>
            <td><span class="label">DESTINO:</span> {{ mb_strtoupper($destino) }}</td>
            <td><span class="label">COMPAÑÍA:</span> CMAN GLOBAL CONSTRUCTION S.A. DE C.V.</td>
        </tr>
        <tr>
            <td><span class="label">SOLICITANTE:</span> {{ mb_strtoupper($solicitanteNombre) }}</td>
            <td><span class="label">ID SOLICITANTE:</span> {{ mb_strtoupper($solicitanteId) }}</td>
        </tr>
        <tr>
            <td><span class="label">ÁREA/ROL:</span> {{ mb_strtoupper($areaSolicitante) }}</td>
            <td><span class="label">EMAIL:</span> {{ $solicitanteEmail }}</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%; border-right: 0;">
                <span class="label">OPERADOR ASIGNADO:</span> {{ mb_strtoupper($operadorNombre) }}
            </td>
        </tr>
    </table>
</div>

<table class="items">
    <thead>
        <tr>
            <th class="col-code">Código</th>
            <th class="col-description">Descripción del material, herramienta y/o equipo</th>
            <th class="col-unit">Medida</th>
            <th class="col-quantity">Cantidad</th>
            <th class="col-price">Costo unitario</th>
            <th class="col-total">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salida->detalles as $detalle)
            @php
                $importe = (float) $detalle->precio_unitario * (int) $detalle->cantidad;
            @endphp
            <tr>
                <td class="col-code">
                    {{ $detalle->inventario?->economico ?? 'INV-' . $detalle->inventario_id }}
                </td>
                <td class="col-description">
                    <strong>{{ mb_strtoupper($detalle->inventario?->nombre_producto ?? 'MATERIAL NO DISPONIBLE') }}</strong>
                    @if($detalle->inventario?->categoria)
                        <br>{{ mb_strtoupper($detalle->inventario->categoria) }}
                    @endif
                </td>
                <td class="col-unit">
                    {{ mb_strtoupper($detalle->inventario?->medida ?? 'UNIDAD') }}
                </td>
                <td class="col-quantity">{{ $detalle->cantidad }}</td>
                <td class="col-price">${{ number_format((float) $detalle->precio_unitario, 2) }}</td>
                <td class="col-total">${{ number_format($importe, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="height: 35px; text-align: center;">
                    SIN MATERIALES REGISTRADOS
                </td>
            </tr>
        @endforelse

    </tbody>
</table>

<table class="summary-table">
    <tr>
        <td class="observations-cell">
            <div class="small-header">Observaciones</div>
            <div class="observations-body">
                {{ $salida->observaciones ?: 'SIN OBSERVACIONES' }}
            </div>
        </td>
        <td class="summary-space"></td>
        <td class="total-cell">
            <div class="small-header">Total</div>
            <div class="total-value">
                ${{ number_format((float) $salida->precio_total, 2) }}
            </div>
        </td>
    </tr>
</table>

<div class="obligations">
    <div class="small-header">Obligaciones del solicitante</div>
    <p>
        EL PERSONAL SOLICITANTE DEBERÁ REVISAR LOS MATERIALES, HERRAMIENTAS
        Y/O EQUIPOS AL MOMENTO DE LA ENTREGA. ESTOS NO DEBERÁN PRESENTAR
        FALTANTES, DEFECTOS O ALTERACIONES; DE SER ASÍ, NO DEBERÁ RECIBIRLOS.
    </p>
</div>

<table class="signatures">
    <tr>
        <td class="signature-cell">
            <div class="signature-box">
                <div class="signature-title">Vo. Bo. autorización de salida</div>
                <div class="signature-space">
                    @if($firmaAutorizador ?? null)
                        <img class="signature-image"
                            width="145"
                            height="50"
                            src="{{ $firmaAutorizador }}"
                            alt="Firma de autorización">
                    @endif
                </div>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ mb_strtoupper(($firmanteAutorizador ?? null)?->name ?? 'ING. FRANCISCO MAGAÑA FIGUEROA') }}
                </div>
            </div>
        </td>
        <td class="signature-cell">
            <div class="signature-box">
                <div class="signature-title">Almacén general - entregó</div>
                <div class="signature-space">
                    @if($firmaAlmacen ?? null)
                        <img class="signature-image"
                            width="145"
                            height="50"
                            src="{{ $firmaAlmacen }}"
                            alt="Firma de almacén">
                    @endif
                </div>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ mb_strtoupper(($firmanteAlmacen ?? null)?->name ?? 'ING. JOSE JAVIER PERERA DE LA CRUZ') }}
                </div>
            </div>
        </td>
        <td class="signature-cell">
            <div class="signature-box">
                <div class="signature-title">Recibió material</div>
                <div class="signature-space">
                    @if($firmaSolicitante ?? null)
                        <img class="signature-image"
                            width="145"
                            height="50"
                            src="{{ $firmaSolicitante }}"
                            alt="Firma del solicitante">
                    @endif
                </div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ mb_strtoupper($solicitanteNombre) }}</div>
            </div>
        </td>
    </tr>
</table>

<div class="footer">
    Para dudas relacionadas con el suministro de materiales, comuníquese al
    993 175 5082 o a almacengeneral@cman.com.mx
</div>
</body>
</html>
