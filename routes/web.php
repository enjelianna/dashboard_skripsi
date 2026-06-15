<?php

use Illuminate\Support\Facades\Route;
use App\Models\HasilCluster;
use App\Http\Controllers\DashboardController;

Route::get('/test-db', function () {
    return HasilCluster::all();
});


Route::get('/', [DashboardController::class, 'index']);
