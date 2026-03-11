<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Requisición {{ $requisicion->folio ?? '#' . str_pad($requisicion->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;700&display=swap');

        :root {
            --navy:   #0a1628;
            --navy2:  #0f2044;
            --blue:   #1a3a6e;
            --gold:   #c8922a;
            --gold2:  #e6a832;
            --light:  #f4f6f9;
            --border: #d0d7e3;
            --text:   #1a2035;
            --muted:  #5a6478;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'IBM Plex Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: var(--text);
            background: #e8ecf2;
        }

        .action-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: var(--navy); border-bottom: 2px solid var(--gold);
            padding: 9px 28px; display: flex; align-items: center; justify-content: space-between;
        }
        .doc-ref { color: #8a9bbf; font-size: 12px; font-family: 'IBM Plex Mono', monospace; }
        .doc-ref strong { color: #e2e8f2; }
        .btns { display: flex; gap: 10px; }
        .btn-print {
            background: var(--gold); color: var(--navy); border: none;
            padding: 6px 20px; font-size: 12px; font-weight: 700;
            cursor: pointer; border-radius: 3px; letter-spacing: 0.3px;
        }
        .btn-print:hover { background: var(--gold2); }
        .btn-back {
            background: transparent; color: #8a9bbf; border: 1px solid #2a3a5e;
            padding: 6px 18px; font-size: 12px; font-weight: 500; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border-radius: 3px;
        }
        .btn-back:hover { background: #1a2a4e; color: #c8d4ea; }

        .page-wrapper {
            max-width: 900px; margin: 68px auto 40px;
            background: #fff; box-shadow: 0 8px 32px rgba(10,22,40,0.18);
        }
        .top-stripe { height: 5px; background: var(--gold); }

        /* ENCABEZADO */
        .header { background: var(--navy); display: flex; align-items: stretch; }
        .header-logo-zone {
            background: var(--navy2); border-right: 1px solid rgba(200,146,42,0.25);
            padding: 18px 24px; display: flex; align-items: center; justify-content: center; min-width: 160px;
        }
        .header-logo-zone img { max-height: 52px; max-width: 140px; object-fit: contain; filter: brightness(0) invert(1); }
        .logo-fallback { color: #fff; font-size: 16px; font-weight: 800; letter-spacing: 1px; text-align: center; line-height: 1.2; }
        .logo-fallback span { display: block; font-size: 9px; font-weight: 400; color: var(--gold2); letter-spacing: 2px; margin-top: 3px; }
        .header-company { flex: 1; padding: 18px 22px; border-right: 1px solid rgba(200,146,42,0.2); }
        .company-name { font-size: 15px; font-weight: 700; color: #fff; letter-spacing: 0.5px; }
        .company-sub { font-size: 9px; color: var(--gold2); letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .company-tagline { font-size: 9px; color: #5a7aaa; margin-top: 6px; text-transform: uppercase; letter-spacing: 1px; }
        .header-doc { padding: 18px 24px; text-align: right; display: flex; flex-direction: column; justify-content: center; min-width: 200px; }
        .doc-type-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--gold); margin-bottom: 4px; }
        .doc-folio { font-family: 'IBM Plex Mono', monospace; font-size: 22px; font-weight: 700; color: #fff; letter-spacing: 2px; line-height: 1; }
        .doc-date-small { font-size: 9px; color: #5a7aaa; margin-top: 5px; }

        /* STATUS BAR */
        .status-bar {
            background: var(--navy2); border-bottom: 2px solid var(--gold);
            padding: 7px 28px; display: flex; align-items: center; justify-content: space-between;
        }
        .status-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; color: #5a7aaa; }
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 14px; font-size: 10px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase; border-radius: 2px;
        }
        .badge-pendiente { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); }
        .badge-aprobado  { background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.35); }
        .badge-denegado  { background: rgba(239,68,68,0.12);  color: #ef4444; border: 1px solid rgba(239,68,68,0.35); }

        /* CUERPO */
        .body-content { padding: 24px 28px; }

        /* ESTADO DUAL */
        .status-dual { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid var(--border); margin-bottom: 20px; }
        .status-cell { padding: 10px 16px; border-right: 1px solid var(--border); }
        .status-cell:last-child { border-right: none; }
        .status-cell-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); margin-bottom: 4px; }
        .status-cell-value { font-size: 12px; font-weight: 700; }
        .status-cell-by { font-size: 9px; color: var(--muted); margin-top: 2px; }
        .s-aprobado { color: #0a8a5c; } .s-pendiente { color: #c07800; } .s-denegado { color: #b91c1c; }
        .status-cell.aprobado-bg  { background: #f0faf5; border-top: 2px solid #0a8a5c; }
        .status-cell.pendiente-bg { background: #fffbf0; border-top: 2px solid #c07800; }
        .status-cell.denegado-bg  { background: #fff5f5; border-top: 2px solid #b91c1c; }

        /* SECCIÓN TÍTULO */
        .section-title {
            font-size: 8px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 2px; color: var(--gold); padding: 0 0 5px;
            margin-bottom: 10px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .section-title::before { content: ''; display: inline-block; width: 3px; height: 12px; background: var(--gold); border-radius: 1px; }

        /* GRID INFO */
        .info-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; border: 1px solid var(--border); margin-bottom: 20px; }
        .info-block { padding: 12px 16px; border-right: 1px solid var(--border); }
        .info-block:last-child { border-right: none; }
        .info-block-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid var(--light); }
        .field-row { display: flex; align-items: baseline; gap: 4px; padding: 3px 0; border-bottom: 1px dotted #e8ecf2; font-size: 10.5px; }
        .field-row:last-child { border-bottom: none; }
        .field-label { color: var(--muted); flex-shrink: 0; min-width: 70px; font-size: 10px; }
        .field-value { font-weight: 600; color: var(--text); text-align: right; flex: 1; }

        /* SOLICITANTE */
        .user-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dotted #e8ecf2; }
        .user-initials {
            width: 34px; height: 34px; border-radius: 2px;
            background: var(--navy); color: var(--gold2);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0;
        }
        .user-name-text { font-size: 12px; font-weight: 700; color: var(--text); }
        .user-dept { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; }

        /* TABLA */
        table.tbl { width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 20px; border: 1px solid var(--border); }
        table.tbl thead tr { background: var(--navy); }
        table.tbl thead th { padding: 9px 12px; color: #c8d4ea; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; text-align: left; border-right: 1px solid rgba(255,255,255,0.08); }
        table.tbl thead th:last-child { border-right: none; }
        table.tbl tbody tr:nth-child(even) { background: #fafbfd; }
        table.tbl tbody tr:nth-child(odd)  { background: #fff; }
        table.tbl tbody td { padding: 8px 12px; border-bottom: 1px solid #eaeef5; border-right: 1px solid #eaeef5; }
        table.tbl tbody td:last-child { border-right: none; }
        .row-num {
            width: 22px; height: 22px; background: var(--light); border: 1px solid var(--border);
            border-radius: 2px; display: inline-flex; align-items: center; justify-content: center;
            font-size: 9px; font-weight: 700; color: var(--muted); font-family: 'IBM Plex Mono', monospace;
        }
        .qty-val { font-family: 'IBM Plex Mono', monospace; font-weight: 700; font-size: 12px; color: var(--navy); }

        /* OBSERVACIONES */
        .obs-block { border: 1px solid #e8d59a; border-left: 3px solid var(--gold); background: #fffdf2; padding: 12px 16px; margin-bottom: 24px; font-size: 10.5px; line-height: 1.5; }
        .obs-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #a07010; margin-bottom: 5px; }

        /* FIRMAS */
        .sign-section { border: 1px solid var(--border); margin-top: 4px; }
        .sign-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; }
        .sign-cell { padding: 16px 20px 14px; text-align: center; border-right: 1px solid var(--border); }
        .sign-cell:last-child { border-right: none; }
        .sign-img-zone { height: 72px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 8px; }
        .sign-img-zone img { max-height: 68px; max-width: 180px; object-fit: contain; }
        .sign-line { border-top: 1.5px solid var(--navy); margin-bottom: 7px; }
        .sign-person { font-size: 11px; font-weight: 700; color: var(--text); }
        .sign-role-tag { font-size: 8px; text-transform: uppercase; letter-spacing: 1.2px; color: var(--muted); margin-top: 2px; }
        .sign-pill { display: inline-block; margin-top: 6px; padding: 2px 10px; border-radius: 2px; font-size: 8.5px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; }
        .pill-pending { background: #fef9e7; color: #a07010; border: 1px solid #f0d060; }
        .pill-denied  { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }

        /* FOOTER */
        .doc-footer {
            background: var(--navy); border-top: 2px solid var(--gold);
            padding: 10px 28px; display: flex; justify-content: space-between; align-items: center;
            font-size: 9px; color: #4a6088; font-family: 'IBM Plex Mono', monospace;
        }
        .fc { color: #6a80a8; font-family: 'IBM Plex Sans', sans-serif; letter-spacing: 0.5px; }

        /* PRINT */
        @media print {
            body { background: #fff; }
            .action-bar { display: none !important; }
            .page-wrapper { max-width: 100%; margin: 0; box-shadow: none; }
            @page { margin: 1cm 1.2cm; size: A4; }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <span class="doc-ref">
            REQ &nbsp;<strong>{{ $requisicion->folio ?? str_pad($requisicion->id, 4, '0', STR_PAD_LEFT) }}</strong>
            &nbsp;/&nbsp; {{ $requisicion->created_at->format('d.m.Y') }}
            &nbsp;/&nbsp;
            @if($requisicion->estatus === 'aprobado')
                <span style="color:#10b981; font-weight:700;">&#9679; APROBADO</span>
            @elseif($requisicion->estatus === 'pendiente')
                <span style="color:#f59e0b; font-weight:700;">&#9679; PENDIENTE</span>
            @else
                <span style="color:#ef4444; font-weight:700;">&#9679; DENEGADO</span>
            @endif
        </span>
        <div class="btns">
            <a href="{{ route('requisiciones.index') }}" class="btn-back">&#8592; Regresar</a>
            <button class="btn-print" onclick="window.print()">&#128438; Imprimir / Guardar PDF</button>
        </div>
    </div>

    <div class="page-wrapper">

        <div class="top-stripe"></div>

        {{-- ENCABEZADO --}}
        <div class="header">
            <div class="header-logo-zone">
                @php $logoPath = public_path('img/logo/logo_cman.png'); @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                         alt="CMAN Global Construction">
                @else
                    <div class="logo-fallback">CMAN<span>GLOBAL</span></div>
                @endif
            </div>
            <div class="header-company">
                <div class="company-name">CMAN GLOBAL CONSTRUCTION</div>
                <div class="company-sub">Control de Compras &nbsp;&middot;&nbsp; Abastecimiento</div>
                <div class="company-tagline">Requisición de Materiales y Servicios</div>
            </div>
            <div class="header-doc">
                <div class="doc-type-label">No. de Requisición</div>
                <div class="doc-folio">{{ $requisicion->folio ?? str_pad($requisicion->id, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="doc-date-small">Generado: {{ now()->format('d/m/Y H:i') }} hrs</div>
            </div>
        </div>

        {{-- STATUS BAR --}}
        <div class="status-bar">
            <span class="status-label">Estatus General del Documento</span>
            @if($requisicion->estatus === 'pendiente')
                <span class="badge badge-pendiente">&#9679; Pendiente</span>
            @elseif($requisicion->estatus === 'aprobado')
                <span class="badge badge-aprobado">&#10003; Aprobado</span>
            @else
                <span class="badge badge-denegado">&#10007; Denegado</span>
            @endif
        </div>

        <div class="body-content">

            {{-- ESTADO DUAL --}}
            <div class="status-dual">
                <div class="status-cell {{ $requisicion->estatus_finanzas === 'aprobado' ? 'aprobado-bg' : ($requisicion->estatus_finanzas === 'denegado' ? 'denegado-bg' : 'pendiente-bg') }}">
                    <div class="status-cell-label">Estado Finanzas</div>
                    <div class="status-cell-value {{ $requisicion->estatus_finanzas === 'aprobado' ? 's-aprobado' : ($requisicion->estatus_finanzas === 'denegado' ? 's-denegado' : 's-pendiente') }}">
                        {{ strtoupper($requisicion->estatus_finanzas) }}
                    </div>
                    @if($requisicion->aprobadorFinanzas)
                    <div class="status-cell-by">Revisado por: {{ $requisicion->aprobadorFinanzas->name }}</div>
                    @endif
                </div>
                <div class="status-cell {{ $requisicion->estatus === 'aprobado' ? 'aprobado-bg' : ($requisicion->estatus === 'denegado' ? 'denegado-bg' : 'pendiente-bg') }}">
                    <div class="status-cell-label">Estado Dirección</div>
                    <div class="status-cell-value {{ $requisicion->estatus === 'aprobado' ? 's-aprobado' : ($requisicion->estatus === 'denegado' ? 's-denegado' : 's-pendiente') }}">
                        {{ strtoupper($requisicion->estatus) }}
                    </div>
                    @if($requisicion->estatus !== 'pendiente')
                    <div class="status-cell-by">{{ $requisicion->updated_at->format('d/m/Y H:i') }} hrs</div>
                    @endif
                </div>
            </div>

            {{-- INFO GENERAL --}}
            <div class="section-title">Datos Generales</div>
            <div class="info-grid-3">

                <div class="info-block">
                    <div class="info-block-title">Solicitante</div>
                    <div class="user-row">
                        <div class="user-initials">{{ substr($requisicion->nombre_solicitante, 0, 1) }}</div>
                        <div>
                            <div class="user-name-text">{{ $requisicion->nombre_solicitante }}</div>
                            <div class="user-dept">{{ $requisicion->departamento }}</div>
                        </div>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Tipo Req.</span>
                        <span class="field-value">{{ ucfirst($requisicion->tipo_requerimiento) }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Fecha</span>
                        <span class="field-value">{{ $requisicion->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="info-block">
                    <div class="info-block-title">Datos del Documento</div>
                    <div class="field-row">
                        <span class="field-label">Folio</span>
                        <span class="field-value" style="font-family:'IBM Plex Mono',monospace;">{{ $requisicion->folio ?? 'N/A' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Proyecto</span>
                        <span class="field-value">{{ $requisicion->proyecto ?? 'N/A' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">SIT</span>
                        <span class="field-value">{{ $requisicion->sit ?? 'N/A' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Partida</span>
                        <span class="field-value">{{ $requisicion->partida ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="info-block">
                    <div class="info-block-title">Contrato / Ubicación</div>
                    @if($requisicion->contrato)
                    <div class="field-row">
                        <span class="field-label">Contrato</span>
                        <span class="field-value" style="font-size:9.5px;">{{ $requisicion->contrato->contrato ?? 'N/A' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Convenio</span>
                        <span class="field-value" style="font-size:9.5px;">{{ $requisicion->contrato->convenio ?? 'N/A' }}</span>
                    </div>
                    @endif
                    <div class="field-row">
                        <span class="field-label">Plataforma</span>
                        <span class="field-value">{{ $requisicion->plataforma ?? 'N/A' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Embarcación</span>
                        <span class="field-value">{{ $requisicion->embarcacion ?? 'N/A' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Área</span>
                        <span class="field-value">{{ $requisicion->area ?? 'N/A' }}</span>
                    </div>
                </div>

            </div>

            {{-- TABLA --}}
            <div class="section-title">Relación de Materiales / Servicios Solicitados</div>
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:44px; text-align:center;">No.</th>
                        <th style="width:90px;">Cantidad</th>
                        <th style="width:110px;">Unidad</th>
                        <th>Descripción del Material / Servicio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisicion->detalles as $i => $detalle)
                    <tr>
                        <td style="text-align:center;"><span class="row-num">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span></td>
                        <td><span class="qty-val">{{ $detalle->cantidad }}</span></td>
                        <td style="color:var(--muted);">{{ $detalle->unidad }}</td>
                        <td style="font-weight:500;">{{ $detalle->material }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#b0b8c8; padding:20px; font-style:italic;">
                            Sin materiales registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- OBSERVACIONES --}}
            @if(!empty($requisicion->comentario))
            <div class="obs-block">
                <div class="obs-label">Comentarios / Justificación</div>
                {{ $requisicion->comentario }}
            </div>
            @endif

            {{-- FIRMAS --}}
            <div class="section-title">Firmas de Autorización</div>
            <div class="sign-section">
                <div class="sign-grid">

                    <div class="sign-cell">
                        <div class="sign-img-zone">
                            @if($firmaUsuarioBase64)
                                <img src="data:image/png;base64,{{ $firmaUsuarioBase64 }}" alt="Firma solicitante">
                            @endif
                        </div>
                        <div class="sign-line"></div>
                        <div class="sign-person">{{ $requisicion->nombre_solicitante }}</div>
                        <div class="sign-role-tag">Elaboró &nbsp;&middot;&nbsp; Solicitante</div>
                    </div>

                    <div class="sign-cell">
                        <div class="sign-img-zone">
                            @if($firmaFinanzasBase64)
                                <img src="data:image/png;base64,{{ $firmaFinanzasBase64 }}" alt="Firma Finanzas">
                            @endif
                        </div>
                        <div class="sign-line"></div>
                        <div class="sign-person">{{ $requisicion->aprobadorFinanzas ? $requisicion->aprobadorFinanzas->name : 'Finanzas' }}</div>
                        <div class="sign-role-tag">Revisó &nbsp;&middot;&nbsp; Finanzas</div>
                        @if($requisicion->estatus_finanzas === 'pendiente')
                            <span class="sign-pill pill-pending">Pendiente de Revisión</span>
                        @elseif($requisicion->estatus_finanzas === 'denegado')
                            <span class="sign-pill pill-denied">No Aprobado</span>
                        @endif
                    </div>

                    <div class="sign-cell">
                        <div class="sign-img-zone">
                            @if($firmaDireccionBase64)
                                <img src="data:image/png;base64,{{ $firmaDireccionBase64 }}" alt="Firma Dirección">
                            @endif
                        </div>
                        <div class="sign-line"></div>
                        <div class="sign-person">Francisco Magaña</div>
                        <div class="sign-role-tag">Autorizó &nbsp;&middot;&nbsp; Director General</div>
                        @if($requisicion->estatus === 'pendiente')
                            <span class="sign-pill pill-pending">Pendiente de Autorización</span>
                        @elseif($requisicion->estatus === 'denegado')
                            <span class="sign-pill pill-denied">No Autorizado</span>
                        @endif
                    </div>

                </div>
            </div>

        </div>{{-- /body-content --}}

        <div class="doc-footer">
            <span>{{ $requisicion->folio ?? str_pad($requisicion->id, 4, '0', STR_PAD_LEFT) }} &nbsp;/&nbsp; {{ $requisicion->created_at->format('d.m.Y') }}</span>
            <span class="fc">CMAN GLOBAL CONSTRUCTION &nbsp;&middot;&nbsp; DOCUMENTO OFICIAL</span>
            <span>{{ now()->format('d/m/Y H:i') }}</span>
        </div>

    </div>

</body>
</html>