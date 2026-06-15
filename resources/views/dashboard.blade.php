<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Klasterisasi Wilayah Rawan Bencana</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            overflow: hidden;
            height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #fff;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-logo {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #e63946, #f4a261);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
        }

        .topbar-title {
            line-height: 1.2;
        }

        .topbar-title h6 {
            font-size: 13px;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        .topbar-title small {
            font-size: 10px;
            color: #6b7280;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .notif-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 18px;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
        }

        .notif-badge {
            position: absolute;
            top: -2px; right: -4px;
            background: #e63946;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            border-radius: 10px;
            padding: 1px 4px;
            min-width: 16px;
            text-align: center;
        }

        .admin-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f3f4f6;
            border-radius: 20px;
            padding: 4px 12px 4px 6px;
        }

        .admin-avatar {
            width: 26px;
            height: 26px;
            background: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
        }

        .admin-chip span {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        /* ── MAIN LAYOUT ── */
        .main-content {
            margin-top: 52px;
            height: calc(100vh - 52px);
            display:grid;
            grid-template-columns:48% 52%;
            gap:12px;
            padding:12px;
            overflow:hidden;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width:100%;
            min-width:100%;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding: 14px;
        }

        .left-panel::-webkit-scrollbar { width: 4px; }
        .left-panel::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        .panel-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1px;
        }

        .panel-subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .method-select {
            font-size: 11px;
            padding: 5px 10px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            color: #374151;
            background: #fff;
            width: 100%;
            margin-bottom: 10px;
            cursor: pointer;
        }

        #map {
            width: 100%;
            height:340px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
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
            color: #374151;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .section-label {
            font-size: 11px;
            font-weight: 600;
            color: #374151;
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
            background: #f9fafb;
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
            color: #374151;
            flex: 1;
            margin-left: 7px;
        }

        .cluster-row .c-count {
            font-size: 11px;
            color: #6b7280;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            flex: 1;
            overflow-y: auto;
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
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .stat-card {
            border-radius: 12px;
            padding: 12px 14px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card .s-num {
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }

        .stat-card .s-label {
            font-size: 10px;
            opacity: .9;
            margin-top: 2px;
        }

        .stat-card .s-icon {
            font-size: 26px;
            opacity: .3;
        }

        .sc-blue   { background: linear-gradient(135deg, #2563eb, #3b82f6); }
        .sc-red    { background: linear-gradient(135deg, #dc2626, #ef4444); }
        .sc-orange { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .sc-green  { background: linear-gradient(135deg, #16a34a, #22c55e); }
        .sc-purple { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
        .sc-teal   { background: linear-gradient(135deg, #0d9488, #2dd4bf); }

        /* ── CHART ROW ── */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .chart-card .cc-title {
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        /* ── KARAKTERISTIK TABLE ── */
        .char-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .char-card .cc-title {
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .char-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .char-table th {
            background: #f3f4f6;
            padding: 6px 10px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .char-table td {
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
            color: #4b5563;
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

        .bc-tinggi  { background: #dc2626; }
        .bc-sedang  { background: #d97706; }
        .bc-rendah  { background: #16a34a; }
    </style>
</head>
<body>

<!-- ══════════════ TOP BAR ══════════════ -->
<div class="topbar">
    <div class="topbar-left">
        <div class="topbar-logo">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        <div class="topbar-title">
            <h6>Dashboard Klasterisasi Wilayah Rawan Bencana</h6>
            <small>Provinsi Jawa Timur, Periode 2021 – 2025</small>
        </div>
    </div>
    <div class="topbar-right">
        <button class="notif-btn">
            <i class="bi bi-bell"></i>
        </button>
        <button class="notif-btn">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-badge">5</span>
        </button>
        <div class="admin-chip">
            <div class="admin-avatar"><i class="bi bi-person-fill" style="font-size:12px"></i></div>
            <span>Admin BPBD</span>
        </div>
    </div>
</div>

<!-- ══════════════ MAIN ══════════════ -->
<div class="main-content">

    <!-- ══ LEFT PANEL ══ -->
    <div class="left-panel">
        <div class="panel-title">Dashboard Klasterisasi Wilayah Rawan Bencana</div>
        <div class="panel-subtitle">Provinsi Jawa Timur, Periode 2021 – 2025</div>

        <select class="method-select">
            <option>Metode: Algoritma K-Means Clustering</option>
        </select>

        <!-- MAP -->
        <div id="map"></div>

        <div class="map-legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#dc2626"></div>
                Rawan Tinggi &nbsp;<strong>{{ $tinggi }}</strong>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#f59e0b"></div>
                Rawan Sedang &nbsp;<strong>{{ $sedang }}</strong>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#16a34a"></div>
                Rawan Rendah &nbsp;<strong>{{ $rendah }}</strong>
            </div>
        </div>

        <!-- Cluster kejadian summary -->
        <div class="section-label">Distribusi Kejadian per Klaster</div>
        <div class="cluster-summary">
            <div class="cluster-row">
                <div class="dot" style="background:#dc2626"></div>
                <span class="c-name">Rawan Tinggi</span>
                <span class="c-count">{{ $kejadianTinggi }} kejadian</span>
            </div>
            <div class="cluster-row">
                <div class="dot" style="background:#f59e0b"></div>
                <span class="c-name">Rawan Sedang</span>
                <span class="c-count">{{ $kejadianSedang }} kejadian</span>
            </div>
            <div class="cluster-row">
                <div class="dot" style="background:#16a34a"></div>
                <span class="c-name">Rawan Rendah</span>
                <span class="c-count">{{ $kejadianRendah }} kejadian</span>
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
            <div class="stat-card sc-teal">
                <div>
                    <div class="s-num">{{ $tinggi }}</div>
                    <div class="s-label">Kabupaten Tinggi</div>
                </div>
                <i class="bi bi-geo-alt-fill s-icon"></i>
            </div>
            <div class="stat-card sc-green">
                <div>
                    <div class="s-num">{{ $rendah }}</div>
                    <div class="s-label">Klaster Rawan Rendah</div>
                </div>
                <i class="bi bi-check-circle-fill s-icon"></i>
            </div>
            <div class="stat-card sc-purple">
                <div>
                    <div class="s-num">{{ $rendah }}</div>
                    <div class="s-label">Klaster Rawan Rendah</div>
                </div>
                <i class="bi bi-bar-chart-fill s-icon"></i>
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
Chart.defaults.font.family = 'Poppins';
Chart.defaults.font.size   = 10;

// ── 1. BAR CHART: Frekuensi per Klaster ──
new Chart(document.getElementById('freqBarChart'), {
    type: 'bar',
    data: {
        labels: ['Rawan\nTinggi', 'Klaster Rawan\nSedang', 'Klaster Rawan\nRendah'],
        datasets: [{
            data: [kejadianTinggi, kejadianSedang, kejadianRendah],
            backgroundColor: ['#dc2626', '#f59e0b', '#16a34a'],
            borderRadius: 6,
            barThickness: 32
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 9 } } },
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
                '#2563eb',
                '#dc2626',
                '#f59e0b',
                '#16a34a',
                '#8b5cf6',
                '#14b8a6',
                '#6b7280'
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

// ── 4. LEAFLET MAP ──
const map = L.map('map').setView([-7.5, 112.5], 7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

function getColor(cluster) {
    if (cluster === 'cluster_1') return '#dc2626';
    if (cluster === 'cluster_2') return '#f59e0b';
    if (cluster === 'cluster_0') return '#16a34a';
    return '#d1d5db';
}

fetch('/geojson/jatim_kabupaten.geojson')
    .then(r => r.json())
    .then(geojson => {
        const layer = L.geoJSON(geojson, {
            style(feature) {
                const name = feature.properties.NAME_2 || '';
                const item = clusterData.find(x =>
                    x.kabupaten.toLowerCase() === name.toLowerCase()
                );
                return {
                    fillColor:   item ? getColor(item.cluster) : '#d1d5db',
                    fillOpacity: 0.8,
                    color:       '#ffffff',
                    weight:      1,
                    opacity:     1
                };
            },
            onEachFeature(feature, lyr) {
                const name = feature.properties.NAME_2 || '';
                const item = clusterData.find(x =>
                    x.kabupaten.toLowerCase() === name.toLowerCase()
                );
                const kategori = !item ? 'Tidak Ada Data'
                    : item.cluster === 'cluster_1' ? 'Rawan Tinggi'
                    : item.cluster === 'cluster_2' ? 'Rawan Sedang'
                    : 'Rawan Rendah';
                lyr.bindPopup(`
                <div style="min-width:220px">
                    <h6>${name}</h6>

                    <hr>

                    <b>Status :</b> ${kategori}<br>

                    <b>Frekuensi :</b>
                    ${item ? item.frekuensi : 0}<br>

                    <b>Bencana Dominan :</b>
                    ${item?.disaster_type ?? 'Tidak tersedia'}
                </div>
            `);
                lyr.on('mouseover', () => lyr.setStyle({ fillOpacity: 1 }));
                lyr.on('mouseout',  () => lyr.setStyle({ fillOpacity: 0.8 }));

                if(item){

                const center = lyr.getBounds().getCenter();

                const color =
                    item.cluster === 'cluster_1'
                    ? '#dc2626'
                    : item.cluster === 'cluster_2'
                    ? '#f59e0b'
                    : '#16a34a';

                L.circleMarker(center,{
                    radius:7,
                    fillColor:color,
                    color:'#fff',
                    weight:2,
                    fillOpacity:1
                }).addTo(map);
            }
            }
        }).addTo(map);
        map.fitBounds(layer.getBounds());
    })
    .catch(() => console.warn('GeoJSON belum tersedia'));
</script>

</body>
</html>
