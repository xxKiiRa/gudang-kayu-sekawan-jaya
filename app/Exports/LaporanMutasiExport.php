<?php

namespace App\Exports;

use App\Models\Kayu;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class LaporanMutasiExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Hitung mutasi per jenis kayu
        $dataMutasi = Kayu::all()->map(function ($kayu) use ($bulanIni, $tahunIni) {
            $masuk = $kayu->barangMasuks()
                ->whereMonth('waktu_masuk', $bulanIni)
                ->whereYear('waktu_masuk', $tahunIni)
                ->sum('jumlah');

            $keluar = $kayu->barangKeluars()
                ->whereMonth('waktu_keluar', $bulanIni)
                ->whereYear('waktu_keluar', $tahunIni)
                ->sum('jumlah');

            $persediaanBulanIni = $kayu->stok;
            $persediaanBulanLalu = ($persediaanBulanIni + $keluar) - $masuk;
            $volumePerBatang = $kayu->volume_per_batang;

            return [
                'jenis_kayu'                => $kayu->jenis_kayu,
                'awal_batang'               => $persediaanBulanLalu,
                'volume_awal'               => $this->formatVolume($persediaanBulanLalu * $volumePerBatang),
                'masuk_batang'              => $masuk,
                'volume_masuk'              => $this->formatVolume($masuk * $volumePerBatang),
                'keluar_batang'             => $keluar,
                'volume_keluar'             => $this->formatVolume($keluar * $volumePerBatang),
                'jumlah_olah_sendiri'       => $keluar,
                'volume_olah_sendiri'       => $this->formatVolume($keluar * $volumePerBatang),
                'jumlah_penggunaan_lain'    => 0,
                'volume_penggunaan_lain'    => '0',
                'akhir_batang'              => $persediaanBulanIni,
                'volume_akhir'              => $this->formatVolume($persediaanBulanIni * $volumePerBatang),
                'keterangan'                => '',
            ];
        });

        return view('exports.laporan_mutasi', [
            'data'    => $dataMutasi,
            'tanggal' => Carbon::now()->translatedFormat('d F Y')
        ]);

    }

    private function formatVolume(float $volume): string
    {
        return $volume > 0 ? number_format($volume, 3, ',', '.') : '-';
    }

    /**
     * Memaksa gaya (Style) langsung ke program Excel
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Ambil baris paling bawah tempat tabel berakhir
        $highestRow = $sheet->getHighestRow();

        // 2. Beri Border (Garis Hitam) dan Rata Tengah pada area tabel (Mulai baris ke-7 sampai ujung)
        $sheet->getStyle('A7:P' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText' => true, // Teks tidak akan menabrak batas sel
            ],
        ]);

        // 3. Area Keterangan Atas (Header Profil UD) dibikin rata kiri
        $sheet->getStyle('A1:P6')->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'font' => [
                'size' => 11,
            ]
        ]);

        // 4. Spesifik menengahkan Judul Dokumen Utama
        $sheet->getStyle('I1')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
                'underline' => true
            ]
        ]);

        return [];
    }
}