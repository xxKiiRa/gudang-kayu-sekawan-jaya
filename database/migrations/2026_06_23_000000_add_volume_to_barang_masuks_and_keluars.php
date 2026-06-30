<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->decimal('volume', 10, 6)->nullable()->after('jumlah')->comment('Volume dalam m³');
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->decimal('volume', 10, 6)->nullable()->after('jumlah')->comment('Volume dalam m³');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->dropColumn('volume');
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropColumn('volume');
        });
    }
};
