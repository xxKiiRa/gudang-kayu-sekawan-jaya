<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kayu_id',
        'ukuran',
        'jumlah',
        'panjang',
        'diameter',
        'volume',
        'jenis_penggunaan', // 'diolah_sendiri' | 'penggunaan_lain'
        'customer',
        'waktu_keluar',
        'kode_transaksi',
    ];

    protected $casts = [
        'waktu_keluar' => 'datetime',
        'panjang'      => 'float',
        'diameter'     => 'float',
        'volume'       => 'float',
    ];

    // Relasi BelongsTo: Riwayat keluar ini milik 1 jenis Kayu tertentu
    public function kayu()
    {
        return $this->belongsTo(Kayu::class);
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
