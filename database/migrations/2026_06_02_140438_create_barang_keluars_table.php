<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel kayus
            $table->foreignId('kayu_id')->constrained('kayus')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('customer')->nullable(); // Contoh: Bpk. Budi
            $table->dateTime('waktu_keluar');
            $table->string('kode_transaksi')->unique(); // Contoh: SJ-OUT-101
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};