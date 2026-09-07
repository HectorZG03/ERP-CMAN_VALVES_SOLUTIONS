<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vale EPP {{ $valepp->numero_vale }}</title>
    <style>
        @page {
            margin: 18px 22px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            line-height: 1.25;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .document {
            border: 1.4px solid #111827;
        }

        .header-table td {
            border: 1px solid #111827;
            padding: 4px 5px;
            text-align: center;
            vertical-align: middle;
        }

        .logo-cell {
            width: 20%;
            height: 105px;
        }

        .logo {
            display: block;
            width: 105px;
            max-height: 86px;
            margin: 0 auto;
            object-fit: contain;
        }

        .logo-fallback {
            color: #f5c400;
            font-size: 25px;
            font-weight: bold;
        }

        .company-name {
            height: 23px;
            font-family: DejaVu Serif, serif;
            font-size: 10px;
            font-weight: bold;
        }

        .control-label {
            background: #f8fafc;
            font-family: DejaVu Serif, serif;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .control-value {
            height: 18px;
            font-family: DejaVu Serif, serif;
            font-size: 8px;
        }

        .title-label {
            width: 11%;
            background: #fff2d1;
            font-family: DejaVu Serif, serif;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .document-title {
            height: 43px;
            background: #fff2d1;
            font-family: DejaVu Serif, serif;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: .2px;
        }

        .section-gap {
            height: 8px;
        }

        .info-table td {
            border: 1px solid #111827;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .info-label {
            width: 18%;
            background: #fff2d1;
            font-family: DejaVu Serif, serif;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .info-value-left {
            font-size: 9px;
            font-weight: bold;
            text-align: left;
        }

        .materials {
            table-layout: fixed;
        }

        .materials thead {
            display: table-header-group;
        }

        .materials tr {
            page-break-inside: avoid;
        }

        .materials th,
        .materials td {
            border: 1px solid #111827;
            vertical-align: middle;
        }

        .materials th {
            background: #fff2d1;
            padding: 5px 4px;
            font-family: DejaVu Serif, serif;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .materials td {
            padding: 5px 5px;
            font-size: 8.5px;
        }

        .quantity-column {
            width: 10%;
            text-align: center;
        }

        .description-column {
            width: 60%;
            font-weight: bold;
            text-align: center;
        }

        .size-column {
            width: 16%;
            font-weight: bold;
            text-align: center;
        }

        .date-column {
            width: 14%;
            text-align: center;
        }

        .economic-code {
            display: block;
            margin-top: 2px;
            color: #4b5563;
            font-size: 7px;
            font-weight: normal;
        }

        .empty-materials td {
            height: 36px;
            color: #6b7280;
            text-align: center;
        }

        .observations {
            page-break-inside: avoid;
        }

        .observations-title {
            border: 1px solid #111827;
            background: #fff2d1;
            padding: 5px;
            font-family: DejaVu Serif, serif;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .observations-content {
            min-height: 57px;
            border-right: 1px solid #111827;
            border-bottom: 1px solid #111827;
            border-left: 1px solid #111827;
            padding: 7px 9px;
            white-space: pre-line;
        }

        .signatures {
            page-break-inside: avoid;
            table-layout: fixed;
        }

        .signatures td {
            width: 50%;
            height: 115px;
            padding: 6px 18px 5px;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-space {
            position: relative;
            height: 63px;
        }

        .signature-image {
            position: absolute;
            right: 0;
            bottom: 2px;
            left: 0;
            display: block;
            max-width: 150px;
            max-height: 59px;
            margin: 0 auto;
        }

        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 4px;
        }

        .signature-name {
            min-height: 12px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-role {
            margin-top: 2px;
            font-size: 7.5px;
            text-transform: uppercase;
        }

        .document-note {
            border-top: 1px solid #111827;
            padding: 4px 7px;
            color: #6b7280;
            font-size: 6.5px;
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $solicitud = $valepp->solicitudMaterial;
    $personal = $valepp->personal;

    $logoDataUri = null;
    $logoCandidates = [
        public_path('img/logo/logo_cman.png'),
        public_path('img/logo/logo.png'),
    ];

    foreach ($logoCandidates as $logoCandidate) {
        if (!is_file($logoCandidate)) {
            continue;
        }

        $logoMime = mime_content_type($logoCandidate) ?: 'image/png';
        $logoDataUri = 'data:' . $logoMime . ';base64,'
            . base64_encode(file_get_contents($logoCandidate));
        break;
    }

    $extraerTalla = static function (?string $nombre, ?string $economico): string {
        $nombreNormalizado = mb_strtoupper(trim((string) $nombre));

        $patrones = [
            '/TALLA\s*(?:#|NO\.?|NÚMERO|NUMERO|:)?\s*([0-9]{1,3}(?:\s*[A-Z]{1,3})?)/u',
            '/(?:NÚMERO|NUMERO)\s*(?:#|NO\.?|:)?\s*([0-9]{1,3}(?:\s*[A-Z]{1,3})?)/u',
        ];

        foreach ($patrones as $patron) {
            if (preg_match($patron, $nombreNormalizado, $coincidencia)) {
                return trim($coincidencia[1]);
            }
        }

        $economicoNormalizado = mb_strtoupper(trim((string) $economico));

        if (preg_match('/(?:ON|OV)([0-9]{2,3}[A-Z]*)$/', $economicoNormalizado, $coincidencia)) {
            return $coincidencia[1];
        }

        return 'N/A';
    };
@endphp

<div class="document">
    <table class="header-table">
        <tr>
            <td class="logo-cell" rowspan="4">
                @if($logoDataUri)
                    <img class="logo" src="{{ $logoDataUri }}" alt="CMAN">
                @else
                    <div class="logo-fallback">CMAN</div>
                    <div>VALVES SOLUTIONS</div>
                @endif
            </td>
            <td class="company-name" colspan="5">
                CMAN GLOBAL CONSTRUCTION SA DE CV
            </td>
        </tr>
        <tr>
            <td class="control-label">Revisión</td>
            <td class="control-label">Fecha de emisión original</td>
            <td class="control-label">Fecha de última revisión</td>
            <td class="control-label">Código</td>
            <td class="control-label">Página</td>
        </tr>
        <tr>
            <td class="control-value">3</td>
            <td class="control-value">13/10/2020</td>
            <td class="control-value">07/04/2026</td>
            <td class="control-value">FOR-01-PRO-SEG-009</td>
            <td class="control-value">1 de 1</td>
        </tr>
        <tr>
            <td class="title-label">Título</td>
            <td class="document-title" colspan="4">
                VALE PARA ENTREGA Y CAMBIO DE EPP
            </td>
        </tr>
    </table>

    <div class="section-gap"></div>

    <table class="info-table">
        <tr>
            <td class="info-label">Trabajador:</td>
            <td class="info-value-left" colspan="3">
                {{ mb_strtoupper($personal?->nombre_completo ?? 'N/A') }}
            </td>
            <td class="info-label">Fecha:</td>
            <td class="info-value">
                {{ $valepp->fecha_solicitud?->format('d/m/y') ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Categoría:</td>
            <td class="info-value" colspan="2">
                {{ mb_strtoupper($personal?->grado ?? $personal?->area ?? 'N/A') }}
            </td>
            <td class="info-label">Embarcación:</td>
            <td class="info-value" colspan="2">
                {{ mb_strtoupper($solicitud?->destino ?? $valepp->embarcacion ?? 'N/A') }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Solicitud EPP:</td>
            <td class="info-value" colspan="2">
                @if($solicitud)
                    #{{ str_pad((string) $solicitud->id, 4, '0', STR_PAD_LEFT) }}
                @else
                    N/A
                @endif
            </td>
            <td class="info-label">Vale EPP:</td>
            <td class="info-value" colspan="2">
                {{ $valepp->numero_vale }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Compañía:</td>
            <td class="info-value" colspan="5">
                CMAN GLOBAL CONSTRUCTION
            </td>
        </tr>
    </table>

    <div class="section-gap"></div>

    <table class="materials">
        <thead>
            <tr>
                <th class="quantity-column">Cantidad</th>
                <th class="description-column">Descripción</th>
                <th class="size-column">Talla/Número</th>
                <th class="date-column">Fecha de recibido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($valepp->detalles as $detalle)
                @php
                    $inventario = $detalle->inventario;
                    $talla = $extraerTalla(
                        $inventario?->nombre_producto,
                        $inventario?->economico
                    );
                @endphp
                <tr>
                    <td class="quantity-column">
                        {{ $detalle->cantidad }}
                    </td>
                    <td class="description-column">
                        {{ mb_strtoupper($inventario?->nombre_producto ?? 'PRODUCTO NO DISPONIBLE') }}
                        @if($inventario?->economico)
                            <span class="economic-code">
                                {{ $inventario->economico }}
                            </span>
                        @endif
                    </td>
                    <td class="size-column">
                        {{ $talla }}
                    </td>
                    <td class="date-column">
                        {{ $valepp->fecha_solicitud?->format('d/m/y') ?? 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr class="empty-materials">
                    <td colspan="4">SIN EQUIPOS ASIGNADOS</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="observations">
        <div class="observations-title">Observaciones</div>
        <div class="observations-content">{{ $valepp->observaciones ?: 'SIN OBSERVACIONES' }}</div>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-space">
                    @if($firmaHse ?? null)
                        <img
                            class="signature-image"
                            src="{{ $firmaHse }}"
                            alt="Firma HSE"
                        >
                    @endif
                </div>
                <div class="signature-line">
                    <div class="signature-name">
                        {{ $firmanteHse?->name ?? 'ING. WENDY GABRIELA VERRA GARCÍA' }}
                    </div>
                    <div class="signature-role">
                        Supervisor HSE CMAN Global Construction
                    </div>
                </div>
            </td>
            <td>
                <div class="signature-space"></div>
                <div class="signature-line">
                    <div class="signature-name">Recibe:</div>
                    <div class="signature-role">
                        Nombre y firma del trabajador
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="document-note">
        Documento generado el {{ $fechaGeneracion }} · Registro {{ $valepp->numero_vale }}
    </div>
</div>
</body>
</html>
