<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kayu_id',
        'ukuran',
        'jumlah',
        'panjang',
        'diameter',
        'volume',
        'asal_supplier',
        'waktu_masuk',
        'kode_transaksi',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'panjang'     => 'float',
        'diameter'    => 'float',
        'volume'      => 'float',
    ];

    // Relasi BelongsTo: Riwayat masuk ini milik 1 jenis Kayu tertentu
    public function kayu()
    {
        return $this->belongsTo(Kayu::class);
    }


}
