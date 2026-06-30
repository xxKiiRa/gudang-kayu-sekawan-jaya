<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuks', function (Blueprint $table) {
            // Tambah kolom panjang dan diameter
            $table->decimal('panjang', 8, 2)->after('jumlah')->nullable()->comment('Panjang dalam meter');
            $table->decimal('diameter', 8, 2)->after('panjang')->nullable()->comment('Diameter dalam cm');
            // Drop kolom volume yang lama
            $table->dropColumn('volume');
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            // Tambah kolom panjang dan diameter
            $table->decimal('panjang', 8, 2)->after('jumlah')->nullable()->comment('Panjang dalam meter');
            $table->decimal('diameter', 8, 2)->after('panjang')->nullable()->comment('Diameter dalam cm');
            // Drop kolom volume yang lama
            $table->dropColumn('volume');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->decimal('volume', 10, 6)->nullable()->after('jumlah');
            $table->dropColumn(['panjang', 'diameter']);
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->decimal('volume', 10, 6)->nullable()->after('jumlah');
            $table->dropColumn(['panjang', 'diameter']);
        });
    }
};
