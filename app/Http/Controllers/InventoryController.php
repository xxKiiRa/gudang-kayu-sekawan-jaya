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

    // ========================================================================
    // DASHBOARD
    // ========================================================================
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

        $tahunSekarang = (int) Carbon::now()->year;
        $daftarTahun   = range($tahunSekarang, $tahunSekarang - 5);
        $daftarBulan   = collect(range(1, 12))->mapWithKeys(fn ($m) => [
            $m => Carbon::create()->month($m)->translatedFormat('F'),
        ]);

        return view('dashboard', compact(
            'totalStok', 'barangMasukBulanIni', 'barangKeluarBulanIni',
            'peringatanStok', 'daftarKayu', 'aktivitasTerakhir',
            'laporanTransaksi', 'daftarTahun', 'daftarBulan', 'tahunSekarang'
        ));
    }

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
            'kategori'          => $kayu->kategori,
            'stok'              => $kayu->stok,
            'volume_per_batang' => round($kayu->volume_per_batang, 4),
        ]);
    }

    // ========================================================================
    // MASTER KAYU
    // ========================================================================
    public function storeKayu(Request $request)
    {
        $request->validate([
            'jenis_kayu' => 'required|string|max:100',
            'dimensi'    => 'nullable|string|max:100',
            'kategori'   => 'nullable|string|max:100',
            'stok'       => 'nullable|integer|min:0',
        ]);

        Kayu::create([
            'jenis_kayu' => $request->jenis_kayu,
            'dimensi'    => $request->dimensi,
            'kategori'   => $request->kategori,
            'stok'       => $request->stok ?? 0,
        ]);

        return redirect()->back()->with('success', 'Jenis kayu baru berhasil ditambahkan!');
    }

    public function destroyKayu($id)
    {
        $kayu = Kayu::findOrFail($id);
        $kayu->delete();

        return redirect()->back()->with('success', 'Jenis kayu berhasil dihapus.');
    }

    // ========================================================================
    // BARANG MASUK — tambah / edit / hapus
    // ========================================================================
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
     * Tampilkan form edit untuk 1 transaksi masuk.
     */
    public function editBarangMasuk($id)
    {
        $masuk      = BarangMasuk::with('kayu')->findOrFail($id);
        $daftarKayu = Kayu::orderBy('jenis_kayu')->get();

        return view('transaksi.edit_masuk', compact('masuk', 'daftarKayu'));
    }

    /**
     * Simpan perubahan transaksi masuk + sesuaikan stok.
     *
     * LOGIKA STOK: jenis kayu tidak diubah (dikunci di form). Yang memengaruhi
     * stok hanya perubahan JUMLAH. selisih = jumlah_baru − jumlah_lama.
     *   - selisih positif  → stok bertambah
     *   - selisih negatif  → stok berkurang (dijaga tidak sampai minus)
     */
    public function updateBarangMasuk(Request $request, $id)
    {
        $masuk = BarangMasuk::findOrFail($id);

        $request->validate([
            'jumlah'         => 'required|integer|min:1',
            'panjang'        => 'required|numeric|min:0.01',
            'diameter'       => 'required|numeric|min:0.1',
            'asal_supplier'  => 'nullable|string|max:255',
            'waktu_masuk'    => 'required|date',
            // abaikan baris ini sendiri saat cek keunikan kode
            'kode_transaksi' => 'required|string|unique:barang_masuks,kode_transaksi,' . $masuk->id,
        ]);

        $kayu    = $masuk->kayu;
        $selisih = $request->jumlah - $masuk->jumlah;

        // Cegah stok minus (mis. jumlah dikurangi padahal kayu sudah terpakai)
        if ($kayu->stok + $selisih < 0) {
            return redirect()->back()
                ->with('error', 'Tidak bisa mengubah: stok akan menjadi minus karena sebagian kayu sudah terpakai.');
        }

        DB::transaction(function () use ($request, $masuk, $kayu, $selisih) {
            $masuk->update($request->only([
                'jumlah', 'panjang', 'diameter', 'asal_supplier', 'waktu_masuk', 'kode_transaksi',
            ]));

            $kayu->update(['stok' => $kayu->stok + $selisih]);
        });

        return redirect()->route('dashboard')->with('success', 'Transaksi barang masuk berhasil diperbarui.');
    }

    /**
     * Hapus transaksi masuk + kembalikan efek stoknya (stok berkurang).
     */
    public function destroyBarangMasuk($id)
    {
        $masuk = BarangMasuk::findOrFail($id);
        $kayu  = $masuk->kayu;

        if ($kayu->stok - $masuk->jumlah < 0) {
            return redirect()->back()
                ->with('error', 'Tidak bisa menghapus: stok akan menjadi minus karena kayu sudah terpakai.');
        }

        DB::transaction(function () use ($masuk, $kayu) {
            $kayu->update(['stok' => $kayu->stok - $masuk->jumlah]);
            $masuk->delete();
        });

        return redirect()->back()->with('success', 'Transaksi barang masuk berhasil dihapus.');
    }

    // ========================================================================
    // BARANG KELUAR — tambah / edit / hapus
    // ========================================================================
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
     * Tampilkan form edit untuk 1 transaksi keluar.
     */
    public function editBarangKeluar($id)
    {
        $keluar     = BarangKeluar::with('kayu')->findOrFail($id);
        $daftarKayu = Kayu::orderBy('jenis_kayu')->get();

        return view('transaksi.edit_keluar', compact('keluar', 'daftarKayu'));
    }

    /**
     * Simpan perubahan transaksi keluar + sesuaikan stok.
     *
     * LOGIKA STOK: jenis kayu dikunci. Karena keluar MENGURANGI stok, mengubah
     * jumlah berarti: stok_baru = stok + jumlah_lama − jumlah_baru.
     * Dijaga agar stok tidak minus (jumlah_baru tidak boleh melebihi stok+jumlah_lama).
     */
    public function updateBarangKeluar(Request $request, $id)
    {
        $keluar = BarangKeluar::findOrFail($id);

        $request->validate([
            'jumlah'           => 'required|integer|min:1',
            'panjang'          => 'required|numeric|min:0.01',
            'diameter'         => 'required|numeric|min:0.1',
            'jenis_penggunaan' => 'required|in:diolah_sendiri,penggunaan_lain',
            'customer'         => 'nullable|string|max:255',
            'waktu_keluar'     => 'required|date',
            'kode_transaksi'   => 'required|string|unique:barang_keluars,kode_transaksi,' . $keluar->id,
        ]);

        $kayu           = $keluar->kayu;
        $stokTersedia   = $kayu->stok + $keluar->jumlah; // stok kalau transaksi lama dibatalkan

        if ($request->jumlah > $stokTersedia) {
            return redirect()->back()
                ->with('error', 'Stok tidak mencukupi untuk jumlah baru (maksimal ' . $stokTersedia . ').');
        }

        DB::transaction(function () use ($request, $keluar, $kayu, $stokTersedia) {
            $keluar->update($request->only([
                'jumlah', 'panjang', 'diameter', 'jenis_penggunaan',
                'customer', 'waktu_keluar', 'kode_transaksi',
            ]));

            // stok akhir = (stok setelah membatalkan keluar lama) − jumlah baru
            $kayu->update(['stok' => $stokTersedia - $request->jumlah]);
        });

        return redirect()->route('dashboard')->with('success', 'Transaksi barang keluar berhasil diperbarui.');
    }

    /**
     * Hapus transaksi keluar + kembalikan stoknya (stok bertambah lagi).
     */
    public function destroyBarangKeluar($id)
    {
        $keluar = BarangKeluar::findOrFail($id);
        $kayu   = $keluar->kayu;

        DB::transaction(function () use ($keluar, $kayu) {
            $kayu->update(['stok' => $kayu->stok + $keluar->jumlah]);
            $keluar->delete();
        });

        return redirect()->back()->with('success', 'Transaksi barang keluar berhasil dihapus.');
    }

    // ========================================================================
    // EXPORT LAPORAN MUTASI
    // ========================================================================
    public function exportExcel(Request $request)
    {
        [$bulan, $tahun] = $this->resolvePeriode($request);

        $report   = $this->mutasi->build($bulan, $tahun);
        $filename = 'laporan_mutasi_kayu_' . $tahun . '_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx';

        return (new LaporanMutasiExport($report))->download($filename);
    }

    public function exportPdf(Request $request)
    {
        [$bulan, $tahun] = $this->resolvePeriode($request);

        $report = $this->mutasi->build($bulan, $tahun);

        return view('exports.laporan_mutasi_pdf', compact('report'));
    }

    private function resolvePeriode(Request $request): array
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $bulan = max(1, min(12, $bulan));

        return [$bulan, $tahun];
    }
}
