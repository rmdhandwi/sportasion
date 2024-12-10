<?php

use App\Http\Controllers\Authen;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [Authen::class, 'loginPage'], [
        'title' => 'Login'
    ])->name('login');

    Route::post('register', [Authen::class, 'register'])
        ->name('register');

    Route::post('login', [Authen::class, 'login'])
        ->name('login');
});

Route::middleware('auth')->group(function () {

    Route::post('logout', [Authen::class, 'destroy'])
    ->name('logout');
    
});
