<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTE AUTENTIKASI (bisa diakses tanpa login)
|--------------------------------------------------------------------------
| GET menampilkan halaman; POST memproses. Form memakai route('login') /
| route('register') sebagai action dengan method POST.
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| RUTE TERPROTEKSI (hanya bisa diakses setelah login)
|--------------------------------------------------------------------------
| Semua dibungkus middleware 'auth'. Jika belum login, Laravel otomatis
| mengarahkan ke halaman 'login'.
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');

    // API detail 1 jenis kayu (JSON)
    Route::get('/api/kayu/{id}', [InventoryController::class, 'getKayuData'])->name('api.kayu');

    // Master data kayu
    Route::post('/kayu/store', [InventoryController::class, 'storeKayu'])->name('kayu.store');
    Route::delete('/kayu/{id}', [InventoryController::class, 'destroyKayu'])->name('kayu.destroy');

    // ------------------------- Barang Masuk -----------------------------
    Route::post('/barang-masuk/store', [InventoryController::class, 'storeBarangMasuk'])->name('barang-masuk.store');
    Route::get('/barang-masuk/{id}/edit', [InventoryController::class, 'editBarangMasuk'])->name('barang-masuk.edit');
    Route::put('/barang-masuk/{id}', [InventoryController::class, 'updateBarangMasuk'])->name('barang-masuk.update');
    Route::delete('/barang-masuk/{id}', [InventoryController::class, 'destroyBarangMasuk'])->name('barang-masuk.destroy');

    // ------------------------- Barang Keluar ----------------------------
    Route::post('/barang-keluar/store', [InventoryController::class, 'storeBarangKeluar'])->name('barang-keluar.store');
    Route::get('/barang-keluar/{id}/edit', [InventoryController::class, 'editBarangKeluar'])->name('barang-keluar.edit');
    Route::put('/barang-keluar/{id}', [InventoryController::class, 'updateBarangKeluar'])->name('barang-keluar.update');
    Route::delete('/barang-keluar/{id}', [InventoryController::class, 'destroyBarangKeluar'])->name('barang-keluar.destroy');

    // Export Laporan Mutasi
    Route::get('/laporan/export/excel', [InventoryController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/export/pdf', [InventoryController::class, 'exportPdf'])->name('laporan.pdf');
});
