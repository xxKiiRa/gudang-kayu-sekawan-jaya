<?php

namespace App\Exports;

use App\Services\MutasiReportService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Membuat file .xlsx "Laporan Mutasi Hasil Hutan (A1. Kayu Bulat)" dengan
 * struktur header bertingkat + border persis seperti form manual.
 *
 * Menggunakan PhpSpreadsheet langsung (sudah tersedia via maatwebsite/excel)
 * agar merge cell & border benar-benar terkontrol, dan hasilnya .xlsx asli
 * yang dibuka bersih di Excel (bukan file HTML berekstensi .xls).
 */
class LaporanMutasiExport
{
    public function __construct(
        private array $report
    ) {}

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet();
        $writer      = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildSpreadsheet(): Spreadsheet
    {
        $p      = config('perusahaan');
        $rows   = $this->report['rows'];
        $totals = $this->report['totals'];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mutasi Kayu Bulat');

        // ============================ KOP PERUSAHAAN ============================
        $kop = [
            ['Nama Perusahaan', $p['nama']],
            ['Alamat',          $p['alamat']],
            ['Jenis Industri',  $p['jenis_industri']],
            ['Lokasi Industri', $p['lokasi_industri']],
            ['Dinas Kehutanan', $p['dinas_kehutanan']],
            ['Propinsi',        $p['propinsi']],
        ];
        $r = 1;
        foreach ($kop as [$label, $val]) {
            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("C{$r}", ': ' . $val);
            $sheet->mergeCells("C{$r}:H{$r}");
            $r++;
        }

        // Judul di kanan kop
        $sheet->setCellValue('J1', 'LAPORAN MUTASI HASIL HUTAN');
        $sheet->mergeCells('J1:O1');
        $sheet->getStyle('J1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'underline' => Border::BORDER_NONE],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('J1')->getFont()->setUnderline(true);

        // Sub-judul bagian
        $sheet->setCellValue('A8', 'A1. KAYU BULAT');
        $sheet->getStyle('A8')->getFont()->setBold(true);

        // ============================ HEADER TABEL =============================
        // Baris header: 9,10,11 (deskripsi) + 12 (nomor kolom)
        $hStart = 9;

        // Grup atas (baris 9)
        $sheet->setCellValue('A9', 'No');
        $sheet->mergeCells('A9:A11');

        $sheet->setCellValue('B9', 'Persediaan Akhir Bulan Lalu');
        $sheet->mergeCells('B9:D10');

        $sheet->setCellValue('E9', 'Perolehan Kayu Bulat');
        $sheet->mergeCells('E9:G10');

        $sheet->setCellValue('H9', 'Penggunaan Kayu Bulat');
        $sheet->mergeCells('H9:L9');

        $sheet->setCellValue('M9', 'Persediaan Bulan Ini');
        $sheet->mergeCells('M9:O10');

        $sheet->setCellValue('P9', 'Keterangan');
        $sheet->mergeCells('P9:P11');

        // Sub-grup penggunaan (baris 10)
        $sheet->setCellValue('H10', 'Diolah Sendiri');
        $sheet->mergeCells('H10:J10');
        $sheet->setCellValue('K10', 'Penggunaan Lain');
        $sheet->mergeCells('K10:L10');

        // Sub-kolom (baris 11)
        $subKolom = [
            'B' => 'Jenis Kayu', 'C' => 'Jumlah Batang', 'D' => 'Volume M3/Ton',
            'E' => 'Jenis Kayu', 'F' => 'Jumlah Batang', 'G' => 'Volume M3/Ton',
            'H' => 'Jenis Kayu', 'I' => 'Jumlah Batang', 'J' => 'Volume M3/Ton',
            'K' => 'Jumlah Batang', 'L' => 'Volume M3/Ton',
            'M' => 'Jenis Kayu', 'N' => 'Jumlah Batang', 'O' => 'Volume M3/Ton',
        ];
        foreach ($subKolom as $col => $text) {
            $sheet->setCellValue("{$col}11", $text);
        }

        // Baris nomor kolom (baris 12): 1..16
        $kolomUrut = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
        foreach ($kolomUrut as $i => $col) {
            $sheet->setCellValue("{$col}12", $i + 1);
        }

        // ============================ ISI DATA =================================
        $dataStart = 13;
        $r = $dataStart;
        foreach ($rows as $i => $row) {
            $sheet->setCellValueExplicit("A{$r}", (string) ($i + 1), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("B{$r}", $row['jenis_kayu']);
            $sheet->setCellValue("C{$r}", MutasiReportService::fmtBatang($row['awal_batang']));
            $sheet->setCellValue("D{$r}", MutasiReportService::fmt($row['awal_volume']));
            $sheet->setCellValue("E{$r}", $row['jenis_kayu']);
            $sheet->setCellValue("F{$r}", MutasiReportService::fmtBatang($row['masuk_batang']));
            $sheet->setCellValue("G{$r}", MutasiReportService::fmt($row['masuk_volume']));
            $sheet->setCellValue("H{$r}", $row['jenis_kayu']);
            $sheet->setCellValue("I{$r}", MutasiReportService::fmtBatang($row['diolah_batang']));
            $sheet->setCellValue("J{$r}", MutasiReportService::fmt($row['diolah_volume']));
            $sheet->setCellValue("K{$r}", MutasiReportService::fmtBatang($row['lain_batang']));
            $sheet->setCellValue("L{$r}", MutasiReportService::fmt($row['lain_volume']));
            $sheet->setCellValue("M{$r}", $row['jenis_kayu']);
            $sheet->setCellValue("N{$r}", MutasiReportService::fmtBatang($row['akhir_batang']));
            $sheet->setCellValue("O{$r}", MutasiReportService::fmt($row['akhir_volume']));
            $sheet->setCellValue("P{$r}", $row['keterangan']);
            $r++;
        }

        // ============================ BARIS JUMLAH =============================
        $totalRow = $r;
        $sheet->setCellValue("A{$totalRow}", 'Jumlah :');
        $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
        $sheet->setCellValue("C{$totalRow}", MutasiReportService::fmtBatang($totals['awal_batang']));
        $sheet->setCellValue("D{$totalRow}", MutasiReportService::fmt($totals['awal_volume']));
        $sheet->setCellValue("E{$totalRow}", 'Jumlah :');
        $sheet->setCellValue("F{$totalRow}", MutasiReportService::fmtBatang($totals['masuk_batang']));
        $sheet->setCellValue("G{$totalRow}", MutasiReportService::fmt($totals['masuk_volume']));
        $sheet->setCellValue("H{$totalRow}", 'Jumlah :');
        $sheet->setCellValue("I{$totalRow}", MutasiReportService::fmtBatang($totals['diolah_batang']));
        $sheet->setCellValue("J{$totalRow}", MutasiReportService::fmt($totals['diolah_volume']));
        $sheet->setCellValue("K{$totalRow}", MutasiReportService::fmtBatang($totals['lain_batang']));
        $sheet->setCellValue("L{$totalRow}", MutasiReportService::fmt($totals['lain_volume']));
        $sheet->setCellValue("M{$totalRow}", 'Jumlah :');
        $sheet->setCellValue("N{$totalRow}", MutasiReportService::fmtBatang($totals['akhir_batang']));
        $sheet->setCellValue("O{$totalRow}", MutasiReportService::fmt($totals['akhir_volume']));

        // ============================ TANDA TANGAN =============================
        $ttdRow = $totalRow + 2;
        $tanggal = Carbon::now()->translatedFormat('d F Y');
        $sheet->setCellValue("M{$ttdRow}", "{$p['kota']}, {$tanggal}");
        $sheet->mergeCells("M{$ttdRow}:O{$ttdRow}");
        $sheet->setCellValue("M" . ($ttdRow + 1), $p['jabatan'] . ',');
        $sheet->mergeCells("M" . ($ttdRow + 1) . ":O" . ($ttdRow + 1));
        $sheet->setCellValue("M" . ($ttdRow + 5), $p['pimpinan']);
        $sheet->mergeCells("M" . ($ttdRow + 5) . ":O" . ($ttdRow + 5));
        $sheet->getStyle("M" . ($ttdRow + 5))->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("M{$ttdRow}:O" . ($ttdRow + 5))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ============================ STYLING ==================================
        // Border seluruh area tabel (header + data + jumlah)
        $tableRange = "A{$hStart}:P{$totalRow}";
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // Header abu-abu + bold
        $sheet->getStyle("A{$hStart}:P12")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE8E8E8'],
            ],
        ]);

        // Baris jumlah bold + abu-abu
        $sheet->getStyle("A{$totalRow}:P{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE8E8E8'],
            ],
        ]);

        // Kolom "Jenis Kayu" & "Jumlah :" rata kiri biar rapi
        foreach (['B', 'E', 'H', 'M'] as $col) {
            $sheet->getStyle("{$col}{$dataStart}:{$col}{$totalRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Kop rata kiri
        $sheet->getStyle('A1:H6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Lebar kolom
        $lebar = [
            'A' => 4,  'B' => 10, 'C' => 8, 'D' => 11, 'E' => 10, 'F' => 8,
            'G' => 11, 'H' => 10, 'I' => 8, 'J' => 11, 'K' => 8,  'L' => 11,
            'M' => 10, 'N' => 8,  'O' => 11, 'P' => 12,
        ];
        foreach ($lebar as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Setup halaman: Landscape, fit 1 halaman lebar
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.3)->setRight(0.3);

        return $spreadsheet;
    }
}
