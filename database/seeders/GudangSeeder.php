<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kayu;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class GudangSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Masukkan Data Master Kayu
        $jati = Kayu::create([
            'jenis_kayu' => 'Jati',
            'dimensi'    => '400x20x15',
            'ukuran'     => 'Besar',
            'stok'       => 150, // Sesuai data di dashboard sebelumnya
        ]);

        $mahoni = Kayu::create([
            'jenis_kayu' => 'Mahoni',
            'dimensi'    => '300x15x10',
            'ukuran'     => 'Sedang',
            'stok'       => 85,
        ]);

        $sengon = Kayu::create([
            'jenis_kayu' => 'Sengon',
            'dimensi'    => '200x10x5',
            'ukuran'     => 'Sedang',
            'stok'       => 320,
        ]);

        // 2. Masukkan Data Riwayat Barang Masuk
        BarangMasuk::create([
            'kayu_id'        => $jati->id,
            'jumlah'         => 150,
            'asal_supplier'  => 'CV. Kayu Makmur',
            'waktu_masuk'    => Carbon::parse('2026-04-07 08:30:00'),
            'kode_transaksi' => 'SJ-IN-001',
        ]);

        // 3. Masukkan Data Riwayat Barang Keluar
        BarangKeluar::create([
            'kayu_id'        => $sengon->id,
            'jumlah'         => 20,
            'customer'       => 'Bpk. Budi',
            'waktu_keluar'   => Carbon::parse('2026-04-07 10:15:00'),
            'kode_transaksi' => 'SJ-OUT-101',
        ]);
    }
}
