<?php

namespace App\Services;

use App\Models\Kayu;
use Carbon\Carbon;

/**
 * Membangun data "Laporan Mutasi Hasil Hutan (A1. Kayu Bulat)" untuk 1 periode
 * (bulan & tahun). Dipakai bersama oleh export PDF maupun Excel supaya logikanya
 * satu sumber (tidak ada duplikasi & tidak bisa beda hasil).
 *
 * Prinsip perhitungan (mengikuti kaidah form resmi):
 *   Persediaan Bulan Ini = Persediaan Bulan Lalu + Perolehan - Penggunaan
 *   (berlaku untuk Jumlah Batang MAUPUN Volume)
 *
 * Volume DIJUMLAHKAN dari volume tiap transaksi (masing-masing punya panjang &
 * diameter sendiri), BUKAN dari perkalian jumlah × volume tetap.
 */
class MutasiReportService
{
    /**
     * @return array{
     *   periode: array{bulan:int, tahun:int, label:string},
     *   rows: array<int, array<string, mixed>>,
     *   totals: array<string, float>
     * }
     */
    public function build(int $bulan, int $tahun): array
    {
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhirBulan = (clone $awalBulan)->endOfMonth();

        $rows = [];

        $totals = [
            'awal_batang'   => 0,   'awal_volume'   => 0.0,
            'masuk_batang'  => 0,   'masuk_volume'  => 0.0,
            'diolah_batang' => 0,   'diolah_volume' => 0.0,
            'lain_batang'   => 0,   'lain_volume'   => 0.0,
            'akhir_batang'  => 0,   'akhir_volume'  => 0.0,
        ];

        $daftarKayu = Kayu::orderBy('jenis_kayu')->get();

        foreach ($daftarKayu as $kayu) {
            // ---- Persediaan Bulan Lalu = seluruh mutasi SEBELUM awal bulan ini ----
            $masukSebelum  = $kayu->barangMasuks()->where('waktu_masuk', '<', $awalBulan)->get();
            $keluarSebelum = $kayu->barangKeluars()->where('waktu_keluar', '<', $awalBulan)->get();

            $awalBatang = $masukSebelum->sum('jumlah') - $keluarSebelum->sum('jumlah');
            $awalVolume = $masukSebelum->sum(fn ($t) => $t->volume) - $keluarSebelum->sum(fn ($t) => $t->volume);

            // ---- Perolehan (Barang Masuk) bulan ini ----
            $masukBulanIni = $kayu->barangMasuks()
                ->whereBetween('waktu_masuk', [$awalBulan, $akhirBulan])
                ->get();

            $masukBatang = $masukBulanIni->sum('jumlah');
            $masukVolume = $masukBulanIni->sum(fn ($t) => $t->volume);

            // ---- Penggunaan (Barang Keluar) bulan ini, dipisah 2 kategori ----
            $keluarBulanIni = $kayu->barangKeluars()
                ->whereBetween('waktu_keluar', [$awalBulan, $akhirBulan])
                ->get();

            $diolah = $keluarBulanIni->where('jenis_penggunaan', 'diolah_sendiri');
            $lain   = $keluarBulanIni->where('jenis_penggunaan', 'penggunaan_lain');

            $diolahBatang = $diolah->sum('jumlah');
            $diolahVolume = $diolah->sum(fn ($t) => $t->volume);
            $lainBatang   = $lain->sum('jumlah');
            $lainVolume   = $lain->sum(fn ($t) => $t->volume);

            // ---- Persediaan Bulan Ini ----
            $akhirBatang = $awalBatang + $masukBatang - $diolahBatang - $lainBatang;
            $akhirVolume = $awalVolume + $masukVolume - $diolahVolume - $lainVolume;

            // Lewati jenis kayu yang benar-benar tidak ada aktivitas & tidak ada stok
            $adaAktivitas = $awalBatang || $masukBatang || $diolahBatang || $lainBatang || $akhirBatang;
            if (! $adaAktivitas) {
                continue;
            }

            $rows[] = [
                'jenis_kayu'    => $kayu->jenis_kayu,
                'awal_batang'   => $awalBatang,
                'awal_volume'   => $awalVolume,
                'masuk_batang'  => $masukBatang,
                'masuk_volume'  => $masukVolume,
                'diolah_batang' => $diolahBatang,
                'diolah_volume' => $diolahVolume,
                'lain_batang'   => $lainBatang,
                'lain_volume'   => $lainVolume,
                'akhir_batang'  => $akhirBatang,
                'akhir_volume'  => $akhirVolume,
                'keterangan'    => '',
            ];

            foreach ([
                'awal_batang', 'awal_volume', 'masuk_batang', 'masuk_volume',
                'diolah_batang', 'diolah_volume', 'lain_batang', 'lain_volume',
                'akhir_batang', 'akhir_volume',
            ] as $key) {
                $totals[$key] += $rows[count($rows) - 1][$key];
            }
        }

        return [
            'periode' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'label' => $awalBulan->translatedFormat('F Y'),
            ],
            'rows'   => $rows,
            'totals' => $totals,
        ];
    }

    /**
     * Format angka volume ala Indonesia (ribuan titik, desimal koma), 3 desimal.
     * Nol ditampilkan sebagai "-" agar mirip form manual.
     */
    public static function fmt(float $volume): string
    {
        return abs($volume) > 0.0000001
            ? number_format($volume, 3, ',', '.')
            : '-';
    }

    /**
     * Format jumlah batang. Nol -> "-".
     */
    public static function fmtBatang(int $batang): string
    {
        return $batang !== 0 ? (string) $batang : '-';
    }
}
