<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memisahkan penggunaan kayu keluar menjadi 2 kategori sesuai form
     * Laporan Mutasi Hasil Hutan: "Diolah Sendiri" dan "Penggunaan Lain".
     */
    public function up(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->enum('jenis_penggunaan', ['diolah_sendiri', 'penggunaan_lain'])
                ->default('diolah_sendiri')
                ->after('diameter')
                ->comment('Kategori penggunaan kayu keluar');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropColumn('jenis_penggunaan');
        });
    }
};
