<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilCluster extends Model
{
    protected $table = 'cluster_daerah_skripsi';

    public $timestamps = false;

    protected $fillable = [
        'kabupaten',
        'cluster',
        'frekuensi',
    ];
}
