<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kayus', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kayu'); // Contoh: Jati, Mahoni, Sengon
            $table->string('dimensi');    // Contoh: 400x20x15
            $table->string('ukuran');     // Contoh: Besar, Sedang, Kecil
            $table->integer('stok')->default(0); // Default 0 saat pertama kali input
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kayus');
    }
};