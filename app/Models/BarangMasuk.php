<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kayu_id',
        'jumlah',
        'panjang',
        'diameter',
        'asal_supplier',
        'waktu_masuk',
        'kode_transaksi',
    ];

    // Relasi BelongsTo: Riwayat masuk ini milik 1 jenis Kayu tertentu
    public function kayu()
    {
        return $this->belongsTo(Kayu::class);
    }

    // Hitung volume berdasarkan panjang (m), diameter (cm), dan jumlah
    // Volume silinder = π × (d/2)² × L × jumlah
    // d dalam cm, L dalam m
    public function getVolumeAttribute(): float
    {
        if (!$this->panjang || !$this->diameter || !$this->jumlah) {
            return 0.0;
        }

        $diameterInMeters = $this->diameter / 100; // cm ke meter
        $radiusInMeters = $diameterInMeters / 2;
        $volumePerBatang = pi() * ($radiusInMeters ** 2) * $this->panjang;
        
        return $volumePerBatang * $this->jumlah;
    }
}
