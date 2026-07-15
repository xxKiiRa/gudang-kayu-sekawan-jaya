<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kayu extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_kayu',
        'dimensi',
        'kategori', // label ukuran: "Besar"/"Sedang"/"Kecil" (dulu bernama "panjang")
        'stok',
    ];

    protected $casts = [
        'stok' => 'integer',
    ];

    // Relasi ke Barang Masuk (satu kayu punya banyak transaksi masuk)
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    // Relasi ke Barang Keluar (satu kayu punya banyak transaksi keluar)
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    /**
     * CATATAN: Volume pada Laporan Mutasi TIDAK diambil dari sini, melainkan
     * dijumlahkan dari volume tiap transaksi. Accessor ini hanya perkiraan kasar
     * untuk tampilan, dibuat aman agar tidak error walau dimensi berupa teks.
     */
    public function getVolumePerBatangAttribute(): float
    {
        preg_match('/\d+(\.\d+)?/', (string) $this->dimensi, $m);
        $sisiCm = isset($m[0]) ? (float) $m[0] : 0.0;

        if ($sisiCm <= 0) {
            return 0.0;
        }

        $sisiM = $sisiCm / 100;

        return $sisiM * $sisiM * 1.0;
    }
}
