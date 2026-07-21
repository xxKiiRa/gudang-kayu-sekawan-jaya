<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kayu extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_kayu',
        'ukuran',
        'panjang',
        'diameter',
        'stok',
        'volume',
    ];

    protected $casts = [
        'stok' => 'integer',
        'panjang' => 'float',
        'diameter' => 'float',
        'volume' => 'float',
    ];

    public static function determineUkuran($panjang, $diameter)
    {
        if ($diameter <= 20) {
            return 'OP';
        } elseif ($diameter < 29) {
            return 'OD';
        } else {
            return 'OGD';
        }
    }

    public static function calculateVolume($panjang, $diameterCm, $jumlah)
    {
        if (! $panjang || ! $diameterCm || ! $jumlah) return 0.0;
        $diameterMeter = $diameterCm / 100;
        return (M_PI / 4) * ($diameterMeter ** 2) * $panjang * $jumlah;
    }

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


}
