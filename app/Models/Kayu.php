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
}
