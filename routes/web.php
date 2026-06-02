<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

use App\Http\Controllers\InventoryController;

// Route untuk halaman utama Dashboard
Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');

// Route untuk memproses form transaksi (Barang Masuk & Keluar)
Route::post('/barang-masuk/store', [InventoryController::class, 'storeBarangMasuk'])->name('barang-masuk.store');
Route::post('/barang-keluar/store', [InventoryController::class, 'storeBarangKeluar'])->name('barang-keluar.store');