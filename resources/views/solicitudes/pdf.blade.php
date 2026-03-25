<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud #{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;700&display=swap');

        :root {
            --navy:   #0a1628;
            --navy2:  #0f2044;
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

        /* ── BARRA DE ACCIONES ── */
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

        /* ── CONTENEDOR ── */
        .page-wrapper {
            max-width: 900px; margin: 68px auto 40px;
            background: #fff; box-shadow: 0 8px 32px rgba(10,22,40,0.18);
        }
        .top-stripe { height: 5px; background: var(--gold); }

        /* ── ENCABEZADO ── */
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

        /* ── STATUS BAR ── */
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

        /* ── CUERPO ── */
        .body-content { padding: 24px 28px; }

        /* ── SECCIÓN TÍTULO ── */
        .section-title {
            font-size: 8px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 2px; color: var(--gold); padding: 0 0 5px;
            margin-bottom: 10px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .section-title::before { content: ''; display: inline-block; width: 3px; height: 12px; background: var(--gold); border-radius: 1px; }

        /* ── GRID INFO ── */
        .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid var(--border); margin-bottom: 20px; }
        .info-block { padding: 12px 16px; border-right: 1px solid var(--border); }
        .info-block:last-child { border-right: none; }
        .info-block-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid var(--light); }

        .field-row { display: flex; align-items: baseline; gap: 4px; padding: 3px 0; border-bottom: 1px dotted #e8ecf2; font-size: 10.5px; }
        .field-row:last-child { border-bottom: none; }
        .field-label { color: var(--muted); flex-shrink: 0; min-width: 80px; font-size: 10px; }
        .field-value { font-weight: 600; color: var(--text); text-align: right; flex: 1; }

        /* ── SOLICITANTE ── */
        .user-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dotted #e8ecf2; }
        .user-initials {
            width: 34px; height: 34px; border-radius: 2px;
            background: var(--navy); color: var(--gold2);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0;
        }
        .user-name-text { font-size: 12px; font-weight: 700; color: var(--text); }
        .user-dept { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; }

        /* ── TABLA ── */
        table.tbl { width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 20px; border: 1px solid var(--border); }
        table.tbl thead tr { background: var(--navy); }
        table.tbl thead th { padding: 9px 12px; color: #c8d4ea; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; text-align: left; border-right: 1px solid rgba(255,255,255,0.08); }
        table.tbl thead th:last-child { border-right: none; text-align: right; }
        table.tbl tbody tr:nth-child(even) { background: #fafbfd; }
        table.tbl tbody tr:nth-child(odd)  { background: #fff; }
        table.tbl tbody td { padding: 8px 12px; border-bottom: 1px solid #eaeef5; border-right: 1px solid #eaeef5; color: var(--text); }
        table.tbl tbody td:last-child { border-right: none; text-align: right; font-weight: 700; }

        .row-num {
            width: 22px; height: 22px; background: var(--light); border: 1px solid var(--border);
            border-radius: 2px; display: inline-flex; align-items: center; justify-content: center;
            font-size: 9px; font-weight: 700; color: var(--muted); font-family: 'IBM Plex Mono', monospace;
        }
        .qty-val { font-family: 'IBM Plex Mono', monospace; font-weight: 700; font-size: 12px; color: var(--navy); }
        .pname { font-weight: 600; color: var(--text); }
        .pcat  { font-size: 9px; color: #94a3b8; margin-top: 1px; }

        /* ── TOTALES ── */
        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 20px; }
        .totals-box {
            background: var(--light); border: 1px solid var(--border);
            padding: 12px 20px; min-width: 260px;
        }
        .t-row { display: flex; justify-content: space-between; font-size: 10.5px; padding: 4px 0; color: var(--muted); border-bottom: 1px dotted #e0e5ef; }
        .t-row:last-child { border-bottom: none; }
        .t-row.final {
            border-top: 1.5px solid var(--gold); border-bottom: none;
            margin-top: 6px; padding-top: 8px;
            font-size: 13px; font-weight: 800; color: var(--navy);
        }

        /* ── OBSERVACIONES ── */
        .obs-block { border: 1px solid #e8d59a; border-left: 3px solid var(--gold); background: #fffdf2; padding: 12px 16px; margin-bottom: 24px; font-size: 10.5px; line-height: 1.5; }
        .obs-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #a07010; margin-bottom: 5px; }

        /* ── FIRMAS ── */
        .sign-section { border: 1px solid var(--border); margin-top: 4px; }
        .sign-grid { display: grid; grid-template-columns: 1fr 1fr; }
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

        /* ── FOOTER ── */
        .doc-footer {
            background: var(--navy); border-top: 2px solid var(--gold);
            padding: 10px 28px; display: flex; justify-content: space-between; align-items: center;
            font-size: 9px; color: #4a6088; font-family: 'IBM Plex Mono', monospace;
        }
        .fc { color: #6a80a8; font-family: 'IBM Plex Sans', sans-serif; letter-spacing: 0.5px; }

        /* ── PRINT ── */
        @media print {
            body { background: #fff; }
            .action-bar { display: none !important; }
            .page-wrapper { max-width: 100%; margin: 0; box-shadow: none; }
            @page { margin: 1cm 1.2cm; size: A4; }
        }
    </style>
</head>
<body>

    {{-- ── BARRA DE ACCIONES ── --}}
    <div class="action-bar">
        <span class="doc-ref">
            SOL &nbsp;<strong>#{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</strong>
            &nbsp;/&nbsp; {{ $solicitud->created_at->format('d.m.Y') }}
            &nbsp;/&nbsp;
            @if($solicitud->estatus === 'aprobado')
                <span style="color:#10b981; font-weight:700;">&#9679; APROBADO</span>
            @elseif($solicitud->estatus === 'pendiente')
                <span style="color:#f59e0b; font-weight:700;">&#9679; PENDIENTE</span>
            @else
                <span style="color:#ef4444; font-weight:700;">&#9679; DENEGADO</span>
            @endif
        </span>
        <div class="btns">
            <a href="{{ route('solicitudes.index') }}" class="btn-back">&#8592; Regresar</a>
            <button class="btn-print" onclick="window.print()">&#128438; Imprimir / Guardar PDF</button>
        </div>
    </div>

    <div class="page-wrapper">

        <div class="top-stripe"></div>

        {{-- ── ENCABEZADO ── --}}
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
                <div class="company-sub">Control de Inventario &nbsp;&middot;&nbsp; Almacén</div>
                <div class="company-tagline">Solicitud de Material</div>
            </div>
            <div class="header-doc">
                <div class="doc-type-label">No. de Solicitud</div>
                <div class="doc-folio">#{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="doc-date-small">Generado: {{ now()->format('d/m/Y H:i') }} hrs</div>
            </div>
        </div>


        {{-- CSS DEL FOLIO DEL DOCUMENTO  --}}

        <style>
            .status-bar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-folio {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 2px;
            font-size: 11px;
            opacity: 0.95;
        
        }

        </style>

        

        {{-- ── STATUS BAR ── --}}
        <div class="status-bar">
            <span class="status-label">Estatus General del Documento</span>

            <span class="status-folio">FOR-03-PRO-ALM-001</span>

            @if($solicitud->estatus === 'pendiente')
                <span class="badge badge-pendiente">&#9679; Pendiente</span>
            @elseif($solicitud->estatus === 'aprobado')
                <span class="badge badge-aprobado">&#10003; Aprobado</span>
            @else
                <span class="badge badge-denegado">&#10007; Denegado</span>
            @endif
        </div>

        <div class="body-content">

            {{-- ── INFO GENERAL ── --}}
            <div class="section-title">Datos Generales</div>
            <div class="info-grid-2">

                {{-- Datos de la solicitud --}}
                <div class="info-block">
                    <div class="info-block-title">Datos de la Solicitud</div>
                    <div class="field-row">
                        <span class="field-label">Folio</span>
                        <span class="field-value" style="font-family:'IBM Plex Mono',monospace;">#{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Fecha creación</span>
                        <span class="field-value">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Hora</span>
                        <span class="field-value">{{ $solicitud->created_at->format('H:i') }} hrs</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Destino</span>
                        <span class="field-value">{{ $solicitud->destino ?? 'No especificado' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Estatus</span>
                        <span class="field-value">{{ ucfirst($solicitud->estatus) }}</span>
                    </div>
                    @if($solicitud->estatus !== 'pendiente')
                    <div class="field-row">
                        <span class="field-label">Fecha resolución</span>
                        <span class="field-value">{{ $solicitud->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Solicitante --}}
                <div class="info-block">
                    <div class="info-block-title">Solicitante</div>
                    <div class="user-row">
                        <div class="user-initials">{{ substr($solicitud->user->name, 0, 1) }}</div>
                        <div>
                            <div class="user-name-text">{{ $solicitud->user->name }}</div>
                            <div class="user-dept">{{ ucfirst(str_replace('_', ' ', $solicitud->user->role)) }}</div>
                        </div>
                    </div>
                    @if($solicitud->user->email)
                    <div class="field-row">
                        <span class="field-label">Correo</span>
                        <span class="field-value" style="font-size:9.5px;">{{ $solicitud->user->email }}</span>
                    </div>
                    @endif
                    @if($solicitud->user->num_empleado ?? false)
                    <div class="field-row">
                        <span class="field-label">No. Empleado</span>
                        <span class="field-value">{{ $solicitud->user->num_empleado }}</span>
                    </div>
                    @endif
                    <div class="field-row">
                        <span class="field-label">Total productos</span>
                        <span class="field-value">{{ $solicitud->total_productos }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Total unidades</span>
                        <span class="field-value">{{ $solicitud->total_unidades }}</span>
                    </div>
                </div>

            </div>

            {{-- ── TABLA DE PRODUCTOS ── --}}
            <div class="section-title">Relación de Productos Solicitados</div>

            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:44px; text-align:center;">No.</th>
                        <th>Producto</th>
                        <th style="width:110px;">Categoría</th>
                        <th style="width:90px;">Medida</th>
                        <th style="width:100px; text-align:right;">Cant. Solicitada</th>
                        @if($solicitud->estatus === 'aprobado')
                        <th style="width:100px; text-align:right;">Cant. Aprobada</th>
                        @endif
                        @if($solicitud->total > 0)
                        <th style="width:90px; text-align:right;">Precio Unit.</th>
                        <th style="width:90px; text-align:right;">Subtotal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitud->detalles as $i => $detalle)
                    <tr>
                        <td style="text-align:center;"><span class="row-num">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span></td>
                        <td>
                            <div class="pname">{{ $detalle->inventario->nombre_producto ?? 'Producto no disponible' }}</div>
                            @if($detalle->inventario->descripcion ?? false)
                            <div class="pcat">{{ Str::limit($detalle->inventario->descripcion, 55) }}</div>
                            @endif
                        </td>
                        <td style="color:var(--muted);">{{ $detalle->inventario->categoria ?? '&mdash;' }}</td>
                        <td style="color:var(--muted);">{{ $detalle->inventario->medida ?? '&mdash;' }}</td>
                        <td><span class="qty-val">{{ $detalle->cantidad_solicitada }}</span></td>
                        @if($solicitud->estatus === 'aprobado')
                        <td style="color:#0a8a5c; font-weight:700; font-family:'IBM Plex Mono',monospace;">
                            {{ $detalle->cantidad_aprobada ?? $detalle->cantidad_solicitada }}
                        </td>
                        @endif
                        @if($solicitud->total > 0)
                        <td>${{ number_format($detalle->precio_unitario ?? 0, 2) }}</td>
                        <td>${{ number_format(($detalle->precio_unitario ?? 0) * $detalle->cantidad_solicitada, 2) }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:#b0b8c8; padding:20px; font-style:italic;">
                            Sin productos registrados en esta solicitud.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ── TOTALES ── --}}
            <div class="totals-wrap">
                <div class="totals-box">
                    <div class="t-row">
                        <span>Total de productos:</span>
                        <span style="font-weight:600; color:var(--text);">{{ $solicitud->total_productos }}</span>
                    </div>
                    <div class="t-row">
                        <span>Total de unidades:</span>
                        <span style="font-weight:600; color:var(--text);">{{ $solicitud->total_unidades }}</span>
                    </div>
                    @if($solicitud->total > 0)
                    <div class="t-row final">
                        <span>TOTAL</span>
                        <span>${{ number_format($solicitud->total, 2) }} MXN</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── OBSERVACIONES ── --}}
            @if(!empty($solicitud->comentario) || !empty($solicitud->observaciones))
            <div class="obs-block">
                <div class="obs-label">Comentarios / Observaciones</div>
                {{ $solicitud->comentario ?? $solicitud->observaciones }}
            </div>
            @endif

            {{-- ── FIRMAS ── --}}
            <div class="section-title">Firmas de Autorización</div>
            <div class="sign-section">
                <div class="sign-grid">

                    {{-- Firma 1: Solicitante --}}
                    <div class="sign-cell">
                        <div class="sign-img-zone">
                            @if($firmaUserBase64)
                                <img src="data:image/png;base64,{{ $firmaUserBase64 }}" alt="Firma solicitante">
                            @endif
                        </div>
                        <div class="sign-line"></div>
                        <div class="sign-person">{{ $solicitud->user->name }}</div>
                        <div class="sign-role-tag">Elaboró &nbsp;&middot;&nbsp; Solicitante</div>
                    </div>

                    {{-- Firma 2: Director General --}}
                    <div class="sign-cell">
                        <div class="sign-img-zone">
                            @if($firmaAdminBase64)
                                <img src="data:image/png;base64,{{ $firmaAdminBase64 }}" alt="Firma Director General">
                            @endif
                        </div>
                        <div class="sign-line"></div>
                        <div class="sign-person">Francisco Magaña</div>
                        <div class="sign-role-tag">Autorizó &nbsp;&middot;&nbsp; Director General</div>
                        @if($solicitud->estatus === 'pendiente')
                            <span class="sign-pill pill-pending">Pendiente de Autorización</span>
                        @elseif($solicitud->estatus === 'denegado')
                            <span class="sign-pill pill-denied">No Autorizado</span>
                        @endif
                    </div>

                </div>
            </div>

        </div>{{-- /body-content --}}

        {{-- ── FOOTER ── --}}
        <div class="doc-footer">
            <span>#{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }} &nbsp;/&nbsp; {{ $solicitud->created_at->format('d.m.Y') }}</span>
            <span class="fc">CMAN GLOBAL CONSTRUCTION &nbsp;&middot;&nbsp; DOCUMENTO OFICIAL</span>
            <span>{{ now()->format('d/m/Y H:i') }}</span>
        </div>

    </div>

</body>
</html>