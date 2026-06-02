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
        // 1. Hitung Total Stok dari Semua Kayu
        $totalStok = Kayu::sum('stok');

        // 2. Hitung Total Barang Masuk Bulan Ini
        $barangMasukBulanIni = BarangMasuk::whereMonth('waktu_masuk', Carbon::now()->month)
            ->whereYear('waktu_masuk', Carbon::now()->year)
            ->sum('jumlah');

        // 3. Hitung Total Barang Keluar Bulan Ini
        $barangKeluarBulanIni = BarangKeluar::whereMonth('waktu_keluar', Carbon::now()->month)
            ->whereYear('waktu_keluar', Carbon::now()->year)
            ->sum('jumlah');

        // 4. Hitung Peringatan Stok (Contoh: Kayu yang stoknya di bawah 10 lembar/batang)
        $peringatanStok = Kayu::where('stok', '<', 10)->count();

        // 5. Ambil Semua Data Master Kayu untuk Tabel Real-time
        $daftarKayu = Kayu::all();

        // 6. Ambil Aktivitas Terakhir (Gabungan 5 data masuk dan keluar terbaru)
        $logMasuk = BarangMasuk::with('kayu')->latest()->take(5)->get()->map(function ($item) {
            $item->tipe = 'masuk';
            $item->waktu = $item->waktu_masuk;
            return $item;
        });

        $logKeluar = BarangKeluar::with('kayu')->latest()->take(5)->get()->map(function ($item) {
            $item->tipe = 'keluar';
            $item->waktu = $item->waktu_keluar;
            return $item;
        });

        // Gabungkan dan urutkan berdasarkan waktu terbaru
        $aktivitasTerakhir = $logMasuk->concat($logKeluar)->sortByDesc('waktu')->take(5);

        return view('dashboard', compact(
            'totalStok',
            'barangMasukBulanIni',
            'barangKeluarBulanIni',
            'peringatanStok',
            'daftarKayu',
            'aktivitasTerakhir'
        ));
    }

    /**
     * Proses Input Barang Masuk (Stok Bertambah)
     */
    public function storeBarangMasuk(Request $request)
    {
        // Validasi Input untuk Keamanan Data
        $request->validate([
            'kayu_id'        => 'required|exists:kayus,id',
            'jumlah'         => 'required|integer|min:1',
            'asal_supplier'  => 'nullable|string|max:255',
            'waktu_masuk'    => 'required|date',
            'kode_transaksi' => 'required|string|unique:barang_masuks,kode_transaksi',
        ]);

        // Menggunakan DB Transaction untuk memastikan kedua proses sukses bersamaan
        DB::beginTransaction();
        try {
            // 1. Simpan data ke tabel barang_masuks
            BarangMasuk::create([
                'kayu_id'        => $request->kayu_id,
                'jumlah'         => $request->jumlah,
                'asal_supplier'  => $request->asal_supplier,
                'waktu_masuk'    => $request->waktu_masuk,
                'kode_transaksi' => $request->kode_transaksi,
            ]);

            // 2. Tambahkan stok kayu yang bersangkutan
            $kayu = Kayu::findOrFail($request->kayu_id);
            $kayu->increment('stok', $request->jumlah);

            DB::commit(); // Simpan perubahan permanen jika semua lancar
            return redirect()->back()->with('success', 'Data barang masuk berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua proses jika ada error agar data tidak inkonsisten
            return redirect()->back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Proses Input Barang Keluar (Stok Berkurang)
     */
    public function storeBarangKeluar(Request $request)
    {
        // Validasi Input
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

            // Validasi krusial: Cek apakah stok di gudang mencukupi
            if ($kayu->stok < $request->jumlah) {
                return redirect()->back()->with('error', 'Transaksi ditolak! Stok kayu ' . $kayu->jenis_kayu . ' tidak mencukupi.');
            }

            // 1. Simpan data ke tabel barang_keluars
            BarangKeluar::create([
                'kayu_id'        => $request->kayu_id,
                'jumlah'         => $request->jumlah,
                'customer'       => $request->customer,
                'waktu_keluar'   => $request->waktu_keluar,
                'kode_transaksi' => $request->kode_transaksi,
            ]);

            // 2. Kurangi stok kayu tersebut
            $kayu->decrement('stok', $request->jumlah);

            DB::commit();
            return redirect()->back()->with('success', 'Data barang keluar berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }
}
