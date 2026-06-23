<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kayu extends Model
{
    use HasFactory;

    // Melindungi database dari input sembarangan (Mass Assignment)
    protected $fillable = [
        'jenis_kayu',
        'dimensi',
        'ukuran',
        'stok',
    ];

    // Relasi One-to-Many: 1 Jenis Kayu bisa punya banyak riwayat masuk
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    // Relasi One-to-Many: 1 Jenis Kayu bisa punya banyak riwayat keluar
    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    // Volume per batang dalam m3 jika dimensi tersimpan dalam cm (LxWxT)
    public function getVolumePerBatangAttribute(): float
    {
        $parts = preg_split('/[^0-9.]+/', $this->dimensi, -1, PREG_SPLIT_NO_EMPTY);
        if (count($parts) !== 3) {
            return 0.0;
        }

        [$length, $width, $height] = array_map('floatval', $parts);
        if ($length <= 0 || $width <= 0 || $height <= 0) {
            return 0.0;
        }

        return ($length * $width * $height) / 1000000;
    }
}
