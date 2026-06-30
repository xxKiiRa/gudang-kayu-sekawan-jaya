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
     * Kalkulator Otomatis Volume per Batang (m3)
     * Menghitung volume berdasarkan Diameter (dimensi) dan Panjang kayu.
     */
    public function getVolumePerBatangAttribute(): float
    {
        // 1. Ambil angka murni dari kolom panjang (misal "400 cm" -> 400)
        $panjangStr = preg_replace('/[^0-9.]/', '', $this->panjang ?? '0');
        $length = floatval($panjangStr);

        // 2. Ambil angka murni dari kolom dimensi tunggal (misal "30 cm" -> 30)
        $dimensiStr = preg_replace('/[^0-9.]/', '', $this->dimensi ?? '0');
        $diameter = floatval($dimensiStr);

        if ($length <= 0 || $diameter <= 0) {
            return 0.0;
        }

        // RUMUS 1: Standar Kayu Bulat (Tabung / Lingkaran murni)
        // Rumus asli: 1/4 * pi * D^2 * P -> (0.7854 * D^2 * P) / 1.000.000 (satuan cm ke m3)
        return (0.7854 * pow($diameter, 2) * $length) / 1000000;

        /* ---------------------------------------------------------------------------------
        RUMUS 2 (Alternatif): Jika UD. SEKAWAN JAYA menghitung volume sebagai balok kotak persegi 
        (di mana Lebar = Tinggi = 30 cm), silakan aktifkan rumus di bawah ini dan hapus Rumus 1:
        
        return ($diameter * $diameter * $length) / 1000000;
        ---------------------------------------------------------------------------------
        */
    }
}