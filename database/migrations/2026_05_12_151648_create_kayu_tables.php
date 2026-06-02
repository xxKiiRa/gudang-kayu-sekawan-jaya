<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Kayu
        Schema::create('kayus', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kayu');
            $table->enum('ukuran', ['OP', 'OD', 'OGD']);
            $table->string('dimensi');
            $table->integer('stok')->default(0);
            $table->string('satuan')->default('Batang');
            $table->timestamps();
        });

        // 2. Tabel Barang Masuk
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan');
            $table->date('tanggal_masuk');
            $table->string('nama_supplier');
            $table->string('asal_kayu');
            $table->foreignId('kayu_id')->constrained('kayus');
            $table->integer('jumlah');
            $table->timestamps();
        });

        // 3. Tabel Barang Keluar
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan');
            $table->date('tanggal_keluar');
            $table->string('nama_customer'); // Sesuai kebutuhan konsumen
            $table->foreignId('kayu_id')->constrained('kayus');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
        Schema::dropIfExists('barang_masuk');
        Schema::dropIfExists('kayus');
    }
};