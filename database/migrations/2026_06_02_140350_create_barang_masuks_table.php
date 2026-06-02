<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel kayus
            $table->foreignId('kayu_id')->constrained('kayus')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('asal_supplier')->nullable(); // Contoh: CV. Kayu Makmur
            $table->dateTime('waktu_masuk');
            $table->string('kode_transaksi')->unique(); // Contoh: SJ-IN-001
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};