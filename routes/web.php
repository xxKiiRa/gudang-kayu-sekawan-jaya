<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

// Halaman utama Dashboard
Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');

// API detail 1 jenis kayu (JSON)
Route::get('/api/kayu/{id}', [InventoryController::class, 'getKayuData'])->name('api.kayu');

// Master data kayu
Route::post('/kayu/store', [InventoryController::class, 'storeKayu'])->name('kayu.store');
Route::delete('/kayu/{id}', [InventoryController::class, 'destroyKayu'])->name('kayu.destroy');

// Transaksi masuk & keluar
Route::post('/barang-masuk/store', [InventoryController::class, 'storeBarangMasuk'])->name('barang-masuk.store');
Route::post('/barang-keluar/store', [InventoryController::class, 'storeBarangKeluar'])->name('barang-keluar.store');

// Export Laporan Mutasi (bulan & tahun dikirim via query string ?bulan=&tahun=)
Route::get('/laporan/export/excel', [InventoryController::class, 'exportExcel'])->name('laporan.excel');
Route::get('/laporan/export/pdf', [InventoryController::class, 'exportPdf'])->name('laporan.pdf');
