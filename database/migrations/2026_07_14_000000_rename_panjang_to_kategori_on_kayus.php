<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mengganti nama kolom "panjang" pada tabel kayus menjadi "kategori".
 *
 * ALASAN: kolom ini berisi LABEL ukuran ("Besar"/"Sedang"/"Kecil"), bukan
 * panjang dalam meter. Sementara kolom "panjang" di tabel transaksi (masuk/keluar)
 * berisi angka meter. Nama yang sama untuk arti berbeda membingungkan, sehingga
 * di master kayu diganti menjadi "kategori".
 *
 * Catatan: Laravel 12 mendukung renameColumn tanpa perlu paket tambahan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kayus', function (Blueprint $table) {
            $table->renameColumn('panjang', 'kategori');
        });
    }

    public function down(): void
    {
        Schema::table('kayus', function (Blueprint $table) {
            $table->renameColumn('kategori', 'panjang');
        });
    }
};
