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
            /* ── Palet "instrumen survei" ── */
            --ink:        #1B2430;   /* teks utama */
            --ink-soft:   #5E6877;   /* teks sekunder */
            --paper:      #ECEFE8;  /* latar halaman, kertas peta */
            --surface:    #FFFFFF;   /* permukaan kartu */
            --line:       #DBDFD4;   /* garis tepi tipis */

            --signal-high: #B23A2E; /* rawan tinggi — brick red */
            --signal-mid:  #BD8327; /* rawan sedang — ochre */
            --signal-low:  #3C7A56; /* rawan rendah — moss green */
            --signal-info: #2B5C73; /* info/total — teal-slate */
            --accent-violet:#6E5A9E;
            --accent-teal:  #2F8F82;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Work Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            overflow-y: auto;
            min-height: 100vh;
        }

        .num-mono {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
        }

        /* ── MAIN LAYOUT ── */
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
            min-width: 100%;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            overflow-y: auto;
        }

        .left-panel::-webkit-scrollbar { width: 4px; }
        .left-panel::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }

        .panel-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .01em;
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

        .method-select {
            font-size: 11px;
            padding: 5px 10px;
            border: 1px solid var(--line);
            border-radius: 7px;
            color: var(--ink);
            background: var(--surface);
            width: 100%;
            margin-bottom: 10px;
            cursor: pointer;
        }

        #map {
            width: 100%;
            height: 720px;
            border-radius: 10px;
            border: 1px solid var(--line);
        }

        .map-legend {
            display: flex;
            gap: 14px;
            margin: 8px 0;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            color: var(--ink-soft);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .section-label {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--ink);
            margin: 10px 0 6px;
        }

        /* Cluster summary table (bottom-left) */
        .cluster-summary {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .cluster-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--paper);
            border-radius: 8px;
            padding: 7px 10px;
        }

        .cluster-row .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .cluster-row .c-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--ink);
            flex: 1;
            margin-left: 7px;
        }

        .cluster-row .c-count {
            font-size: 11px;
            color: var(--ink-soft);
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            flex: 1;
            overflow: visible;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .right-panel::-webkit-scrollbar { width: 4px; }
        .right-panel::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

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
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card .s-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            color: var(--ink);
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
        }

        .sc-blue   { border-top-color: var(--signal-info); }
        .sc-blue   .s-icon { color: var(--signal-info); }
        .sc-red    { border-top-color: var(--signal-high); }
        .sc-red    .s-icon { color: var(--signal-high); }
        .sc-orange { border-top-color: var(--signal-mid); }
        .sc-orange .s-icon { color: var(--signal-mid); }
        .sc-green  { border-top-color: var(--signal-low); }
        .sc-green  .s-icon { color: var(--signal-low); }

        /* ── CHART ROW ── */
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
            box-shadow: 0 1px 3px rgba(27,36,48,.04);
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

        /* ── KARAKTERISTIK TABLE ── */
        .char-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(27,36,48,.04);
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

        .char-table {
            width: 100%;
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
        }

        .bc-tinggi  { background: var(--signal-high); }
        .bc-sedang  { background: var(--signal-mid); }
        .bc-rendah  { background: var(--signal-low); }
    </style>
</head>
<body>


