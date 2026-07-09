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

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'panjang'     => 'float',
        'diameter'    => 'float',
    ];

    // Relasi BelongsTo: Riwayat masuk ini milik 1 jenis Kayu tertentu
    public function kayu()
    {
        return $this->belongsTo(Kayu::class);
    }

    /**
     * Volume total baris ini (m³), dihitung dari panjang (m), diameter (cm), jumlah.
     * Rumus kayu bulat (silinder): V = (π/4) × d² × L
     *  - d = diameter dikonversi dari cm ke meter
     *  - L = panjang dalam meter
     */
    public function getVolumeAttribute(): float
    {
        if (! $this->panjang || ! $this->diameter || ! $this->jumlah) {
            return 0.0;
        }

        $diameterMeter   = $this->diameter / 100; // cm -> m
        $volumePerBatang = (M_PI / 4) * ($diameterMeter ** 2) * $this->panjang;

        return $volumePerBatang * $this->jumlah;
    }
}
