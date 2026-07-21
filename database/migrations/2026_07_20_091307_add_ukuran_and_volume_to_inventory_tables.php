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
        Schema::table('kayus', function (Blueprint $table) {
            $table->string('ukuran', 10)->nullable()->after('jenis_kayu')->comment('OP, OD, OGD');
            $table->decimal('panjang', 8, 2)->nullable()->after('ukuran');
            $table->decimal('diameter', 8, 2)->nullable()->after('panjang');
            $table->decimal('volume', 12, 4)->default(0)->after('stok');
            $table->unique(['jenis_kayu', 'ukuran'], 'kayu_ukuran_unique');
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->string('ukuran', 10)->nullable()->after('kayu_id');
            $table->decimal('volume', 12, 4)->default(0)->after('diameter');
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->string('ukuran', 10)->nullable()->after('kayu_id');
            $table->decimal('volume', 12, 4)->default(0)->after('diameter');
            // Change panjang and diameter to nullable as they won't be required anymore
            $table->decimal('panjang', 8, 2)->nullable()->change();
            $table->decimal('diameter', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kayus', function (Blueprint $table) {
            $table->dropUnique('kayu_ukuran_unique');
            $table->dropColumn(['ukuran', 'panjang', 'diameter', 'volume']);
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->dropColumn(['ukuran', 'volume']);
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropColumn(['ukuran', 'volume']);
            // Reverting back might be tricky without knowing original, but we can assume nullable=false
        });
    }
};