<!-- ══════════════ MAIN ══════════════ -->
<div class="main-content">

    <!-- ══ LEFT PANEL ══ -->
    <div class="left-panel">
        <div class="panel-title">Dashboard Klasterisasi Wilayah Rawan Bencana</div>
        <div class="panel-subtitle">Provinsi Jawa Timur, Periode 2021 – 2025</div>

        <div style="display:flex;gap:8px;margin-bottom:10px;">
            <input type="text" id="searchInput" placeholder="Cari nama daerah..."
                class="form-control form-control-sm" style="font-size:11px;">
            <select id="clusterFilter" class="form-select form-select-sm"
                style="width:150px;font-size:11px;">
                <option value="all">Semua</option>
                <option value="cluster_1">Rawan Tinggi</option>
                <option value="cluster_2">Rawan Sedang</option>
                <option value="cluster_0">Rawan Rendah</option>
            </select>
        </div>

        <!-- MAP -->
        <div id="map"></div>

        <div class="map-legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#B23A2E"></div>
                Rawan Tinggi &nbsp;<strong class="num-mono">{{ $tinggi }}</strong>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#BD8327"></div>
                Rawan Sedang &nbsp;<strong class="num-mono">{{ $sedang }}</strong>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#3C7A56"></div>
                Rawan Rendah &nbsp;<strong class="num-mono">{{ $rendah }}</strong>
            </div>
        </div>

        <!-- Cluster kejadian summary -->
        <div class="section-label">Distribusi Kejadian per Klaster</div>
        <div class="cluster-summary">
            <div class="cluster-row">
                <div class="dot" style="background:#B23A2E"></div>
                <span class="c-name">Rawan Tinggi</span>
                <span class="c-count num-mono">{{ $kejadianTinggi }} kejadian</span>
            </div>
            <div class="cluster-row">
                <div class="dot" style="background:#BD8327"></div>
                <span class="c-name">Rawan Sedang</span>
                <span class="c-count num-mono">{{ $kejadianSedang }} kejadian</span>
            </div>
            <div class="cluster-row">
                <div class="dot" style="background:#3C7A56"></div>
                <span class="c-name">Rawan Rendah</span>
                <span class="c-count num-mono">{{ $kejadianRendah }} kejadian</span>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT PANEL ══ -->
    <div class="right-panel">

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card sc-blue">
                <div>
                    <div class="s-num">{{ number_format($totalKejadian) }}</div>
                    <div class="s-label">Total Kejadian</div>
                </div>
                <i class="bi bi-activity s-icon"></i>
            </div>
            <div class="stat-card sc-red">
                <div>
                    <div class="s-num">{{ $tinggi }}</div>
                    <div class="s-label">Klaster Rawan Tinggi</div>
                </div>
                <i class="bi bi-exclamation-triangle-fill s-icon"></i>
            </div>
            <div class="stat-card sc-orange">
                <div>
                    <div class="s-num">{{ $sedang }}</div>
                    <div class="s-label">Klaster Rawan Sedang</div>
                </div>
                <i class="bi bi-dash-circle-fill s-icon"></i>
            </div>
            <div class="stat-card sc-green">
                <div>
                    <div class="s-num">{{ $rendah }}</div>
                    <div class="s-label">Klaster Rawan Rendah</div>
                </div>
                <i class="bi bi-check-circle-fill s-icon"></i>
            </div>
        </div>

        <!-- CHART ROW -->
        <div class="chart-grid">
            <!-- Bar: Frekuensi per Klaster -->
            <div class="chart-card">
                <div class="cc-title">Frekuensi Kejadian per Klaster</div>
                <canvas id="freqBarChart" height="130"></canvas>
            </div>

            <!-- Donut: Jenis Bencana -->
            <div class="chart-card">
                <div class="cc-title">Jenis Bencana Dominan</div>
                <canvas id="jenisDonut" height="130"></canvas>
            </div>

        </div>

        <!-- KARAKTERISTIK TABLE -->
        <div class="char-card">
            <div class="cc-title">Ringkasan Karakteristik Tiap Klaster</div>
            <table class="char-table">
                <thead>
                    <tr>
                        <th>Klaster</th>
                        <th>Keterangan</th>
                        <th>Jenis Bencana Dominan</th>
                        <th>Wilayah Terdampak</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td><span class="badge-cluster bc-tinggi">Rawan Tinggi</span></td>
                    <td>Frekuensi Kejadian Tinggi</td>
                    <td>{{ $dominanTinggi->disaster_type ?? '-' }}</td>
                    <td>{{ $tinggi }} Kabupaten/Kota</td>
                </tr>

                <tr>
                    <td><span class="badge-cluster bc-sedang">Rawan Sedang</span></td>
                    <td>Frekuensi Kejadian Sedang</td>
                    <td>{{ $dominanSedang->disaster_type ?? '-' }}</td>
                    <td>{{ $sedang }} Kabupaten/Kota</td>
                </tr>

                <tr>
                    <td><span class="badge-cluster bc-rendah">Rawan Rendah</span></td>
                    <td>Frekuensi Kejadian Rendah</td>
                    <td>{{ $dominanRendah->disaster_type ?? '-' }}</td>
                    <td>{{ $rendah }} Kabupaten/Kota</td>
                </tr>
                </tbody>
                            </table>
                        </div>

                    </div><!-- /right-panel -->
                </div><!-- /main-content -->

                <script>
                // ── DATA FROM BLADE ──
                const clusterData = @json($mapData);
                const tinggi  = {{ $tinggi }};
                const sedang  = {{ $sedang }};
                const rendah  = {{ $rendah }};
                const kejadianTinggi = {{ $kejadianTinggi }};
                const kejadianSedang = {{ $kejadianSedang }};
                const kejadianRendah = {{ $kejadianRendah }};
                const jenisLabels = @json($jenisBencana->pluck('disaster_type'));
                const jenisValues = @json($jenisBencana->pluck('total'));

