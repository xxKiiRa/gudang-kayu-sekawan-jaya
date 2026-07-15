<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kayu_id',
        'jumlah',
        'panjang',
        'diameter',
        'jenis_penggunaan', // 'diolah_sendiri' | 'penggunaan_lain'
        'customer',
        'waktu_keluar',
        'kode_transaksi',
    ];

    protected $casts = [
        'waktu_keluar' => 'datetime',
        'panjang'      => 'float',
        'diameter'     => 'float',
    ];

    // Relasi BelongsTo: Riwayat keluar ini milik 1 jenis Kayu tertentu
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

        $diameterMeter    = $this->diameter / 100; // cm -> m
        $volumePerBatang  = (M_PI / 4) * ($diameterMeter ** 2) * $this->panjang;

        return $volumePerBatang * $this->jumlah;
    }

    /**
     * Label rapi untuk jenis penggunaan (dipakai di tabel/laporan).
     */
    public function getJenisPenggunaanLabelAttribute(): string
    {
        return match ($this->jenis_penggunaan) {
            'penggunaan_lain' => 'Penggunaan Lain',
            default           => 'Diolah Sendiri',
        };
    }
}
