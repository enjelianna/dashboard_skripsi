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

        // ── Total kejadian ──
        $totalKejadian  = HasilCluster::sum('frekuensi');
        $kejadianTinggi = HasilCluster::where('cluster', 'cluster_1')->sum('frekuensi');
        $kejadianSedang = HasilCluster::where('cluster', 'cluster_2')->sum('frekuensi');
        $kejadianRendah = HasilCluster::where('cluster', 'cluster_0')->sum('frekuensi');


        // rata rata kejadian//
        $kejadianTinggi = round(
            HasilCluster::where('cluster','cluster_1')
            ->avg('frekuensi')
        );

        $kejadianSedang = round(
            HasilCluster::where('cluster','cluster_2')
            ->avg('frekuensi')
        );

        $kejadianRendah = round(
            HasilCluster::where('cluster','cluster_0')
            ->avg('frekuensi')
        );

        // ── Jenis bencana dominan (untuk donut chart) ──
        $jenisBencana = DB::table('data_bencana')
            ->select('disaster_type', DB::raw('COUNT(*) as total'))
            ->groupBy('disaster_type')
            ->orderByDesc('total')
            ->get();

        // ── Bencana dominan tiap cluster (untuk tabel karakteristik) ──
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

        // ── Data peta: normalisasi nama agar cocok dengan GeoJSON ──
        // GeoJSON NAME_2 format: "Bangkalan", "Kota Blitar", "Kota Malang"
        // DB format            : "Bangkalan Kabupaten", "Blitar Kota", "Malang Kota"
        // Normalisasi: buang kata Kabupaten/Kota, trim → "bangkalan", "blitar", "malang"
        // JS di blade juga melakukan hal yang sama, jadi matching pasti cocok.

        $mapData = DB::table('cluster_daerah_skripsi')
            ->select(
                DB::raw('`Kabupaten/Kota` as kabupaten'),
                DB::raw('Cluster as cluster'),
                DB::raw('Frekuensi as frekuensi')
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
