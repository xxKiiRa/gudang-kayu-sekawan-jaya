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
            $table->dropColumn(['dimensi', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kayus', function (Blueprint $table) {
            $table->string('dimensi', 100)->nullable()->after('jenis_kayu');
            $table->string('kategori', 100)->nullable()->after('dimensi');
        });
    }
};