// ── CHART DEFAULTS ──
Chart.defaults.font.family = 'Work Sans';
Chart.defaults.font.size   = 10;
Chart.defaults.color       = '#5E6877';

// ── 1. BAR CHART: Frekuensi per Klaster ──
new Chart(document.getElementById('freqBarChart'), {
    type: 'bar',
    data: {
        labels: ['Rawan\nTinggi', 'Klaster Rawan\nSedang', 'Klaster Rawan\nRendah'],
        datasets: [{
            data: [kejadianTinggi, kejadianSedang, kejadianRendah],
            backgroundColor: ['#B23A2E', '#BD8327', '#3C7A56'],
            borderRadius: 6,
            barThickness: 32
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#ECEFE8' }, ticks: { font: { size: 9 } } },
            x: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
    }
});

// ── 2. DONUT: Jenis Bencana ──

new Chart(document.getElementById('jenisDonut'), {
    type: 'doughnut',
    data: {
        labels: jenisLabels,
        datasets: [{
            data: jenisValues,
            backgroundColor:[
                '#2B5C73',
                '#B23A2E',
                '#BD8327',
                '#3C7A56',
                '#6E5A9E',
                '#2F8F82',
                '#8A8F87'
            ],
            borderWidth:2,
            borderColor:'#fff'
        }]
    },
    options:{
        cutout:'65%',
        plugins:{
            legend:{
                position:'right',
                labels:{
                    boxWidth:10,
                    font:{size:9}
                }
            }
        }
    }
});

// ── LEAFLET MAP ──
const map = L.map('map').setView([-7.5, 112.5], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

function getColor(c) {
    return c === 'cluster_1' ? '#B23A2E' : c === 'cluster_2' ? '#BD8327' : c === 'cluster_0' ? '#3C7A56' : '#9ca3af';
}
function getLabel(c) {
    return c === 'cluster_1' ? 'Rawan Tinggi' : c === 'cluster_2' ? 'Rawan Sedang' : c === 'cluster_0' ? 'Rawan Rendah' : 'Tidak Ada Data';
}
function normName(str) {
    return str.toLowerCase().replace(/\bkabupaten\b/g,'').replace(/\bkota\b/g,'').replace(/\s+/g,' ').trim();
}

const lookup = {};
clusterData.forEach(item => { lookup[normName(item.kabupaten)] = item; });

// FIX #1: allMarkers harus dideklarasikan di scope luar (global),
// supaya bisa diakses oleh applyFilter() yang juga berada di luar
// callback fetch().then(...). Sebelumnya ini dideklarasikan dengan
// `const` DI DALAM callback, sehingga hilang begitu callback selesai
// dan applyFilter() melempar error "allMarkers is not defined" ->
// itulah sebabnya search & filter cluster berhenti berfungsi.
let allMarkers = [];
let borderLayer = null;

fetch('/skripsi_pemetaan/public/geojson/jatim_kabupaten.geojson')
    .then(r => r.json())
    .then(geojson => {

        // 1. Garis batas wilayah
        borderLayer = L.geoJSON(geojson, {
            style: () => ({ fillOpacity: 0, color: '#facc15', weight: 1.5 }),
            onEachFeature(feature, lyr) {
                const name  = feature.properties.NAME_2 || '';
                const item  = lookup[normName(name)];
                const color = item ? getColor(item.cluster) : '#9ca3af';
                lyr.on('mouseover', function() { this.setStyle({ fillColor: color, fillOpacity: 0.2 }); });
                lyr.on('mouseout',  function() { borderLayer.resetStyle(this); });
                lyr.on('click',     function() { this.openPopup(); });
                lyr.bindPopup(`<b style="font-family:'Space Grotesk',sans-serif;">${name}</b><br>
                    <span style="background:${color};color:#fff;padding:1px 8px;border-radius:4px;font-size:10px">${item ? getLabel(item.cluster) : '-'}</span><br>
                    Frekuensi: <b style="font-family:'JetBrains Mono', monospace;">${item ? item.frekuensi : '-'}</b> kejadian`);
            }
        }).addTo(map);

        // 2. Circle marker di tiap kabupaten
        geojson.features.forEach(feature => {

            const name = feature.properties.NAME_2 || '';
            const item = lookup[normName(name)];

            if (!item) return;

            const color = getColor(item.cluster);

            const coords = [];

            function extract(c){
                if(typeof c[0] === 'number'){
                    coords.push(c);
                }else{
                    c.forEach(extract);
                }
            }

            extract(feature.geometry.coordinates);

            const lat =
                coords.reduce((s,c)=>s+c[1],0) /
                coords.length;

            const lng =
                coords.reduce((s,c)=>s+c[0],0) /
                coords.length;

            // FIX #2: bindPopup ditambahkan ke marker juga.
            // Sebelumnya marker (lingkaran angka) menutupi polygon
            // di bawahnya sehingga klik tertangkap oleh marker yang
            // tidak punya popup -> klik di titik rawan terasa "mati".
            const marker = L.marker([lat,lng],{
                icon:L.divIcon({
                    className:'',
                    html:`<div style="
                        width:28px;
                        height:28px;
                        background:#fff;
                        border:3px solid ${color};
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-family:'JetBrains Mono', monospace;
                        font-size:9px;
                        font-weight:700;
                        color:#1B2430;">
                        ${item.frekuensi}
                    </div>`,
                    iconSize:[28,28]
                })
            })
            .bindPopup(`<b style="font-family:'Space Grotesk',sans-serif;">${name}</b><br>
                <span style="background:${color};color:#fff;padding:1px 8px;border-radius:4px;font-size:10px">${getLabel(item.cluster)}</span><br>
                Frekuensi: <b class="num-mono" style="font-family:'JetBrains Mono', monospace;">${item.frekuensi}</b> kejadian`)
            .addTo(map);

            allMarkers.push({
                marker:marker,
                cluster:item.cluster,
                wilayah:name.toLowerCase()
            });

        });

        map.fitBounds(borderLayer.getBounds(), { padding: [6,6] });
    });

function applyFilter(){
    const keyword =
        document.getElementById('searchInput')
        .value
        .trim()
        .toLowerCase();

    const cluster =
        document.getElementById('clusterFilter')
        .value;

    allMarkers.forEach(m => {

        const namaMatch =
            m.wilayah.includes(keyword);

        const clusterMatch =
            cluster === 'all'
            ||
            m.cluster === cluster;

        if(namaMatch && clusterMatch){

            if(!map.hasLayer(m.marker)){
                m.marker.addTo(map);
            }

        }else{

            if(map.hasLayer(m.marker)){
                map.removeLayer(m.marker);
            }

        }

    });

}

document
.getElementById('searchInput')
.addEventListener('input', applyFilter);

document
.getElementById('clusterFilter')
.addEventListener('change', applyFilter);

</script>

</body>
</html>