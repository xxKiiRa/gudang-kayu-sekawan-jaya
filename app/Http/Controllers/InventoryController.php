<?php

namespace App\Http\Controllers;

use App\Exports\LaporanMutasiExport;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kayu;
use App\Services\MutasiReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct(
        private MutasiReportService $mutasi = new MutasiReportService()
    ) {}

    /**
     * Halaman Dashboard + ringkasan data.
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
        $daftarKayu     = Kayu::orderBy('jenis_kayu')->get();

        // Gabungan riwayat masuk + keluar (struktur diseragamkan)
        $semuaMasuk = BarangMasuk::with('kayu')->get()->map(function ($item) {
            $item->tipe          = 'masuk';
            $item->waktu         = $item->waktu_masuk;
            $item->pihak_terkait = $item->asal_supplier;
            return $item;
        });

        $semuaKeluar = BarangKeluar::with('kayu')->get()->map(function ($item) {
            $item->tipe          = 'keluar';
            $item->waktu         = $item->waktu_keluar;
            $item->pihak_terkait = $item->customer;
            return $item;
        });

        $laporanTransaksi  = $semuaMasuk->concat($semuaKeluar)->sortByDesc('waktu')->values();
        $aktivitasTerakhir = $laporanTransaksi->take(5);

        // Daftar periode untuk dropdown export (tahun berjalan mundur 5 tahun)
        $tahunSekarang = (int) Carbon::now()->year;
        $daftarTahun   = range($tahunSekarang, $tahunSekarang - 5);
        $daftarBulan   = collect(range(1, 12))->mapWithKeys(fn ($m) => [
            $m => Carbon::create()->month($m)->translatedFormat('F'),
        ]);

        return view('dashboard', compact(
            'totalStok',
            'barangMasukBulanIni',
            'barangKeluarBulanIni',
            'peringatanStok',
            'daftarKayu',
            'aktivitasTerakhir',
            'laporanTransaksi',
            'daftarTahun',
            'daftarBulan',
            'tahunSekarang'
        ));
    }

    /**
     * API data kayu (JSON) — dipakai bila front-end butuh detail 1 jenis kayu.
     */
    public function getKayuData($id)
    {
        $kayu = Kayu::find($id);

        if (! $kayu) {
            return response()->json(['error' => 'Kayu tidak ditemukan'], 404);
        }

        return response()->json([
            'id'                => $kayu->id,
            'jenis_kayu'        => $kayu->jenis_kayu,
            'dimensi'           => $kayu->dimensi,
            'panjang'           => $kayu->panjang,
            'stok'              => $kayu->stok,
            'volume_per_batang' => round($kayu->volume_per_batang, 4),
        ]);
    }

    /**
     * Tambah master jenis kayu baru.
     */
    public function storeKayu(Request $request)
    {
        $request->validate([
            'jenis_kayu' => 'required|string|max:100',
            'dimensi'    => 'nullable|string|max:100',
            'panjang'    => 'nullable|string|max:100',
            'stok'       => 'nullable|integer|min:0',
        ]);

        Kayu::create([
            'jenis_kayu' => $request->jenis_kayu,
            'dimensi'    => $request->dimensi,
            'panjang'    => $request->panjang,
            'stok'       => $request->stok ?? 0,
        ]);

        return redirect()->back()->with('success', 'Jenis kayu baru berhasil ditambahkan!');
    }

    /**
     * Hapus master jenis kayu (beserta riwayat via cascade).
     */
    public function destroyKayu($id)
    {
        $kayu = Kayu::findOrFail($id);
        $kayu->delete();

        return redirect()->back()->with('success', 'Jenis kayu berhasil dihapus.');
    }

    /**
     * Input Barang Masuk (stok bertambah).
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

        DB::transaction(function () use ($request) {
            BarangMasuk::create($request->only([
                'kayu_id', 'jumlah', 'panjang', 'diameter',
                'asal_supplier', 'waktu_masuk', 'kode_transaksi',
            ]));

            Kayu::whereKey($request->kayu_id)->increment('stok', $request->jumlah);
        });

        return redirect()->back()->with('success', 'Data barang masuk berhasil dicatat!');
    }

    /**
     * Input Barang Keluar (stok berkurang).
     */
    public function storeBarangKeluar(Request $request)
    {
        $request->validate([
            'kayu_id'          => 'required|exists:kayus,id',
            'jumlah'           => 'required|integer|min:1',
            'panjang'          => 'required|numeric|min:0.01',
            'diameter'         => 'required|numeric|min:0.1',
            'jenis_penggunaan' => 'required|in:diolah_sendiri,penggunaan_lain',
            'customer'         => 'nullable|string|max:255',
            'waktu_keluar'     => 'required|date',
            'kode_transaksi'   => 'required|string|unique:barang_keluars,kode_transaksi',
        ]);

        $kayu = Kayu::findOrFail($request->kayu_id);

        if ($kayu->stok < $request->jumlah) {
            return redirect()->back()->with(
                'error',
                'Transaksi ditolak! Stok kayu ' . $kayu->jenis_kayu . ' tidak mencukupi (sisa: ' . $kayu->stok . ').'
            );
        }

        DB::transaction(function () use ($request, $kayu) {
            BarangKeluar::create($request->only([
                'kayu_id', 'jumlah', 'panjang', 'diameter', 'jenis_penggunaan',
                'customer', 'waktu_keluar', 'kode_transaksi',
            ]));

            $kayu->decrement('stok', $request->jumlah);
        });

        return redirect()->back()->with('success', 'Data barang keluar berhasil dicatat!');
    }

    /**
     * Export Laporan Mutasi ke Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        [$bulan, $tahun] = $this->resolvePeriode($request);

        $report   = $this->mutasi->build($bulan, $tahun);
        $filename = 'laporan_mutasi_kayu_' . $tahun . '_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx';

        return (new LaporanMutasiExport($report))->download($filename);
    }

    /**
     * Export Laporan Mutasi ke PDF (halaman siap cetak).
     */
    public function exportPdf(Request $request)
    {
        [$bulan, $tahun] = $this->resolvePeriode($request);

        $report = $this->mutasi->build($bulan, $tahun);

        return view('exports.laporan_mutasi_pdf', compact('report'));
    }

    /**
     * Ambil bulan & tahun dari request; default ke bulan berjalan.
     *
     * @return array{0:int,1:int}
     */
    private function resolvePeriode(Request $request): array
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $bulan = max(1, min(12, $bulan));

        return [$bulan, $tahun];
    }
}
