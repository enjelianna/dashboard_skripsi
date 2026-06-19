<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Work+Sans:wght@400;500;600&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --ink:          #1B2430;
            --ink-soft:     #5E6877;
            --paper:        #ECEFE8;
            --surface:      #FFFFFF;
            --line:         #DBDFD4;
            --signal-high:  #B23A2E;
            --signal-mid:   #BD8327;
            --signal-low:   #3C7A56;
            --signal-info:  #2B5C73;
            --accent-violet:#6E5A9E;
            --accent-teal:  #2F8F82;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Work Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
        }

        img, svg, canvas { max-width: 100%; }

        .num-mono {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
        }

        /* ══════════════════════
           LAYOUT
        ══════════════════════ */
        .main-content {
            display: grid;
            grid-template-columns: 52% 48%;
            gap: 12px;
            padding: 12px;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 100%;
            min-width: 0;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            display: flex;
            flex-direction: column;
        }

        .panel-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 1px;
        }

        .panel-subtitle {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--ink-soft);
            margin-bottom: 10px;
        }

        /* ── SEARCH AREA ── */
        .search-area {
            margin-bottom: 10px;
            flex-shrink: 0;
        }

        .search-row {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        /* Wrapper posisi relative agar dropdown absolut mengikutinya */
        .search-input-wrap {
            position: relative;
            flex: 1 1 160px;
            min-width: 0;
        }

        .search-input-wrap input {
            width: 100%;
            font-size: 11px;
            padding: 6px 28px 6px 10px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--surface);
            color: var(--ink);
            outline: none;
            font-family: 'Work Sans', sans-serif;
        }

        .search-input-wrap input:focus {
            border-color: var(--signal-info);
        }

        /* Tombol ✕ di dalam input */
        .clear-input-btn {
            position: absolute;
            right: 7px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--ink-soft);
            font-size: 13px;
            line-height: 1;
            padding: 0;
            display: none;
        }

        .clear-input-btn.visible { display: block; }

        /* Dropdown autocomplete */
        .search-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            z-index: 1000;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(27,36,48,.12);
            overflow: hidden;
            display: none;
        }

        .search-dropdown.open { display: block; }

        .dropdown-item {
            padding: 7px 12px;
            font-size: 11px;
            cursor: pointer;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--line);
            transition: background .1s;
        }

        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover,
        .dropdown-item.kbd-active { background: var(--paper); }

        .dropdown-item .di-name { flex: 1; font-weight: 600; }

        .dropdown-item .di-badge {
            font-size: 9px;
            padding: 2px 7px;
            border-radius: 4px;
            color: #fff;
            flex-shrink: 0;
        }

        .di-badge.bc-tinggi { background: var(--signal-high); }
        .di-badge.bc-sedang { background: var(--signal-mid); }
        .di-badge.bc-rendah { background: var(--signal-low); }

        .dropdown-empty {
            padding: 10px 12px;
            font-size: 11px;
            color: var(--ink-soft);
            text-align: center;
        }

        /* Tags wilayah terpilih */
        .tags-row {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 7px;
        }

        .tag-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            color: #fff;
        }

        .tag-item.tc-tinggi { background: var(--signal-high); }
        .tag-item.tc-sedang { background: var(--signal-mid); }
        .tag-item.tc-rendah { background: var(--signal-low); }

        .tag-remove {
            background: rgba(255,255,255,.3);
            border: none;
            color: #fff;
            cursor: pointer;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            font-size: 10px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            flex-shrink: 0;
        }

        .tag-remove:hover { background: rgba(255,255,255,.5); }

        .clear-all-btn {
            font-size: 10px;
            color: var(--ink-soft);
            background: none;
            border: none;
            cursor: pointer;
            padding: 3px 6px;
            border-radius: 5px;
            text-decoration: underline;
            text-underline-offset: 2px;
            align-self: center;
        }

        .clear-all-btn:hover { color: var(--signal-high); }

        /* Cluster filter select */
        #clusterFilter {
            flex: 0 0 auto;
            font-size: 11px;
            padding: 5px 8px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--surface);
            color: var(--ink);
            font-family: 'Work Sans', sans-serif;
            cursor: pointer;
        }

        /* MAP */
        #map {
            width: 100%;
            flex: 1 1 auto;
            min-height: 380px;
            border-radius: 10px;
            border: 1px solid var(--line);
        }

        .map-legend {
            display: flex;
            gap: 14px;
            margin: 8px 0;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            color: var(--ink-soft);
            white-space: nowrap;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            min-width: 0;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Banner filter aktif */
        .filter-info {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            background: #EBF3FB;
            border: 1px solid #B5D4F4;
            border-radius: 8px;
            font-size: 11px;
            color: var(--signal-info);
        }

        .filter-info.visible { display: flex; }
        .filter-info strong { font-weight: 600; }

        .filter-info-clear {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--signal-info);
            cursor: pointer;
            font-size: 11px;
            text-decoration: underline;
            padding: 0;
        }

        /* ── STAT CARDS ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-top: 3px solid var(--ink-soft);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 0;
        }

        .stat-card .s-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            color: var(--ink);
            white-space: nowrap;
        }

        .stat-card .s-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--ink-soft);
            margin-top: 4px;
        }

        .stat-card .s-icon {
            font-size: 24px;
            opacity: .35;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .sc-blue   { border-top-color: var(--signal-info); }  .sc-blue   .s-icon { color: var(--signal-info); }
        .sc-red    { border-top-color: var(--signal-high); }  .sc-red    .s-icon { color: var(--signal-high); }
        .sc-orange { border-top-color: var(--signal-mid); }   .sc-orange .s-icon { color: var(--signal-mid); }
        .sc-green  { border-top-color: var(--signal-low); }   .sc-green  .s-icon { color: var(--signal-low); }

        /* ── CHART GRID ── */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .chart-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            min-width: 0;
            overflow: hidden;
        }

        .chart-card .cc-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .chart-box {
            position: relative;
            width: 100%;
            height: 210px;
        }

        .chart-box canvas { width: 100% !important; height: 100% !important; }

        .donut-scroll { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .donut-scroll::-webkit-scrollbar { height: 6px; }
        .donut-scroll::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }
        .donut-box { min-width: 320px; }

        /* ── DISTRIBUSI + CHAR CARDS ── */
        .char-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            min-width: 0;
        }

        .char-card .cc-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .cluster-summary { display: flex; flex-direction: column; gap: 6px; }

        .cluster-row {
            display: flex;
            align-items: center;
            background: var(--paper);
            border-radius: 8px;
            padding: 7px 10px;
            gap: 4px;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .cluster-row .dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .cluster-row .c-name { font-size: 11px; font-weight: 600; color: var(--ink); flex: 1; margin-left: 7px; }
        .cluster-row .c-count { font-size: 11px; color: var(--ink-soft); white-space: nowrap; font-family: 'JetBrains Mono', monospace; font-weight: 600; }

        .table-scroll { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .char-table {
            width: 100%;
            min-width: 460px;
            border-collapse: collapse;
            font-size: 11px;
        }

        .char-table th {
            background: var(--paper);
            padding: 6px 10px;
            text-align: left;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            color: var(--ink);
            border: 1px solid var(--line);
            white-space: nowrap;
        }

        .char-table td {
            padding: 6px 10px;
            border: 1px solid var(--line);
            color: var(--ink-soft);
            vertical-align: middle;
        }

        .badge-cluster {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
        }

        .bc-tinggi { background: var(--signal-high); }
        .bc-sedang { background: var(--signal-mid); }
        .bc-rendah { background: var(--signal-low); }

        /* ══════════════════════
           RESPONSIVE
        ══════════════════════ */
        @media (max-width: 992px) {
            .main-content { grid-template-columns: 1fr; }
            #map { height: 460px; flex: none; }
            .stat-grid { grid-template-columns: repeat(2,1fr); }
            .chart-grid { grid-template-columns: 1fr; }
            .chart-box { height: 190px; }
        }

        @media (max-width: 576px) {
            .main-content { padding: 8px; gap: 8px; }
            .left-panel, .right-panel { padding: 10px; }
            #map { height: 300px; flex: none; }
            .search-row { flex-direction: column; }
            #clusterFilter { width: 100%; }
            .stat-grid { grid-template-columns: repeat(2,1fr); gap: 8px; }
            .stat-card { padding: 10px; }
            .stat-card .s-num { font-size: 18px; }
            .stat-card .s-icon { font-size: 20px; }
            .chart-box { height: 175px; }
            .donut-box { min-width: 240px; }
        }

        @media (max-width: 380px) {
            .stat-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="main-content">

    <!-- ══ LEFT PANEL ══ -->
    <div class="left-panel">
        <div class="panel-title">Dashboard Klasterisasi Wilayah Rawan Bencana</div>
        <div class="panel-subtitle">Provinsi Jawa Timur, Periode 2021 – 2025</div>

        <!-- SEARCH -->
        <div class="search-area">
            <div class="search-row">
                <div class="search-input-wrap">
                    <input type="text" id="searchInput"
                        placeholder="Cari dan pilih wilayah..." autocomplete="off">
                    <button class="clear-input-btn" id="clearInputBtn" title="Hapus teks">✕</button>
                    <div class="search-dropdown" id="searchDropdown"></div>
                </div>
                <select id="clusterFilter">
                    <option value="all">Semua Klaster</option>
                    <option value="cluster_1">Rawan Tinggi</option>
                    <option value="cluster_2">Rawan Sedang</option>
                    <option value="cluster_0">Rawan Rendah</option>
                </select>
            </div>
            <div class="tags-row" id="tagsRow"></div>
        </div>

        <!-- MAP -->
        <div id="map"></div>

        <div class="map-legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#B23A2E"></div>
                Rawan Tinggi &nbsp;<strong class="num-mono" id="lgTinggi">{{ $tinggi }}</strong>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#BD8327"></div>
                Rawan Sedang &nbsp;<strong class="num-mono" id="lgSedang">{{ $sedang }}</strong>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#3C7A56"></div>
                Rawan Rendah &nbsp;<strong class="num-mono" id="lgRendah">{{ $rendah }}</strong>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT PANEL ══ -->
    <div class="right-panel">

        <!-- FILTER INFO BANNER -->
        <div class="filter-info" id="filterInfo">
            <i class="bi bi-funnel-fill" style="font-size:12px"></i>
            <span>Menampilkan: <strong id="filterInfoText"></strong></span>
            <button class="filter-info-clear" id="filterInfoClear">Reset filter</button>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card sc-blue">
                <div>
                    <div class="s-num" id="statTotal">{{ number_format($totalKejadian) }}</div>
                    <div class="s-label">Total Kejadian</div>
                </div>
                <i class="bi bi-activity s-icon"></i>
            </div>
            <div class="stat-card sc-red">
                <div>
                    <div class="s-num" id="statTinggi">{{ $tinggi }}</div>
                    <div class="s-label">Klaster Rawan Tinggi</div>
                </div>
                <i class="bi bi-exclamation-triangle-fill s-icon"></i>
            </div>
            <div class="stat-card sc-orange">
                <div>
                    <div class="s-num" id="statSedang">{{ $sedang }}</div>
                    <div class="s-label">Klaster Rawan Sedang</div>
                </div>
                <i class="bi bi-dash-circle-fill s-icon"></i>
            </div>
            <div class="stat-card sc-green">
                <div>
                    <div class="s-num" id="statRendah">{{ $rendah }}</div>
                    <div class="s-label">Klaster Rawan Rendah</div>
                </div>
                <i class="bi bi-check-circle-fill s-icon"></i>
            </div>
        </div>

        <!-- DISTRIBUSI -->
        <div class="char-card">
            <div class="cc-title">Distribusi Kejadian per Klaster</div>
            <div class="cluster-summary">
                <div class="cluster-row">
                    <div class="dot" style="background:#B23A2E"></div>
                    <span class="c-name">Rawan Tinggi</span>
                    <span class="c-count" id="distTinggi">{{ number_format($kejadianTinggi) }} kejadian</span>
                </div>
                <div class="cluster-row">
                    <div class="dot" style="background:#BD8327"></div>
                    <span class="c-name">Rawan Sedang</span>
                    <span class="c-count" id="distSedang">{{ number_format($kejadianSedang) }} kejadian</span>
                </div>
                <div class="cluster-row">
                    <div class="dot" style="background:#3C7A56"></div>
                    <span class="c-name">Rawan Rendah</span>
                    <span class="c-count" id="distRendah">{{ number_format($kejadianRendah) }} kejadian</span>
                </div>
            </div>
        </div>

        <!-- CHARTS -->
        <div class="chart-grid">
            <div class="chart-card">
                <div class="cc-title">Frekuensi Kejadian per Klaster</div>
                <div class="chart-box">
                    <canvas id="freqBarChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="cc-title">Jenis Bencana Dominan</div>
                <div class="donut-scroll">
                    <div class="chart-box donut-box">
                        <canvas id="jenisDonut"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL KARAKTERISTIK -->
        <div class="char-card">
            <div class="cc-title">Ringkasan Karakteristik Tiap Klaster</div>
            <div class="table-scroll">
                <table class="char-table">
                    <thead>
                        <tr>
                            <th>Klaster</th>
                            <th>Keterangan</th>
                            <th>Bencana Dominan</th>
                            <th>Wilayah Terdampak</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <tr>
                            <td><span class="badge-cluster bc-tinggi">Rawan Tinggi</span></td>
                            <td>Frekuensi Kejadian Tinggi</td>
                            <td>{{ $dominanTinggi->disaster_type ?? '-' }}</td>
                            <td id="tdTinggi">{{ $tinggi }} Kabupaten/Kota</td>
                        </tr>
                        <tr>
                            <td><span class="badge-cluster bc-sedang">Rawan Sedang</span></td>
                            <td>Frekuensi Kejadian Sedang</td>
                            <td>{{ $dominanSedang->disaster_type ?? '-' }}</td>
                            <td id="tdSedang">{{ $sedang }} Kabupaten/Kota</td>
                        </tr>
                        <tr>
                            <td><span class="badge-cluster bc-rendah">Rawan Rendah</span></td>
                            <td>Frekuensi Kejadian Rendah</td>
                            <td>{{ $dominanRendah->disaster_type ?? '-' }}</td>
                            <td id="tdRendah">{{ $rendah }} Kabupaten/Kota</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /right-panel -->
</div><!-- /main-content -->

<script>
/* ══════════════════════════════════════════
   DATA DARI BLADE
══════════════════════════════════════════ */
const clusterData     = @json($mapData);          /* [{kabupaten, cluster, frekuensi, jenisDominan}] */
const BASE_TINGGI     = {{ $tinggi }};
const BASE_SEDANG     = {{ $sedang }};
const BASE_RENDAH     = {{ $rendah }};
const BASE_KJ_TINGGI  = {{ $kejadianTinggi }};
const BASE_KJ_SEDANG  = {{ $kejadianSedang }};
const BASE_KJ_RENDAH  = {{ $kejadianRendah }};
const BASE_TOTAL      = {{ $totalKejadian }};
const jenisLabels     = @json($jenisBencana->pluck('disaster_type'));
const jenisValues     = @json($jenisBencana->pluck('total'));

/* ══════════════════════════════════════════
   STATE
══════════════════════════════════════════ */
let selectedWilayah    = [];   /* [{kabupaten, cluster}] */
let activeClusterFilter = 'all';
let kbdIndex           = -1;

/* ══════════════════════════════════════════
   HELPERS
══════════════════════════════════════════ */
function normName(s) {
    return s.toLowerCase()
            .replace(/\b(kabupaten|kota)\b/g, '')
            .replace(/\s+/g, ' ')
            .trim();
}

function getColor(c) {
    return c === 'cluster_1' ? '#B23A2E'
         : c === 'cluster_2' ? '#BD8327'
         : '#3C7A56';
}

function getLabel(c) {
    return c === 'cluster_1' ? 'Rawan Tinggi'
         : c === 'cluster_2' ? 'Rawan Sedang'
         : 'Rawan Rendah';
}

function getBadgeClass(c) {
    return c === 'cluster_1' ? 'bc-tinggi'
         : c === 'cluster_2' ? 'bc-sedang'
         : 'bc-rendah';
}

function getTagClass(c) {
    return c === 'cluster_1' ? 'tc-tinggi'
         : c === 'cluster_2' ? 'tc-sedang'
         : 'tc-rendah';
}

function fmtNum(n) {
    return Number(n).toLocaleString('id-ID');
}

/* ══════════════════════════════════════════
   FILTER + STATS (client-side dari clusterData)
══════════════════════════════════════════ */
function getFilteredData() {
    let data = clusterData;

    if (selectedWilayah.length > 0) {
        const sel = new Set(selectedWilayah.map(w => w.kabupaten));
        data = data.filter(d => sel.has(d.kabupaten));
    }

    if (activeClusterFilter !== 'all') {
        data = data.filter(d => d.cluster === activeClusterFilter);
    }

    return data;
}

function computeStats(data) {
    let tinggi = 0, sedang = 0, rendah = 0;
    let kjT = 0, kjS = 0, kjR = 0;

    data.forEach(d => {
        if (d.cluster === 'cluster_1') { tinggi++; kjT += Number(d.frekuensi); }
        else if (d.cluster === 'cluster_2') { sedang++; kjS += Number(d.frekuensi); }
        else { rendah++; kjR += Number(d.frekuensi); }
    });

    return {
        tinggi, sedang, rendah,
        kjTinggi: kjT, kjSedang: kjS, kjRendah: kjR,
        total: kjT + kjS + kjR
    };
}

/* ══════════════════════════════════════════
   UPDATE DASHBOARD
══════════════════════════════════════════ */
function updateDashboard() {
    const data  = getFilteredData();
    const isFiltered = selectedWilayah.length > 0 || activeClusterFilter !== 'all';
    const stats = isFiltered ? computeStats(data) : null;

    /* ── stat cards ── */
    document.getElementById('statTotal').textContent  = isFiltered ? fmtNum(stats.total)   : fmtNum(BASE_TOTAL);
    document.getElementById('statTinggi').textContent = isFiltered ? stats.tinggi           : BASE_TINGGI;
    document.getElementById('statSedang').textContent = isFiltered ? stats.sedang           : BASE_SEDANG;
    document.getElementById('statRendah').textContent = isFiltered ? stats.rendah           : BASE_RENDAH;

    /* ── legend ── */
    document.getElementById('lgTinggi').textContent = isFiltered ? stats.tinggi : BASE_TINGGI;
    document.getElementById('lgSedang').textContent = isFiltered ? stats.sedang : BASE_SEDANG;
    document.getElementById('lgRendah').textContent = isFiltered ? stats.rendah : BASE_RENDAH;

    /* ── distribusi ── */
    document.getElementById('distTinggi').textContent = fmtNum(isFiltered ? stats.kjTinggi : BASE_KJ_TINGGI) + ' kejadian';
    document.getElementById('distSedang').textContent = fmtNum(isFiltered ? stats.kjSedang : BASE_KJ_SEDANG) + ' kejadian';
    document.getElementById('distRendah').textContent = fmtNum(isFiltered ? stats.kjRendah : BASE_KJ_RENDAH) + ' kejadian';

    /* ── tabel wilayah terdampak ── */
    document.getElementById('tdTinggi').textContent = (isFiltered ? stats.tinggi : BASE_TINGGI) + ' Kabupaten/Kota';
    document.getElementById('tdSedang').textContent = (isFiltered ? stats.sedang : BASE_SEDANG) + ' Kabupaten/Kota';
    document.getElementById('tdRendah').textContent = (isFiltered ? stats.rendah : BASE_RENDAH) + ' Kabupaten/Kota';

    /* ── bar chart ── */
    barChart.data.datasets[0].data = isFiltered
        ? [stats.kjTinggi, stats.kjSedang, stats.kjRendah]
        : [BASE_KJ_TINGGI, BASE_KJ_SEDANG, BASE_KJ_RENDAH];
    barChart.update();

    /* ── donut chart ── */
    if (isFiltered) {
        const jenis = computeJenis(data);
        donutChart.data.labels                 = jenis.map(j => j.type);
        donutChart.data.datasets[0].data       = jenis.map(j => j.total);
    } else {
        donutChart.data.labels                 = jenisLabels;
        donutChart.data.datasets[0].data       = jenisValues;
    }
    donutChart.update();

    /* ── filter info banner ── */
    const fi = document.getElementById('filterInfo');
    if (isFiltered) {
        fi.classList.add('visible');
        const parts = [];
        if (selectedWilayah.length > 0)
            parts.push(selectedWilayah.map(w => w.kabupaten).join(', '));
        if (activeClusterFilter !== 'all')
            parts.push(getLabel(activeClusterFilter));
        document.getElementById('filterInfoText').textContent = parts.join(' · ');
    } else {
        fi.classList.remove('visible');
    }

    /* ── map markers ── */
    updateMap(data);
}

/* ══════════════════════════════════════════
   CHARTS
══════════════════════════════════════════ */
Chart.defaults.font.family = 'Work Sans';
Chart.defaults.font.size   = 10;
Chart.defaults.color       = '#5E6877';

const barChart = new Chart(document.getElementById('freqBarChart'), {
    type: 'bar',
    data: {
        labels: ['Rawan\nTinggi', 'Rawan\nSedang', 'Rawan\nRendah'],
        datasets: [{
            data: [BASE_KJ_TINGGI, BASE_KJ_SEDANG, BASE_KJ_RENDAH],
            backgroundColor: ['#B23A2E', '#BD8327', '#3C7A56'],
            borderRadius: 6,
            barThickness: 32
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#ECEFE8' }, ticks: { font: { size: 9 } } },
            x: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
    }
});

const donutChart = new Chart(document.getElementById('jenisDonut'), {
    type: 'doughnut',
    data: {
        labels: jenisLabels,
        datasets: [{
            data: jenisValues,
            backgroundColor: ['#2B5C73','#B23A2E','#BD8327','#3C7A56','#6E5A9E','#2F8F82','#8A8F87'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: window.innerWidth < 576 ? 'bottom' : 'right',
                labels: { boxWidth: 10, font: { size: 9 } }
            }
        }
    }
});

let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        const pos = window.innerWidth < 576 ? 'bottom' : 'right';
        if (donutChart.options.plugins.legend.position !== pos) {
            donutChart.options.plugins.legend.position = pos;
            donutChart.update();
        }
    }, 150);
});

/* ══════════════════════════════════════════
   LEAFLET MAP
══════════════════════════════════════════ */
const map = L.map('map').setView([-7.5, 112.5], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

/* lookup: normName → item */
const lookup = {};
clusterData.forEach(item => { lookup[normName(item.kabupaten)] = item; });

let allMarkers  = [];
let borderLayer = null;

function updateMap(activeData) {
    const activeKab = new Set(activeData.map(d => d.kabupaten));

    allMarkers.forEach(m => {
        const show = activeKab.has(m.kabupaten);

        if (show && !map.hasLayer(m.marker)) m.marker.addTo(map);
        else if (!show && map.hasLayer(m.marker)) map.removeLayer(m.marker);

        /* dim/undim marker element */
        if (m.el) m.el.style.opacity = show ? '1' : '0.25';
    });

    /* dim/undim border layer */
    if (borderLayer) {
        borderLayer.eachLayer(lyr => {
            if (!lyr._myItem) return;
            const active = activeKab.has(lyr._myItem.kabupaten);
            lyr.setStyle({ opacity: active ? 1 : 0.2, fillOpacity: 0 });
        });
    }
}

fetch('/skripsi_pemetaan/public/geojson/jatim_kabupaten.geojson')
    .then(r => r.json())
    .then(geojson => {

        /* ── border layer ── */
        borderLayer = L.geoJSON(geojson, {
            style: () => ({ fillOpacity: 0, color: '#facc15', weight: 1.5 }),
            onEachFeature(feature, lyr) {
                const name  = feature.properties.NAME_2 || '';
                const item  = lookup[normName(name)];
                lyr._myItem = item;
                const color = item ? getColor(item.cluster) : '#9ca3af';

                lyr.on('mouseover', function () { this.setStyle({ fillColor: color, fillOpacity: .2 }); });
                lyr.on('mouseout',  function () { this.setStyle({ fillOpacity: 0 }); });
                lyr.on('click', function () {
                    if (item) selectWilayah(item);
                    this.openPopup();
                });

                lyr.bindPopup(`
                    <b style="font-family:'Space Grotesk',sans-serif;">${name}</b><br>
                    <span style="background:${color};color:#fff;padding:1px 8px;border-radius:4px;font-size:10px">
                        ${item ? getLabel(item.cluster) : '-'}
                    </span><br>
                    Frekuensi: <b style="font-family:'JetBrains Mono',monospace;">${item ? item.frekuensi : '-'}</b> kejadian
                    ${item && item.jenisDominan ? '<br>Bencana Dominan: <b>' + item.jenisDominan + '</b>' : ''}
                `);
            }
        }).addTo(map);

        /* ── circle markers ── */
        geojson.features.forEach(feature => {
            const name = feature.properties.NAME_2 || '';
            const item = lookup[normName(name)];
            if (!item) return;

            const color  = getColor(item.cluster);
            const coords = [];

            function extract(c) {
                if (typeof c[0] === 'number') coords.push(c);
                else c.forEach(extract);
            }
            extract(feature.geometry.coordinates);

            const lat = coords.reduce((s, c) => s + c[1], 0) / coords.length;
            const lng = coords.reduce((s, c) => s + c[0], 0) / coords.length;

            /* div element yang akan di-dim saat filter */
            const el = document.createElement('div');
            el.style.cssText = `
                width:28px; height:28px;
                background:#fff;
                border:3px solid ${color};
                border-radius:50%;
                display:flex; align-items:center; justify-content:center;
                font-family:'JetBrains Mono',monospace;
                font-size:9px; font-weight:700;
                color:#1B2430;
                transition: opacity .2s;
            `;
            el.textContent = item.frekuensi;

            const marker = L.marker([lat, lng], {
                icon: L.divIcon({ className: '', html: el, iconSize: [28, 28] })
            })
            .bindPopup(`
                <b style="font-family:'Space Grotesk',sans-serif;">${name}</b><br>
                <span style="background:${color};color:#fff;padding:1px 8px;border-radius:4px;font-size:10px">
                    ${getLabel(item.cluster)}
                </span><br>
                Frekuensi: <b style="font-family:'JetBrains Mono',monospace;">${item.frekuensi}</b> kejadian
                ${item.jenisDominan ? '<br>Bencana Dominan: <b>' + item.jenisDominan + '</b>' : ''}
            `)
            .addTo(map);

            marker.on('click', () => selectWilayah(item));

            allMarkers.push({ marker, kabupaten: item.kabupaten, cluster: item.cluster, el });
        });

        map.fitBounds(borderLayer.getBounds(), { padding: [6, 6] });
        setTimeout(() => map.invalidateSize(), 200);
    });

window.addEventListener('resize', () => { if (map) map.invalidateSize(); });

/* ══════════════════════════════════════════
   SEARCH — AUTOCOMPLETE + MULTI TAG
══════════════════════════════════════════ */
const searchInput   = document.getElementById('searchInput');
const dropdown      = document.getElementById('searchDropdown');
const tagsRow       = document.getElementById('tagsRow');
const clearInputBtn = document.getElementById('clearInputBtn');

/* Render dropdown */
function renderDropdown(keyword) {
    if (!keyword) { closeDropdown(); return; }

    const kw      = keyword.toLowerCase();
    const selSet  = new Set(selectedWilayah.map(w => w.kabupaten));

    let matches = clusterData
        .filter(d => !selSet.has(d.kabupaten))
        .filter(d => {
            const n = d.kabupaten.toLowerCase();
            return n.includes(kw) || normName(d.kabupaten).includes(kw);
        });

    /* Filter sesuai cluster filter aktif */
    if (activeClusterFilter !== 'all') {
        matches = matches.filter(d => d.cluster === activeClusterFilter);
    }

    /* Sort alfabet, max 5 */
    matches = matches
        .sort((a, b) => a.kabupaten.localeCompare(b.kabupaten, 'id'))
        .slice(0, 5);

    if (matches.length === 0) {
        dropdown.innerHTML = `<div class="dropdown-empty">Tidak ada wilayah yang cocok</div>`;
    } else {
        dropdown.innerHTML = matches.map((d, i) => `
            <div class="dropdown-item" data-kab="${d.kabupaten}" data-cluster="${d.cluster}" data-i="${i}">
                <span class="di-name">${d.kabupaten}</span>
                <span class="di-badge ${getBadgeClass(d.cluster)}">${getLabel(d.cluster)}</span>
            </div>
        `).join('');

        dropdown.querySelectorAll('.dropdown-item').forEach(el => {
            el.addEventListener('mousedown', e => {
                e.preventDefault();
                pickFromDropdown(el);
            });
        });
    }

    kbdIndex = -1;
    dropdown.classList.add('open');
}

function closeDropdown() {
    dropdown.classList.remove('open');
    kbdIndex = -1;
}

function pickFromDropdown(el) {
    addWilayah(el.dataset.kab, el.dataset.cluster);
    searchInput.value = '';
    clearInputBtn.classList.remove('visible');
    closeDropdown();
    searchInput.focus();
}

/* Pilih wilayah dari klik peta */
function selectWilayah(item) {
    if (selectedWilayah.find(w => w.kabupaten === item.kabupaten)) return;
    addWilayah(item.kabupaten, item.cluster);
}

function addWilayah(kab, cluster) {
    if (selectedWilayah.find(w => w.kabupaten === kab)) return;
    selectedWilayah.push({ kabupaten: kab, cluster });
    renderTags();
    updateDashboard();
}

function removeWilayah(kab) {
    selectedWilayah = selectedWilayah.filter(w => w.kabupaten !== kab);
    renderTags();
    updateDashboard();
}

function clearAllWilayah() {
    selectedWilayah = [];
    renderTags();
    updateDashboard();
}

function computeJenis(data) {
    const map = {};
    data.forEach(d => {
        if (d.jenisDominan) {
            map[d.jenisDominan] = (map[d.jenisDominan] || 0) + Number(d.frekuensi);
        }
    });
    return Object.entries(map)
        .sort((a, b) => b[1] - a[1])
        .map(([type, total]) => ({ type, total }));
}

/* Render tag pills */
function renderTags() {
    if (selectedWilayah.length === 0) { tagsRow.innerHTML = ''; return; }

    tagsRow.innerHTML =
        selectedWilayah.map(w => `
            <span class="tag-item ${getTagClass(w.cluster)}">
                ${w.kabupaten}
                <button class="tag-remove" data-kab="${w.kabupaten}" title="Hapus">✕</button>
            </span>
        `).join('')
        + `<button class="clear-all-btn" id="clearAllBtn">Hapus semua</button>`;

    tagsRow.querySelectorAll('.tag-remove').forEach(btn => {
        btn.addEventListener('click', () => removeWilayah(btn.dataset.kab));
    });

    document.getElementById('clearAllBtn').addEventListener('click', clearAllWilayah);
}

/* Input events */
searchInput.addEventListener('input', () => {
    const v = searchInput.value.trim();
    clearInputBtn.classList.toggle('visible', v.length > 0);
    renderDropdown(v);
});

searchInput.addEventListener('keydown', e => {
    const items = dropdown.querySelectorAll('.dropdown-item');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        kbdIndex = Math.min(kbdIndex + 1, items.length - 1);
        highlightKbd(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        kbdIndex = Math.max(kbdIndex - 1, 0);
        highlightKbd(items);
    } else if (e.key === 'Enter' && kbdIndex >= 0) {
        e.preventDefault();
        pickFromDropdown(items[kbdIndex]);
    } else if (e.key === 'Escape') {
        closeDropdown();
    }
});

function highlightKbd(items) {
    items.forEach((el, i) => el.classList.toggle('kbd-active', i === kbdIndex));
    if (kbdIndex >= 0) items[kbdIndex].scrollIntoView({ block: 'nearest' });
}

searchInput.addEventListener('blur', () => setTimeout(closeDropdown, 150));

clearInputBtn.addEventListener('click', () => {
    searchInput.value = '';
    clearInputBtn.classList.remove('visible');
    closeDropdown();
    searchInput.focus();
});

/* Cluster filter */
document.getElementById('clusterFilter').addEventListener('change', e => {
    activeClusterFilter = e.target.value;
    updateDashboard();
});

/* Reset dari banner */
document.getElementById('filterInfoClear').addEventListener('click', () => {
    selectedWilayah     = [];
    activeClusterFilter = 'all';
    document.getElementById('clusterFilter').value = 'all';
    searchInput.value = '';
    clearInputBtn.classList.remove('visible');
    renderTags();
    updateDashboard();
});
</script>

</body>
</html>
