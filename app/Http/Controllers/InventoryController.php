<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kayu;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class InventoryController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard beserta Ringkasan Data
     */
    public function dashboard()
    {
        $totalStok = Kayu::sum('stok');

        $barangMasukBulanIni = BarangMasuk::whereMonth('waktu_masuk', Carbon::now()->month)
            ->whereYear('waktu_masuk', Carbon::now()->year)
            ->sum('jumlah');

        $barangKeluarBulanIni = BarangKeluar::whereMonth('waktu_keluar', Carbon::now()->month)
            ->whereYear('waktu_keluar', Carbon::now()->year)
            ->sum('jumlah');

        $peringatanStok = Kayu::where('stok', '<', 10)->count();

        $daftarKayu = Kayu::all();

        // Ambil semua data Masuk dan seragamkan strukturnya
        $semuaMasuk = BarangMasuk::with('kayu')->get()->map(function ($item) {
            $item->tipe = 'masuk';
            $item->waktu = $item->waktu_masuk;
            $item->pihak_terkait = $item->asal_supplier;
            return $item;
        });

        // Ambil semua data Keluar dan seragamkan strukturnya
        $semuaKeluar = BarangKeluar::with('kayu')->get()->map(function ($item) {
            $item->tipe = 'keluar';
            $item->waktu = $item->waktu_keluar;
            $item->pihak_terkait = $item->customer;
            return $item;
        });

        // Gabungkan kedua data, lalu urutkan dari yang terbaru
        $laporanTransaksi = $semuaMasuk->concat($semuaKeluar)->sortByDesc('waktu');
        
        // Untuk aktivitas terakhir di dashboard depan, ambil 5 saja dari data gabungan tadi
        $aktivitasTerakhir = $laporanTransaksi->take(5);

        return view('dashboard', compact(
            'totalStok',
            'barangMasukBulanIni',
            'barangKeluarBulanIni',
            'peringatanStok',
            'daftarKayu',
            'aktivitasTerakhir',
            'laporanTransaksi' // Variabel baru dikirim ke Blade
        ));
    }

    /**
     * Endpoint API untuk mendapatkan data kayu dengan volume per batang (JSON)
     */
    public function getKayuData($id)
    {
        $kayu = Kayu::find($id);
        
        if (!$kayu) {
            return response()->json(['error' => 'Kayu tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $kayu->id,
            'jenis_kayu' => $kayu->jenis_kayu,
            'dimensi' => $kayu->dimensi,
            'ukuran' => $kayu->ukuran,
            'stok' => $kayu->stok,
            'volume_per_batang' => $kayu->volume_per_batang
        ]);
    }

    /**
     * Proses Input Barang Masuk (Stok Bertambah)
     */
    public function storeBarangMasuk(Request $request)
    {
        $request->validate([
            'kayu_id'        => 'required|exists:kayus,id',
            'jumlah'         => 'required|integer|min:1',
            'panjang'        => 'required|numeric|min:0.01',
            'diameter'       => 'required|numeric|min:0.1',
            'asal_supplier'  => 'nullable|string|max:255',
            'waktu_masuk'    => 'required|date',
            'kode_transaksi' => 'required|string|unique:barang_masuks,kode_transaksi',
        ]);

        DB::beginTransaction();
        try {
            BarangMasuk::create([
                'kayu_id'        => $request->kayu_id,
                'jumlah'         => $request->jumlah,
                'panjang'        => $request->panjang,
                'diameter'       => $request->diameter,
                'asal_supplier'  => $request->asal_supplier,
                'waktu_masuk'    => $request->waktu_masuk,
                'kode_transaksi' => $request->kode_transaksi,
            ]);

            $kayu = Kayu::findOrFail($request->kayu_id);
            $kayu->increment('stok', $request->jumlah);

            DB::commit();
            return redirect()->back()->with('success', 'Data barang masuk berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Proses Input Barang Keluar (Stok Berkurang)
     */
    public function storeBarangKeluar(Request $request)
    {
        $request->validate([
            'kayu_id'        => 'required|exists:kayus,id',
            'jumlah'         => 'required|integer|min:1',
            'panjang'        => 'required|numeric|min:0.01',
            'diameter'       => 'required|numeric|min:0.1',
            'customer'       => 'nullable|string|max:255',
            'waktu_keluar'   => 'required|date',
            'kode_transaksi' => 'required|string|unique:barang_keluars,kode_transaksi',
        ]);

        DB::beginTransaction();
        try {
            $kayu = Kayu::findOrFail($request->kayu_id);

            if ($kayu->stok < $request->jumlah) {
                return redirect()->back()->with('error', 'Transaksi ditolak! Stok kayu ' . $kayu->jenis_kayu . ' tidak mencukupi.');
            }

            BarangKeluar::create([
                'kayu_id'        => $request->kayu_id,
                'jumlah'         => $request->jumlah,
                'panjang'        => $request->panjang,
                'diameter'       => $request->diameter,
                'customer'       => $request->customer,
                'waktu_keluar'   => $request->waktu_keluar,
                'kode_transaksi' => $request->kode_transaksi,
            ]);

            $kayu->decrement('stok', $request->jumlah);

            DB::commit();
            return redirect()->back()->with('success', 'Data barang keluar berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }


    /**
     * Export Laporan Mutasi Kayu ke Excel (Format dengan border/outline yang proper)
     */
    public function exportExcel()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $bulanTahun = Carbon::now()->format('Y_m');

        // Ambil data (Logika sama dengan PDF)
        $dataMutasi = Kayu::all()->map(function ($kayu) use ($bulanIni, $tahunIni) {
            $masuk = $kayu->barangMasuks()->whereMonth('waktu_masuk', $bulanIni)->whereYear('waktu_masuk', $tahunIni)->sum('jumlah');
            $keluar = $kayu->barangKeluars()->whereMonth('waktu_keluar', $bulanIni)->whereYear('waktu_keluar', $tahunIni)->sum('jumlah');
            $persediaanBulanIni = $kayu->stok;
            $persediaanBulanLalu = ($persediaanBulanIni + $keluar) - $masuk;
            $volumePerBatang = $kayu->volume_per_batang;

            return [
                'jenis_kayu'             => $kayu->jenis_kayu,
                'awal_batang'            => $persediaanBulanLalu,
                'volume_awal'            => $this->formatVolume($persediaanBulanLalu * $volumePerBatang),
                'masuk_batang'           => $masuk,
                'volume_masuk'           => $this->formatVolume($masuk * $volumePerBatang),
                'keluar_batang'          => $keluar,
                'volume_keluar'          => $this->formatVolume($keluar * $volumePerBatang),
                'jumlah_olah_sendiri'    => $keluar,
                'volume_olah_sendiri'    => $this->formatVolume($keluar * $volumePerBatang),
                'jumlah_penggunaan_lain' => 0,
                'volume_penggunaan_lain' => '0',
                'akhir_batang'           => $persediaanBulanIni,
                'volume_akhir'           => $this->formatVolume($persediaanBulanIni * $volumePerBatang),
                'keterangan'             => '',
            ];
        });

        // Buat HTML dengan border style yang proper untuk Excel
        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; }
        body { 
            font-family: Calibri, Arial, sans-serif; 
            font-size: 11px; 
            padding: 20px;
        }
        
        /* Info perusahaan */
        .header-info {
            margin-bottom: 20px;
            font-size: 10px;
        }
        .header-info p { margin: 2px 0; }
        .judul-laporan {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0;
        }
        
        /* Table dengan border yang proper */
        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #000;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            height: 20px;
        }
        
        th {
            background-color: #e8e8e8;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .text-left { text-align: left !important; }
        .bold { font-weight: bold; }
        .section-title { 
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 11px;
        }
        
        /* Signature area */
        .signature-area {
            margin-top: 40px;
            font-size: 10px;
        }
        .signature-right {
            float: right;
            text-align: center;
            width: 200px;
        }
        .signature-right p { margin: 2px 0; }
        .clear { clear: both; }
    </style>
</head>
<body>

<div class="header-info">
    <p><strong>Nama Perusahaan</strong> : UD. SEKAWAN JAYA</p>
    <p><strong>Alamat</strong> : Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Bantul</p>
    <p><strong>Jenis Industri</strong> : Kayu Jati &amp; Sengon</p>
    <p><strong>Lokasi Industri</strong> : Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Pg. Harjo, Bantul</p>
    <p><strong>Dinas Kehutanan</strong> : -</p>
    <p><strong>Propinsi</strong> : D.I Yogyakarta</p>
</div>

<div class="judul-laporan">LAPORAN MUTASI HASIL HUTAN</div>

<div class="section-title">A1. KAYU BULAT</div>

<table>
    <thead>
        <tr>
            <th rowspan="3" style="width: 3%;">No</th>
            <th colspan="3" style="width: 18%;">Persediaan Akhir Bulan Lalu</th>
            <th colspan="3" style="width: 18%;">Perolehan Kayu Bulat</th>
            <th colspan="3" style="width: 18%;">Diolah Sendiri</th>
            <th colspan="2" style="width: 12%;">Penggunaan Lain</th>
            <th colspan="3" style="width: 18%;">Persediaan Bulan Ini</th>
            <th rowspan="3" style="width: 8%;">Keterangan</th>
        </tr>
        <tr>
            <th style="width: 6%;">Jenis Kayu</th>
            <th style="width: 6%;">Jumlah Batang</th>
            <th style="width: 6%;">Volume M3/Ton</th>
            <th style="width: 6%;">Jenis Kayu</th>
            <th style="width: 6%;">Jumlah Batang</th>
            <th style="width: 6%;">Volume M3/Ton</th>
            <th style="width: 6%;">Jenis Kayu</th>
            <th style="width: 6%;">Jumlah Batang</th>
            <th style="width: 6%;">Volume M3/Ton</th>
            <th style="width: 6%;">Jumlah Batang</th>
            <th style="width: 6%;">Volume M3/Ton</th>
            <th style="width: 6%;">Jenis Kayu</th>
            <th style="width: 6%;">Jumlah Batang</th>
            <th style="width: 6%;">Volume M3/Ton</th>
        </tr>
        <tr>
            <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td>
        </tr>
    </thead>
    <tbody>';

        $totAwal = 0;
        $totMasuk = 0;
        $totOlahSendiri = 0;
        $totPenggunaanLain = 0;
        $totAkhir = 0;

        foreach ($dataMutasi as $index => $row) {
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td class="text-left">' . $row['jenis_kayu'] . '</td>
                <td>' . $row['awal_batang'] . '</td>
                <td>' . $row['volume_awal'] . '</td>
                <td class="text-left">' . $row['jenis_kayu'] . '</td>
                <td>' . $row['masuk_batang'] . '</td>
                <td>' . $row['volume_masuk'] . '</td>
                <td class="text-left">' . $row['jenis_kayu'] . '</td>
                <td>' . $row['jumlah_olah_sendiri'] . '</td>
                <td>' . $row['volume_olah_sendiri'] . '</td>
                <td>' . $row['jumlah_penggunaan_lain'] . '</td>
                <td>' . $row['volume_penggunaan_lain'] . '</td>
                <td class="text-left">' . $row['jenis_kayu'] . '</td>
                <td>' . $row['akhir_batang'] . '</td>
                <td>' . $row['volume_akhir'] . '</td>
                <td>' . $row['keterangan'] . '</td>
            </tr>';

            $totAwal += $row['awal_batang'];
            $totMasuk += $row['masuk_batang'];
            $totOlahSendiri += $row['jumlah_olah_sendiri'];
            $totPenggunaanLain += $row['jumlah_penggunaan_lain'];
            $totAkhir += $row['akhir_batang'];
        }

        // Baris Total
        $html .= '<tr style="background-color: #e8e8e8;">
            <td colspan="2" class="bold text-left">Jumlah:</td>
            <td class="bold">' . $totAwal . '</td>
            <td class="bold">-</td>
            <td></td>
            <td class="bold">' . $totMasuk . '</td>
            <td class="bold">-</td>
            <td></td>
            <td class="bold">' . $totOlahSendiri . '</td>
            <td class="bold">-</td>
            <td class="bold">' . $totPenggunaanLain . '</td>
            <td class="bold">-</td>
            <td></td>
            <td class="bold">' . $totAkhir . '</td>
            <td class="bold">-</td>
            <td></td>
        </tr>';

        $html .= '    </tbody>
</table>

<div class="signature-area">
    <div class="signature-right">
        <p>Yogyakarta, ' . Carbon::now()->translatedFormat('d F Y') . '</p>
        <p>Pimpinan/Pemilik,</p>
        <br><br><br>
        <p class="bold" style="text-decoration: underline;">RUSDIYANTO</p>
    </div>
    <div class="clear"></div>
</div>

</body>
</html>';

        // Return sebagai file Excel dengan border yang proper
        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan_mutasi_kayu_' . $bulanTahun . '.xls"',
        ]);
    }

    /**
     * Alias method untuk kompatibilitas jika dipanggil nama lama
     */
    public function exportLaporanMutasi()
    {
        return $this->exportExcel();
    }

    /**
     * Export Laporan Mutasi Kayu ke PDF (Tampilkan sebagai HTML yang bisa dicetak)
     */
    public function exportPdf()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // Ambil data (Logika sama persis dengan Excel)
        $dataMutasi = Kayu::all()->map(function ($kayu) use ($bulanIni, $tahunIni) {
            $masuk = $kayu->barangMasuks()->whereMonth('waktu_masuk', $bulanIni)->whereYear('waktu_masuk', $tahunIni)->sum('jumlah');
            $keluar = $kayu->barangKeluars()->whereMonth('waktu_keluar', $bulanIni)->whereYear('waktu_keluar', $tahunIni)->sum('jumlah');
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

        // Render view sebagai HTML yang dapat dicetak ke PDF
        return view('exports.laporan_mutasi_pdf', [
            'data'    => $dataMutasi,
            'tanggal' => Carbon::now()->translatedFormat('d F Y')
        ]);
    }

    private function formatVolume(float $volume): string
    {
        return $volume > 0 ? number_format($volume, 3, ',', '.') : '-';
    }
}
