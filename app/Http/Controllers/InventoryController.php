<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kayu;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanMutasiExport;
use Barryvdh\DomPDF\Facade\Pdf;



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
            'asal_supplier'  => 'nullable|string|max:255',
            'waktu_masuk'    => 'required|date',
            'kode_transaksi' => 'required|string|unique:barang_masuks,kode_transaksi',
        ]);

        DB::beginTransaction();
        try {
            BarangMasuk::create([
                'kayu_id'        => $request->kayu_id,
                'jumlah'         => $request->jumlah,
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
     * Export Laporan Mutasi Kayu ke Excel
     */
    public function exportExcel()
    {
        return Excel::download(new LaporanMutasiExport, 'laporan_mutasi_kayu.xlsx');
    }

    /**
     * Alias method untuk kompatibilitas jika dipanggil nama lama
     */
    public function exportLaporanMutasi()
    {
        return $this->exportExcel();
    }

    /**
     * Export Laporan Mutasi Kayu ke PDF
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

        // Load view PDF dan set kertas menjadi A4 Mendatar (Landscape)
        $pdf = Pdf::loadView('exports.laporan_mutasi_pdf', [
            'data'    => $dataMutasi,
            'tanggal' => Carbon::now()->translatedFormat('d F Y')
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Mutasi_Hutan_' . Carbon::now()->format('Y_m') . '.pdf');
    }

    private function formatVolume(float $volume): string
    {
        return $volume > 0 ? number_format($volume, 3, ',', '.') : '-';
    }
}
