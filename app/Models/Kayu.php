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
        'panjang',
        'stok',
    ];

    protected $casts = [
        'stok' => 'integer',
    ];

    // Relasi ke Barang Masuk
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    // Relasi ke Barang Keluar
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    /**
     * CATATAN PENTING:
     * Volume pada Laporan Mutasi TIDAK lagi diambil dari sini. Setiap transaksi
     * (BarangMasuk / BarangKeluar) punya panjang & diameter sendiri, sehingga
     * volume dihitung & dijumlahkan per-transaksi (lihat MutasiReportService).
     *
     * Accessor ini hanya untuk estimasi tampilan dashboard bila dibutuhkan, dan
     * sudah dibuat "aman" (tidak error) meski dimensi berformat "400x20x15".
     */
    public function getVolumePerBatangAttribute(): float
    {
        // Ambil angka pertama dari string dimensi sebagai perkiraan diameter/sisi (cm)
        preg_match('/\d+(\.\d+)?/', (string) $this->dimensi, $m);
        $sisiCm = isset($m[0]) ? (float) $m[0] : 0.0;

        // panjang di tabel kayus bersifat label ("Besar"/"Sedang") -> tidak dipakai
        // untuk perhitungan. Kembalikan 0 bila data tidak memadai.
        if ($sisiCm <= 0) {
            return 0.0;
        }

        // Perkiraan kasar sebagai balok (sisi × sisi × 1 m). Hanya indikatif.
        $sisiM = $sisiCm / 100;

        return $sisiM * $sisiM * 1.0;
    }
}
