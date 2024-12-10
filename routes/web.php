<?php

use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Costumer\Costumer;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Auth', [
        'title' => 'Login'
    ]);
});




Route::middleware('auth')->group(function () {
    Route::get('Dashboard', [Dashboard::class, 'dashboardPage'])->name('dashboard');

    Route::get('Home', [Costumer::class, 'costumerPage'])->name('costumer');
});

require __DIR__ . '/auth.php';
