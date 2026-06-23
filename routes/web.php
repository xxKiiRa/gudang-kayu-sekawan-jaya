<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

use App\Http\Controllers\InventoryController;

// Route untuk halaman utama Dashboard
Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');

// API untuk mendapatkan data kayu dengan volume
Route::get('/api/kayu/{id}', [InventoryController::class, 'getKayuData'])->name('api.kayu');

// Route untuk memproses form transaksi (Barang Masuk & Keluar)
Route::post('/barang-masuk/store', [InventoryController::class, 'storeBarangMasuk'])->name('barang-masuk.store');
Route::post('/barang-keluar/store', [InventoryController::class, 'storeBarangKeluar'])->name('barang-keluar.store');
Route::get('/laporan/export/excel', [InventoryController::class, 'exportExcel'])->name('laporan.excel');
Route::get('/laporan/export/pdf', [InventoryController::class, 'exportPdf'])->name('laporan.pdf');