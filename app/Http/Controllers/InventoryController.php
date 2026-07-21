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
        $daftarKayu     = Kayu::orderBy('jenis_kayu')->orderBy('ukuran')->get();
        $jenisKayuUnik  = Kayu::select('jenis_kayu')->distinct()->orderBy('jenis_kayu')->get();

        $ringkasanStok = $daftarKayu->groupBy('jenis_kayu')->map(function ($items) {
            return (object)[
                'jenis_kayu' => $items->first()->jenis_kayu,
                'stok' => $items->sum('stok')
            ];
        })->values();

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
            'peringatanStok', 'daftarKayu', 'jenisKayuUnik', 'ringkasanStok', 'aktivitasTerakhir',
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
            'stok'              => $kayu->stok,
        ]);
    }

    // ========================================================================
    // MASTER KAYU
    // ========================================================================
    public function storeKayu(Request $request)
    {
        $request->validate([
            'jenis_kayu' => 'required|string|max:100',
            'panjang'    => 'required|numeric|min:0.01',
            'diameter'   => 'required|numeric|min:0.1',
            'stok'       => 'nullable|integer|min:0',
        ]);

        $ukuran = Kayu::determineUkuran($request->panjang, $request->diameter);

        $existing = Kayu::where('jenis_kayu', $request->jenis_kayu)->where('ukuran', $ukuran)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Jenis kayu ' . $request->jenis_kayu . ' dengan ukuran ' . $ukuran . ' sudah ada di Data Master. Silakan gunakan yang sudah ada.');
        }

        $stokAwal = $request->stok ?? 0;
        $volume = Kayu::calculateVolume($request->panjang, $request->diameter, $stokAwal);

        Kayu::create([
            'jenis_kayu' => $request->jenis_kayu,
            'ukuran'     => $ukuran,
            'panjang'    => $request->panjang,
            'diameter'   => $request->diameter,
            'stok'       => $stokAwal,
            'volume'     => $volume,
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
            'jenis_kayu'     => 'required|array',
            'jenis_kayu.*'   => 'required|string',
            'jumlah'         => 'required|array',
            'jumlah.*'       => 'required|integer|min:1',
            'panjang'        => 'required|array',
            'panjang.*'      => 'required|numeric|min:0.01',
            'diameter'       => 'required|array',
            'diameter.*'     => 'required|numeric|min:0.1',
            'asal_supplier'  => 'nullable|string|max:255',
            'waktu_masuk'    => 'required|date',
            'kode_transaksi' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->jenis_kayu as $index => $jenis) {
                $panjang = $request->panjang[$index];
                $diameter = $request->diameter[$index];
                $jumlah = $request->jumlah[$index];

                $ukuran = Kayu::determineUkuran($panjang, $diameter);
                $volume = Kayu::calculateVolume($panjang, $diameter, $jumlah);

                $kayu = Kayu::firstOrCreate(
                    ['jenis_kayu' => $jenis, 'ukuran' => $ukuran],
                    ['panjang' => $panjang, 'diameter' => $diameter, 'stok' => 0, 'volume' => 0]
                );

                BarangMasuk::create([
                    'kayu_id'        => $kayu->id,
                    'jumlah'         => $jumlah,
                    'panjang'        => $panjang,
                    'diameter'       => $diameter,
                    'asal_supplier'  => $request->asal_supplier,
                    'waktu_masuk'    => $request->waktu_masuk,
                    'kode_transaksi' => $request->kode_transaksi,
                    'ukuran'         => $ukuran,
                    'volume'         => $volume,
                ]);

                $kayu->increment('stok', $jumlah);
                $kayu->increment('volume', $volume);
            }
        });

        return redirect(route('dashboard', ['tab' => 'masuk']))->with('success', 'Data barang masuk berhasil dicatat!');
    }

    /**
     * Tampilkan form edit untuk 1 transaksi masuk.
     */
    public function editBarangMasuk($id)
    {
        $masuk      = BarangMasuk::with('kayu')->findOrFail($id);
        $jenisKayuUnik = Kayu::select('jenis_kayu')->distinct()->orderBy('jenis_kayu')->get();

        return view('transaksi.edit_masuk', compact('masuk', 'jenisKayuUnik'));
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
            'jenis_kayu'     => 'required|string',
            'jumlah'         => 'required|integer|min:1',
            'panjang'        => 'required|numeric|min:0.01',
            'diameter'       => 'required|numeric|min:0.1',
            'asal_supplier'  => 'nullable|string|max:255',
            'waktu_masuk'    => 'required|date',
            'kode_transaksi' => 'required|string',
        ]);

        $oldKayu   = $masuk->kayu;
        $oldJumlah = $masuk->jumlah;
        $oldVolume = $masuk->volume;

        $newUkuran = Kayu::determineUkuran($request->panjang, $request->diameter);
        $newVolume = Kayu::calculateVolume($request->panjang, $request->diameter, $request->jumlah);

        // Jika merubah jenis kayu atau ukuran (kategori), pastikan kayu lama tidak minus jika dibatalkan
        // Sebenarnya ini cukup kompleks jika kita batalkan lalu tambah baru.
        // Kita cek saja stok lama cukup untuk dikurangi:
        if ($oldKayu->stok - $oldJumlah < 0) {
            return redirect()->back()
                ->with('error', 'Tidak bisa mengubah: stok kayu lama akan menjadi minus karena sudah terpakai.');
        }

        DB::transaction(function () use ($request, $masuk, $oldKayu, $oldJumlah, $oldVolume, $newUkuran, $newVolume) {
            // 1. Kurangi stok dan volume dari kayu lama
            $oldKayu->decrement('stok', $oldJumlah);
            $oldKayu->decrement('volume', $oldVolume);

            // 2. Cari atau buat kayu baru berdasarkan jenis kayu dan ukuran yang baru
            $newKayu = Kayu::firstOrCreate(
                ['jenis_kayu' => $request->jenis_kayu, 'ukuran' => $newUkuran],
                ['panjang' => $request->panjang, 'diameter' => $request->diameter, 'stok' => 0, 'volume' => 0]
            );

            // 3. Tambahkan stok dan volume ke kayu baru
            $newKayu->increment('stok', $request->jumlah);
            $newKayu->increment('volume', $newVolume);

            // 4. Update data barang masuk
            $masuk->update(array_merge(
                $request->only(['jumlah', 'panjang', 'diameter', 'asal_supplier', 'waktu_masuk', 'kode_transaksi']),
                [
                    'kayu_id' => $newKayu->id,
                    'ukuran'  => $newUkuran,
                    'volume'  => $newVolume,
                ]
            ));
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
            $kayu->decrement('stok', $masuk->jumlah);
            $kayu->decrement('volume', $masuk->volume);
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
            'jenis_kayu'       => 'required|array',
            'jenis_kayu.*'     => 'required|string',
            'ukuran'           => 'required|array',
            'ukuran.*'         => 'required|in:OP,OD,OGD',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'required|integer|min:1',
            'volume'           => 'required|array',
            'volume.*'         => 'required|numeric|min:0.0001',
            'jenis_penggunaan' => 'required|in:diolah_sendiri,penggunaan_lain',
            'customer'         => 'nullable|string|max:255',
            'waktu_keluar'     => 'required|date',
            'kode_transaksi'   => 'required|string',
        ]);

        // Validasi stok sebelum melakukan transaksi
        foreach ($request->jenis_kayu as $index => $jenis) {
            $ukuran = $request->ukuran[$index];
            $jumlah = $request->jumlah[$index];
            
            $kayu = Kayu::where('jenis_kayu', $jenis)->where('ukuran', $ukuran)->first();

            if (!$kayu) {
                return redirect()->back()->with('error', 'Persediaan ' . $jenis . ' ukuran ' . $ukuran . ' tidak ditemukan.');
            }

            if ($kayu->stok < $jumlah) {
                return redirect()->back()->with('error', 'Stok batang ' . $jenis . ' ' . $ukuran . ' tidak mencukupi (sisa: ' . $kayu->stok . ').');
            }
        }

        DB::transaction(function () use ($request) {
            foreach ($request->jenis_kayu as $index => $jenis) {
                $ukuran = $request->ukuran[$index];
                $jumlah = $request->jumlah[$index];
                $volume = $request->volume[$index];

                $kayu = Kayu::where('jenis_kayu', $jenis)->where('ukuran', $ukuran)->first();

                BarangKeluar::create([
                    'kayu_id'          => $kayu->id,
                    'jumlah'           => $jumlah,
                    'volume'           => $volume,
                    'jenis_penggunaan' => $request->jenis_penggunaan,
                    'customer'         => $request->customer,
                    'waktu_keluar'     => $request->waktu_keluar,
                    'kode_transaksi'   => $request->kode_transaksi,
                    'ukuran'           => $ukuran,
                ]);

                $kayu->decrement('stok', $jumlah);
                $kayu->decrement('volume', $volume);
            }
        });

        return redirect(route('dashboard', ['tab' => 'keluar']))->with('success', 'Data barang keluar berhasil dicatat!');
    }

    public function editBarangKeluar($id)
    {
        $keluar     = BarangKeluar::with('kayu')->findOrFail($id);
        $jenisKayuUnik = Kayu::select('jenis_kayu')->distinct()->orderBy('jenis_kayu')->get();

        return view('transaksi.edit_keluar', compact('keluar', 'jenisKayuUnik'));
    }

    public function updateBarangKeluar(Request $request, $id)
    {
        $keluar = BarangKeluar::findOrFail($id);

        $request->validate([
            'jenis_kayu'       => 'required|string',
            'ukuran'           => 'required|in:OP,OD,OGD',
            'jumlah'           => 'required|integer|min:1',
            'volume'           => 'required|numeric|min:0.0001',
            'jenis_penggunaan' => 'required|in:diolah_sendiri,penggunaan_lain',
            'customer'         => 'nullable|string|max:255',
            'waktu_keluar'     => 'required|date',
            'kode_transaksi'   => 'required|string',
        ]);

        $oldKayu   = $keluar->kayu;
        $oldJumlah = $keluar->jumlah;
        $oldVolume = $keluar->volume;

        $newKayu = Kayu::where('jenis_kayu', $request->jenis_kayu)->where('ukuran', $request->ukuran)->first();
        
        if (!$newKayu) {
            return redirect()->back()->with('error', 'Persediaan kayu tidak ditemukan.');
        }

        // Kalau kayu barunya sama dengan kayu lama, kita harus hitung sisa stok sementara
        $tempStok = ($newKayu->id === $oldKayu->id) ? $oldKayu->stok + $oldJumlah : $newKayu->stok;
        if ($tempStok < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok batang tidak mencukupi.');
        }

        DB::transaction(function () use ($request, $keluar, $oldKayu, $oldJumlah, $oldVolume, $newKayu) {
            // 1. Kembalikan stok lama
            $oldKayu->increment('stok', $oldJumlah);
            $oldKayu->increment('volume', $oldVolume);

            // Refresh newKayu if it's the same instance to get updated stok
            if ($newKayu->id === $oldKayu->id) {
                $newKayu->refresh();
            }

            // 3. Potong stok baru
            $newKayu->decrement('stok', $request->jumlah);
            $newKayu->decrement('volume', $request->volume);

            // 4. Update riwayat keluar
            $keluar->update(array_merge(
                $request->only(['jumlah', 'volume', 'jenis_penggunaan', 'customer', 'waktu_keluar', 'kode_transaksi']),
                ['kayu_id' => $newKayu->id, 'ukuran' => $request->ukuran]
            ));
        });

        return redirect()->route('dashboard')->with('success', 'Transaksi barang keluar berhasil diperbarui.');
    }

    public function destroyBarangKeluar($id)
    {
        $keluar = BarangKeluar::findOrFail($id);
        $kayu   = $keluar->kayu;

        DB::transaction(function () use ($keluar, $kayu) {
            $kayu->increment('stok', $keluar->jumlah);
            $kayu->increment('volume', $keluar->volume);
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
