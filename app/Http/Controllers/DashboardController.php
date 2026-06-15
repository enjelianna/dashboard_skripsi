<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\HasilCluster;

class DashboardController extends Controller
{
    public function index()
    {
        $data = HasilCluster::all();

        // Jumlah wilayah per cluster
        $tinggi = hasilCluster::where('cluster', 'cluster_1')->count();
        $sedang = hasilCluster::where('cluster', 'cluster_2')->count();
        $rendah = hasilCluster::where('cluster', 'cluster_0')->count();

        // Total seluruh kejadian
        $totalKejadian = hasilCluster::sum('frekuensi');

        // Total frekuensi kejadian tiap cluster
        $kejadianTinggi = hasilCluster::where('cluster', 'cluster_1')
            ->sum('frekuensi');

        $kejadianSedang = hasilCluster::where('cluster', 'cluster_2')
            ->sum('frekuensi');

        $kejadianRendah = hasilCluster::where('cluster', 'cluster_0')
            ->sum('frekuensi');

        // ================== Jenis bencana dominan =================

        $jenisBencana = DB::table('data_bencana')
            ->select('disaster_type', DB::raw('COUNT(*) as total'))
            ->groupBy('disaster_type')
            ->orderByDesc('total')
            ->get();

        $donutLabels = $jenisBencana->pluck('disaster_type');
        $donutData = $jenisBencana->pluck('total');

        // Data untuk peta
        $mapData = DB::table('cluster_daerah_skripsi as c')
            ->leftJoin(
                'v_bencana_dominan as b',
                DB::raw('LOWER(TRIM(c.`Kabupaten/Kota`))'),
                '=',
                DB::raw('LOWER(TRIM(b.regency))')
            )
            ->select(
                DB::raw('c.`Kabupaten/Kota` as kabupaten'),
                'c.Cluster as cluster',
                'c.Frekuensi as frekuensi',
                'b.disaster_type'
            )
            ->get();

        return view('dashboard', compact(
            'data',
            'tinggi',
            'sedang',
            'rendah',
            'totalKejadian',
            'kejadianTinggi',
            'kejadianSedang',
            'kejadianRendah',
            'mapData',
            'jenisBencana',
            'donutLabels',
            'donutData'
));
    }
}