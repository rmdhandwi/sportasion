<?php

use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Admin\Kategori;
use App\Http\Controllers\Admin\Products;
use App\Http\Controllers\Costumer\Costumer;
use App\Http\Controllers\Order\OrderController;
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

    Route::get('Home', [Costumer::class, 'costumerPage'])->name('costumerPage');
    Route::post('Cart/Add', [OrderController::class, 'addOrder'])->name('cart.add');
    Route::post('Cart/Update', [OrderController::class, 'updateOrder'])->name('cart.update');

    Route::get('Kategori', [Kategori::class, 'kategoriPage'])->name('kategori');
    Route::post('AddKategori', [Kategori::class, 'AddKategori'])->name('addKategori');
    Route::put('EditKategori/{id}/edit', [Kategori::class, 'editKategori'])->name('editKategori');
    Route::delete('DeleteKategori/{id}/delete', [Kategori::class, 'deleteKategori'])->name('deleteKategori');

    Route::get('Products', [Products::class, 'productsPage'])->name('products');
    Route::post('Products', [Products::class, 'addProduct'])->name('addProduct');
    Route::post('Products/{id}/edit', [Products::class, 'editProduct'])->name('editProduct');
    Route::delete('Products/{id}/delete', [Products::class, 'deleteProduct'])->name('deleteProduct');
});

require __DIR__ . '/auth.php';
