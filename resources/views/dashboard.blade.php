<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang Kayu UD SEKAWAN JAYA</title>

    @vite('resources/css/app.css')

    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="flex h-screen bg-gray-50 font-sans text-gray-800 overflow-hidden">

    <aside id="sidebar" class="w-64 transition-all duration-300 fixed md:relative z-20 h-full bg-amber-900 text-amber-50 flex flex-col shadow-xl -translate-x-full md:translate-x-0">
        <div class="p-4 flex items-center justify-between border-b border-amber-800/50">
            <div class="flex items-center gap-2 font-bold text-lg tracking-wide">
                <i data-lucide="database" class="text-amber-400"></i>
                <span>UD SEKAWAN JAYA</span>
            </div>
            <button class="md:hidden text-amber-200 hover:text-white" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-4">
            <p class="px-4 text-xs font-semibold text-amber-400/70 uppercase tracking-wider mb-2">Menu Utama</p>
            <nav class="space-y-1 px-2">
                <button id="nav-dashboard" data-title="Dashboard" onclick="switchTab('dashboard')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors bg-amber-800 text-white font-medium shadow-sm">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 text-amber-400 icon-active"></i>
                    <span>Dashboard</span>
                </button>
                <button id="nav-master" data-title="Data Master Kayu" onclick="switchTab('master')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="database" class="w-5 h-5 icon-active"></i>
                    <span>Data Master Kayu</span>
                </button>
                <button id="nav-masuk" data-title="Barang Masuk" onclick="switchTab('masuk')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="arrow-down-to-line" class="w-5 h-5 icon-active"></i>
                    <span>Barang Masuk</span>
                </button>
                <button id="nav-keluar" data-title="Barang Keluar" onclick="switchTab('keluar')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="arrow-up-from-line" class="w-5 h-5 icon-active"></i>
                    <span>Barang Keluar</span>
                </button>
                <button id="nav-laporan" data-title="Laporan Mutasi" onclick="switchTab('laporan')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="file-text" class="w-5 h-5 icon-active"></i>
                    <span>Laporan Mutasi</span>
                </button>
            </nav>
        </div>

        {{-- Footer sidebar: info admin yang login + tombol keluar --}}
        <div class="p-3 border-t border-amber-800/50">
            <div class="flex items-center gap-2 px-2 py-2 mb-1">
                <div class="w-9 h-9 rounded-full bg-amber-700 flex items-center justify-center font-bold text-white shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-white text-sm truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-amber-300/70">Administrator</p>
                </div>
            </div>
            {{-- Logout memakai form POST + @csrf (bukan link) demi keamanan --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-amber-100 hover:bg-amber-800 transition-colors text-sm">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <div id="overlay" class="fixed inset-0 bg-black/50 z-10 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative w-full">
        <header class="bg-white h-16 border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 shrink-0">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h1 id="header-title" class="text-xl font-semibold text-gray-800 capitalize">Dashboard</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    UD
                </div>
            </div>
        </header>

        {{-- ===================== NOTIFIKASI (satu blok saja) ===================== --}}
        <div id="notifications" class="w-full px-4 lg:px-8 mt-4 shrink-0">
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg shadow-sm mb-2 flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-2 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-2">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i data-lucide="x-circle" class="w-5 h-5"></i> Ada kesalahan input:
                    </div>
                    <ul class="list-disc pl-8 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <script>lucide.createIcons();</script>

        <div class="flex-1 overflow-auto p-4 md:p-8">

            {{-- ============================ DASHBOARD ============================ --}}
            <div id="tab-dashboard" class="tab-content space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-500 text-white"><i data-lucide="database" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Total Stok Kayu</p><h3 class="text-2xl font-bold text-gray-800">{{ $totalStok }}</h3><p class="text-xs text-gray-400 mt-0.5">Batang / Lembar</p></div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-emerald-500 text-white"><i data-lucide="arrow-down-to-line" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Barang Masuk</p><h3 class="text-2xl font-bold text-gray-800">{{ $barangMasukBulanIni }}</h3><p class="text-xs text-gray-400 mt-0.5">Bulan Ini</p></div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-amber-500 text-white"><i data-lucide="arrow-up-from-line" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Barang Keluar</p><h3 class="text-2xl font-bold text-gray-800">{{ $barangKeluarBulanIni }}</h3><p class="text-xs text-gray-400 mt-0.5">Bulan Ini</p></div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-red-500 text-white"><i data-lucide="bell" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Peringatan Stok</p><h3 class="text-2xl font-bold text-gray-800">{{ $peringatanStok }}</h3><p class="text-xs text-gray-400 mt-0.5">Item Menipis</p></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-800">Ringkasan Stok Real-time</h2></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">Jenis Kayu</th>
                                        <th class="px-6 py-3 font-medium">Dimensi</th>
                                        <th class="px-6 py-3 font-medium">Kategori</th>
                                        <th class="px-6 py-3 font-medium text-right">Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($daftarKayu as $kayu)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-medium text-gray-800">{{ $kayu->jenis_kayu }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $kayu->dimensi }}</td>
                                        <td class="px-6 py-3">
                                            <span class="px-2 py-1 rounded text-xs font-medium border bg-teal-50 text-teal-700 border-teal-200">
                                                {{ $kayu->kategori }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <span class="font-semibold {{ $kayu->stok < 10 ? 'text-red-600' : 'text-gray-800' }}">{{ $kayu->stok }}</span>
                                            <span class="text-gray-500 text-xs ml-1">Btg/Lbr</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-gray-500">Belum ada data kayu.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-800">Aktivitas Terakhir</h2></div>
                        <div class="p-4 flex-1 overflow-y-auto max-h-[350px] space-y-4">
                        @forelse($aktivitasTerakhir as $log)
                        <div class="flex gap-4 items-start pb-4 border-b border-gray-50">
                            @if($log->tipe == 'masuk')
                                <div class="mt-1 w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-emerald-100 text-emerald-600"><i data-lucide="arrow-down-to-line" class="w-4 h-4"></i></div>
                            @else
                                <div class="mt-1 w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-amber-100 text-amber-600"><i data-lucide="arrow-up-from-line" class="w-4 h-4"></i></div>
                            @endif

                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $log->kayu->jenis_kayu }} x {{ $log->jumlah }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $log->tipe == 'masuk' ? 'Supplier: ' . $log->asal_supplier : 'Customer: ' . $log->customer }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($log->waktu)->format('Y-m-d H:i') }} &bull; {{ $log->kode_transaksi }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center">Belum ada aktivitas.</p>
                        @endforelse
                    </div>
                    </div>
                </div>
            </div>

            {{-- ========================= DATA MASTER KAYU ========================= --}}
            <div id="tab-master" class="tab-content hidden space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Form tambah jenis kayu --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-blue-50 border-b border-blue-100 px-6 py-4">
                            <h2 class="font-semibold text-blue-800 flex items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Jenis Kayu</h2>
                        </div>
                        <form action="{{ route('kayu.store') }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nama Jenis Kayu <span class="text-red-500">*</span></label>
                                <input type="text" name="jenis_kayu" placeholder="Jati / Mahoni / Akasia" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Dimensi / Diameter (opsional)</label>
                                <input type="text" name="dimensi" placeholder="Ø 20 cm" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Kategori (opsional)</label>
                                <input type="text" name="kategori" placeholder="Besar / Sedang / Kecil" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Stok Awal</label>
                                <input type="number" name="stok" min="0" value="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Untuk saldo awal. Perubahan stok berikutnya lewat Barang Masuk/Keluar.</p>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">Simpan Jenis Kayu</button>
                        </form>
                    </div>

                    {{-- Daftar jenis kayu --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-800">Daftar Jenis Kayu</h2></div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">Jenis Kayu</th>
                                        <th class="px-6 py-3 font-medium">Dimensi</th>
                                        <th class="px-6 py-3 font-medium">Kategori</th>
                                        <th class="px-6 py-3 font-medium text-right">Stok</th>
                                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($daftarKayu as $kayu)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-medium text-gray-800">{{ $kayu->jenis_kayu }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $kayu->dimensi }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $kayu->kategori }}</td>
                                        <td class="px-6 py-3 text-right font-semibold">{{ $kayu->stok }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <form action="{{ route('kayu.destroy', $kayu->id) }}" method="POST" onsubmit="return confirm('Hapus jenis kayu {{ $kayu->jenis_kayu }} beserta seluruh riwayatnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-6 text-gray-500">Belum ada jenis kayu. Tambahkan di sebelah kiri.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================ BARANG MASUK ============================ --}}
            <div id="tab-masuk" class="tab-content hidden">
                <div class="max-w-4xl bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-100">
                        <h2 class="font-semibold text-emerald-800 text-lg flex items-center gap-2"><i data-lucide="arrow-down-to-line" class="w-5 h-5"></i> Form Penerimaan Kayu (Masuk)</h2>
                    </div>
                    <form action="{{ route('barang-masuk.store') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-800 border-b pb-2">Informasi Penerimaan</h3>
                                <div><label class="block text-sm text-emerald-700 font-semibold mb-1">No. Surat Jalan</label><input type="text" name="kode_transaksi" placeholder="SJ-IN-..." class="w-full border border-emerald-300 bg-emerald-50/30 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Tanggal Masuk</label><input type="datetime-local" name="waktu_masuk" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Nama Supplier</label><input type="text" name="asal_supplier" placeholder="Nama CV/PT" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                            </div>
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-800 border-b pb-2">Detail Kayu</h3>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Pilih Jenis Kayu</label>
                                    <select name="kayu_id" id="kayuSelectMasuk" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                                        <option value="" disabled selected>-- Pilih Jenis Kayu --</option>
                                        @foreach($daftarKayu as $kayu)
                                            <option value="{{ $kayu->id }}">{{ $kayu->jenis_kayu }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Panjang (m)</label>
                                        <input type="number" name="panjang" id="panjangMasuk" step="0.01" placeholder="0.00" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="calculateVolumeMasuk()" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Diameter (cm)</label>
                                        <input type="number" name="diameter" id="diameterMasuk" step="0.1" placeholder="0.0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="calculateVolumeMasuk()" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jumlah Masuk (Batang/Lembar)</label>
                                    <input type="number" name="jumlah" id="jumlahMasuk" step="1" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="calculateVolumeMasuk()" required>
                                </div>
                                <div id="volumeInfoMasuk" class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700 font-medium">Total Volume:</span>
                                        <span class="text-lg font-bold text-blue-700" id="totalVolumeMasukDisplay">0.0000 m³</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">Simpan Barang Masuk</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ============================ BARANG KELUAR ============================ --}}
            <div id="tab-keluar" class="tab-content hidden">
                <div class="max-w-4xl bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-amber-50 px-6 py-4 border-b border-amber-100">
                        <h2 class="font-semibold text-amber-800 text-lg flex items-center gap-2">
                            <i data-lucide="arrow-up-from-line" class="w-5 h-5"></i> Form Pengeluaran Kayu (Keluar)
                        </h2>
                    </div>

                    <form action="{{ route('barang-keluar.store') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-800 border-b pb-2">Informasi Pengeluaran</h3>

                                <div>
                                    <label class="block text-sm text-amber-700 font-semibold mb-1">No. Surat Jalan (Keluar)</label>
                                    <input type="text" name="kode_transaksi" placeholder="SJ-OUT-..." class="w-full border border-amber-300 bg-amber-50/30 rounded-lg px-3 py-2 text-sm outline-none focus:border-amber-500" required>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Tanggal & Waktu Keluar</label>
                                    <input type="datetime-local" name="waktu_keluar" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jenis Penggunaan</label>
                                    <select name="jenis_penggunaan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                                        <option value="diolah_sendiri" selected>Diolah Sendiri</option>
                                        <option value="penggunaan_lain">Penggunaan Lain (dijual gelondongan / dipindahtangankan)</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Menentukan kolom pada Laporan Mutasi Hasil Hutan.</p>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Nama Customer / Tujuan</label>
                                    <input type="text" name="customer" placeholder="Bpk. Budi / Produksi Mebel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-800 border-b pb-2 flex items-center gap-2">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i> Pilih Kayu
                                </h3>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Pilih Jenis Kayu</label>
                                    <select name="kayu_id" id="kayuSelectKeluar" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                                        <option value="" disabled selected>-- Pilih Jenis Kayu --</option>
                                        @foreach($daftarKayu as $kayu)
                                            <option value="{{ $kayu->id }}">{{ $kayu->jenis_kayu }} (Sisa: {{ $kayu->stok }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Panjang (m)</label>
                                        <input type="number" name="panjang" id="panjangKeluar" step="0.01" placeholder="0.00" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="calculateVolumeKeluar()" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Diameter (cm)</label>
                                        <input type="number" name="diameter" id="diameterKeluar" step="0.1" placeholder="0.0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="calculateVolumeKeluar()" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jumlah Keluar (Batang/Lembar)</label>
                                    <input type="number" name="jumlah" id="jumlahKeluar" step="1" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="calculateVolumeKeluar()" required>
                                    <p class="text-xs text-gray-400 mt-1">*Pastikan jumlah tidak melebihi sisa stok.</p>
                                </div>

                                <div id="volumeInfoKeluar" class="bg-orange-50 border border-orange-200 p-3 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700 font-medium">Total Volume:</span>
                                        <span class="text-lg font-bold text-orange-700" id="totalVolumeKeluarDisplay">0.0000 m³</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-bold shadow-md transition-colors">
                                Submit Surat Jalan Keluar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ============================ LAPORAN MUTASI ============================ --}}
            <div id="tab-laporan" class="tab-content hidden">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col min-h-[500px]">

                    <div class="border-b border-gray-100 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="font-semibold text-gray-800 text-lg">Laporan Mutasi Hasil Hutan</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Pilih periode lalu unduh sesuai format resmi (A1. Kayu Bulat).</p>
                        </div>
                        <div class="flex flex-wrap gap-2 w-full md:w-auto items-end">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Bulan</label>
                                <select id="filterBulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                                    @foreach($daftarBulan as $num => $nama)
                                        <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                                <select id="filterTahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                                    @foreach($daftarTahun as $th)
                                        <option value="{{ $th }}" {{ $th == $tahunSekarang ? 'selected' : '' }}>{{ $th }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" onclick="exportLaporan('excel')" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 border border-emerald-200">
                                <i data-lucide="download" class="w-4 h-4"></i> Excel
                            </button>
                            <button type="button" onclick="exportLaporan('pdf')" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 border border-red-200">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak PDF
                            </button>
                        </div>
                    </div>

                    {{-- Riwayat transaksi keseluruhan --}}
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 font-medium">No. Surat Jalan</th>
                                    <th class="px-6 py-3 font-medium">Tanggal & Waktu</th>
                                    <th class="px-6 py-3 font-medium">Tipe</th>
                                    <th class="px-6 py-3 font-medium">Jenis Kayu</th>
                                    <th class="px-6 py-3 font-medium">Jumlah</th>
                                    <th class="px-6 py-3 font-medium">Customer / Supplier</th>
                                    <th class="px-6 py-3 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($laporanTransaksi as $laporan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-semibold text-gray-800">{{ $laporan->kode_transaksi }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ \Carbon\Carbon::parse($laporan->waktu)->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-3">
                                        @if($laporan->tipe == 'masuk')
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">Barang Masuk</span>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">Barang Keluar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-800">{{ $laporan->kayu->jenis_kayu }}</td>
                                    <td class="px-6 py-3 text-gray-800 font-medium">{{ $laporan->jumlah }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $laporan->pihak_terkait }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tautan Edit & tombol Hapus diarahkan ke rute yang sesuai
                                                 berdasarkan tipe transaksi (masuk / keluar) --}}
                                            @if($laporan->tipe == 'masuk')
                                                <a href="{{ route('barang-masuk.edit', $laporan->id) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('barang-masuk.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi masuk {{ $laporan->kode_transaksi }}? Stok akan disesuaikan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('barang-keluar.edit', $laporan->id) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('barang-keluar.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi keluar {{ $laporan->kode_transaksi }}? Stok akan dikembalikan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada riwayat transaksi di sistem.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();

        // URL export dari Laravel (route helper)
        const URL_EXCEL = "{{ route('laporan.excel') }}";
        const URL_PDF   = "{{ route('laporan.pdf') }}";

        function exportLaporan(type) {
            const bulan = document.getElementById('filterBulan').value;
            const tahun = document.getElementById('filterTahun').value;
            const base  = type === 'excel' ? URL_EXCEL : URL_PDF;
            const url   = base + '?bulan=' + encodeURIComponent(bulan) + '&tahun=' + encodeURIComponent(tahun);
            if (type === 'pdf') {
                window.open(url, '_blank');
            } else {
                window.location = url;
            }
        }

        // ========== HITUNG VOLUME OTOMATIS (silinder: π/4 × d² × L) ==========
        function hitungVolume(panjang, diameterCm, jumlah) {
            if (panjang <= 0 || diameterCm <= 0 || jumlah <= 0) return 0;
            const dM = diameterCm / 100;
            return (Math.PI / 4) * (dM ** 2) * panjang * jumlah;
        }

        function calculateVolumeMasuk() {
            const panjang  = parseFloat(document.getElementById('panjangMasuk').value) || 0;
            const diameter = parseFloat(document.getElementById('diameterMasuk').value) || 0;
            const jumlah   = parseInt(document.getElementById('jumlahMasuk').value) || 0;
            document.getElementById('totalVolumeMasukDisplay').textContent = hitungVolume(panjang, diameter, jumlah).toFixed(4) + ' m³';
        }

        function calculateVolumeKeluar() {
            const panjang  = parseFloat(document.getElementById('panjangKeluar').value) || 0;
            const diameter = parseFloat(document.getElementById('diameterKeluar').value) || 0;
            const jumlah   = parseInt(document.getElementById('jumlahKeluar').value) || 0;
            document.getElementById('totalVolumeKeluarDisplay').textContent = hitungVolume(panjang, diameter, jumlah).toFixed(4) + ' m³';
        }

        // ========== GANTI TAB MENU ==========
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('bg-amber-800', 'text-white', 'font-medium', 'shadow-sm');
                btn.classList.add('text-amber-200', 'hover:bg-amber-800/50', 'hover:text-white');
                const icon = btn.querySelector('.icon-active');
                if (icon) icon.classList.remove('text-amber-400');
            });

            const activeBtn = document.getElementById('nav-' + tabId);
            activeBtn.classList.remove('text-amber-200', 'hover:bg-amber-800/50', 'hover:text-white');
            activeBtn.classList.add('bg-amber-800', 'text-white', 'font-medium', 'shadow-sm');
            const activeIcon = activeBtn.querySelector('.icon-active');
            if (activeIcon) activeIcon.classList.add('text-amber-400');

            document.getElementById('header-title').innerText = activeBtn.getAttribute('data-title');
            if (window.innerWidth < 768) toggleSidebar();
        }

        // ========== BUKA/TUTUP SIDEBAR ==========
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('overlay').classList.toggle('hidden');
        }
    </script>
</body>
</html>
