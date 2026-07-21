<?php

namespace Database\Seeders;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kayu;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GudangSeeder extends Seeder
{
    public function run(): void
    {
        $bulanLalu = Carbon::now()->subMonthNoOverflow()->startOfMonth()->addDays(4);
        $bulanIni  = Carbon::now()->startOfMonth()->addDays(6);

        // ----------------------------- MASTER KAYU -----------------------------
        // 'kategori' = label ukuran (dulu bernama 'panjang')
        $jati   = Kayu::create(['jenis_kayu' => 'Jati',   'stok' => 0]);
        $mahoni = Kayu::create(['jenis_kayu' => 'Mahoni', 'stok' => 0]);
        $akasia = Kayu::create(['jenis_kayu' => 'Akasia', 'stok' => 0]);

        // ------------------- TRANSAKSI BULAN LALU (saldo awal) ------------------
        $this->masuk($jati,   120, 4.0, 22, 'CV. Kayu Makmur',   $bulanLalu, 'SJ-IN-000');
        $this->masuk($mahoni,  30, 3.0, 14, 'CV. Rimba Jaya',    $bulanLalu, 'SJ-IN-000B');
        $this->masuk($akasia,  10, 2.5, 12, 'UD. Hutan Lestari', $bulanLalu, 'SJ-IN-000C');

        // -------------------------- TRANSAKSI BULAN INI ------------------------
        $this->masuk($jati,   200, 4.0, 24, 'CV. Kayu Makmur',   (clone $bulanIni), 'SJ-IN-101');
        $this->masuk($mahoni,  40, 3.0, 15, 'CV. Rimba Jaya',    (clone $bulanIni)->addDay(), 'SJ-IN-102');
        $this->masuk($akasia,  25, 2.5, 13, 'UD. Hutan Lestari', (clone $bulanIni)->addDays(2), 'SJ-IN-103');

        $this->keluar($jati,  180, 4.0, 23, 'diolah_sendiri', 'Produksi Mebel', (clone $bulanIni)->addDays(3), 'SJ-OUT-201');
        $this->keluar($mahoni, 15, 3.0, 14, 'diolah_sendiri', 'Produksi Kusen', (clone $bulanIni)->addDays(3), 'SJ-OUT-202');
        $this->keluar($akasia, 20, 2.5, 12, 'diolah_sendiri', 'Produksi Palet', (clone $bulanIni)->addDays(4), 'SJ-OUT-203');

        $this->keluar($jati,   10, 4.0, 22, 'penggunaan_lain', 'Bpk. Budi (log)', (clone $bulanIni)->addDays(5), 'SJ-OUT-204');
    }

    private function masuk(Kayu $kayu, int $jumlah, float $panjang, float $diameter, ?string $supplier, Carbon $waktu, string $kode): void
    {
        BarangMasuk::create([
            'kayu_id' => $kayu->id, 'jumlah' => $jumlah, 'panjang' => $panjang,
            'diameter' => $diameter, 'asal_supplier' => $supplier,
            'waktu_masuk' => $waktu, 'kode_transaksi' => $kode,
        ]);
        $kayu->increment('stok', $jumlah);
    }

    private function keluar(Kayu $kayu, int $jumlah, float $panjang, float $diameter, string $jenisPenggunaan, ?string $customer, Carbon $waktu, string $kode): void
    {
        BarangKeluar::create([
            'kayu_id' => $kayu->id, 'jumlah' => $jumlah, 'panjang' => $panjang,
            'diameter' => $diameter, 'jenis_penggunaan' => $jenisPenggunaan,
            'customer' => $customer, 'waktu_keluar' => $waktu, 'kode_transaksi' => $kode,
        ]);
        $kayu->decrement('stok', $jumlah);
    }
}
