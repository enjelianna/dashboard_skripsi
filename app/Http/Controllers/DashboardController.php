<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\HasilCluster;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Jumlah wilayah per cluster ──
        $tinggi = HasilCluster::where('cluster', 'cluster_1')->count();
        $sedang = HasilCluster::where('cluster', 'cluster_2')->count();
        $rendah = HasilCluster::where('cluster', 'cluster_0')->count();

        // ── Total kejadian (sum) ──
        $totalKejadian = HasilCluster::sum('frekuensi');

        // ── Rata-rata frekuensi per cluster ──
        $kejadianTinggi = round(HasilCluster::where('cluster', 'cluster_1')->avg('frekuensi'));
        $kejadianSedang = round(HasilCluster::where('cluster', 'cluster_2')->avg('frekuensi'));
        $kejadianRendah = round(HasilCluster::where('cluster', 'cluster_0')->avg('frekuensi'));

        // ── Jenis bencana dominan (donut chart) ──
        $jenisBencana = DB::table('data_bencana')
            ->select('disaster_type', DB::raw('COUNT(*) as total'))
            ->groupBy('disaster_type')
            ->orderByDesc('total')
            ->get();

        // ── Bencana dominan tiap cluster (tabel karakteristik) ──
        $dominanTinggi = DB::table('v_bencana_dominan as v')
            ->join('cluster_daerah_skripsi as c',
                DB::raw('LOWER(TRIM(v.regency))'), '=',
                DB::raw('LOWER(TRIM(c.`Kabupaten/Kota`))'))
            ->where('c.Cluster', 'cluster_1')
            ->select('v.disaster_type', DB::raw('SUM(v.total) as ttl'))
            ->groupBy('v.disaster_type')
            ->orderByDesc('ttl')
            ->first();

        $dominanSedang = DB::table('v_bencana_dominan as v')
            ->join('cluster_daerah_skripsi as c',
                DB::raw('LOWER(TRIM(v.regency))'), '=',
                DB::raw('LOWER(TRIM(c.`Kabupaten/Kota`))'))
            ->where('c.Cluster', 'cluster_2')
            ->select('v.disaster_type', DB::raw('SUM(v.total) as ttl'))
            ->groupBy('v.disaster_type')
            ->orderByDesc('ttl')
            ->first();

        $dominanRendah = DB::table('v_bencana_dominan as v')
            ->join('cluster_daerah_skripsi as c',
                DB::raw('LOWER(TRIM(v.regency))'), '=',
                DB::raw('LOWER(TRIM(c.`Kabupaten/Kota`))'))
            ->where('c.Cluster', 'cluster_0')
            ->select('v.disaster_type', DB::raw('SUM(v.total) as ttl'))
            ->groupBy('v.disaster_type')
            ->orderByDesc('ttl')
            ->first();

        // ── Data peta + jenisDominan per kabupaten ──
        // Subquery: ambil disaster_type dengan total terbanyak per regency
        $mapData = DB::table('cluster_daerah_skripsi as c')
            ->leftJoin(
                DB::raw('(
                    SELECT regency,
                           disaster_type,
                           RANK() OVER (PARTITION BY regency ORDER BY total DESC) AS rn
                    FROM v_bencana_dominan
                ) AS vd'),
                function ($join) {
                    $join->on(
                        DB::raw('LOWER(TRIM(vd.regency))'), '=',
                        DB::raw('LOWER(TRIM(c.`Kabupaten/Kota`))')
                    )->where('vd.rn', '=', 1);
                }
            )
            ->select(
                DB::raw('c.`Kabupaten/Kota` AS kabupaten'),
                DB::raw('c.Cluster            AS cluster'),
                DB::raw('c.Frekuensi          AS frekuensi'),
                DB::raw('vd.disaster_type     AS jenisDominan')
            )
            ->get();

        return view('dashboard', compact(
            'tinggi', 'sedang', 'rendah',
            'totalKejadian',
            'kejadianTinggi', 'kejadianSedang', 'kejadianRendah',
            'mapData',
            'jenisBencana',
            'dominanTinggi', 'dominanSedang', 'dominanRendah'
        ));
    }
}
