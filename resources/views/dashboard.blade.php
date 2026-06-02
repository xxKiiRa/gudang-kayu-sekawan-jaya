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
                <button id="nav-master" data-title="Data Master & Migrasi" onclick="switchTab('master')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="database" class="w-5 h-5 icon-active"></i>
                    <span>Data Master & Migrasi</span>
                </button>
                <button id="nav-masuk" data-title="Barang Masuk" onclick="switchTab('masuk')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="arrow-down-to-line" class="w-5 h-5 icon-active"></i>
                    <span>Barang Masuk</span>
                </button>
                <button id="nav-keluar" data-title="Barang Keluar" onclick="switchTab('keluar')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="arrow-up-from-line" class="w-5 h-5 icon-active"></i>
                    <span>Barang Keluar</span>
                </button>
                <button id="nav-laporan" data-title="Laporan" onclick="switchTab('laporan')" class="nav-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-amber-200 hover:bg-amber-800/50 hover:text-white">
                    <i data-lucide="file-text" class="w-5 h-5 icon-active"></i>
                    <span>Laporan</span>
                </button>
            </nav>
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

        <div class="flex-1 overflow-auto p-4 md:p-8">
            
            <div id="tab-dashboard" class="tab-content space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-500 text-white"><i data-lucide="database" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Total Stok Kayu</p><h3 class="text-2xl font-bold text-gray-800">750</h3><p class="text-xs text-gray-400 mt-0.5">Batang / Lembar</p></div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-emerald-500 text-white"><i data-lucide="arrow-down-to-line" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Barang Masuk</p><h3 class="text-2xl font-bold text-gray-800">150</h3><p class="text-xs text-gray-400 mt-0.5">Bulan Ini</p></div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-amber-500 text-white"><i data-lucide="arrow-up-from-line" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Barang Keluar</p><h3 class="text-2xl font-bold text-gray-800">20</h3><p class="text-xs text-gray-400 mt-0.5">Bulan Ini</p></div>
                    </div>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-red-500 text-white"><i data-lucide="bell" class="w-6 h-6"></i></div>
                        <div><p class="text-gray-500 text-sm font-medium">Peringatan Stok</p><h3 class="text-2xl font-bold text-gray-800">1</h3><p class="text-xs text-gray-400 mt-0.5">Item Menipis</p></div>
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
                                        <th class="px-6 py-3 font-medium">Dimensi (PxLxT)</th>
                                        <th class="px-6 py-3 font-medium">Ukuran (Size)</th>
                                        <th class="px-6 py-3 font-medium text-right">Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-medium text-gray-800">Jati</td>
                                        <td class="px-6 py-3 text-gray-600">400x20x15</td>
                                        <td class="px-6 py-3"><span class="px-2 py-1 rounded text-xs font-medium border bg-indigo-50 text-indigo-700 border-indigo-200">Besar</span></td>
                                        <td class="px-6 py-3 text-right"><span class="font-semibold text-gray-800">150</span><span class="text-gray-500 text-xs ml-1">Batang</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-medium text-gray-800">Mahoni</td>
                                        <td class="px-6 py-3 text-gray-600">300x15x10</td>
                                        <td class="px-6 py-3"><span class="px-2 py-1 rounded text-xs font-medium border bg-teal-50 text-teal-700 border-teal-200">Sedang</span></td>
                                        <td class="px-6 py-3 text-right"><span class="font-semibold text-gray-800">85</span><span class="text-gray-500 text-xs ml-1">Batang</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-medium text-gray-800">Sengon</td>
                                        <td class="px-6 py-3 text-gray-600">200x10x5</td>
                                        <td class="px-6 py-3"><span class="px-2 py-1 rounded text-xs font-medium border bg-teal-50 text-teal-700 border-teal-200">Sedang</span></td>
                                        <td class="px-6 py-3 text-right"><span class="font-semibold text-gray-800">320</span><span class="text-gray-500 text-xs ml-1">Lembar</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-800">Aktivitas Terakhir</h2></div>
                        <div class="p-4 flex-1 overflow-y-auto max-h-[350px] space-y-4">
                            <div class="flex gap-4 items-start pb-4 border-b border-gray-50">
                                <div class="mt-1 w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-emerald-100 text-emerald-600"><i data-lucide="arrow-down-to-line" class="w-4 h-4"></i></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Jati (Besar) x 50</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Relasi: CV. Kayu Makmur</p>
                                    <p class="text-[11px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded inline-block mt-1">Asal: Hutan Blora</p>
                                    <p class="text-xs text-gray-400 mt-1">2026-04-07 08:30 • SJ-IN-001</p>
                                </div>
                            </div>
                            <div class="flex gap-4 items-start pb-4 border-b border-gray-50">
                                <div class="mt-1 w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-amber-100 text-amber-600"><i data-lucide="arrow-up-from-line" class="w-4 h-4"></i></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Sengon (Sedang) x 20</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Customer: Bpk. Budi</p>
                                    <p class="text-xs text-gray-400 mt-1">2026-04-07 10:15 • SJ-OUT-101</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-master" class="tab-content hidden space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-blue-50 border-b border-blue-100 px-6 py-4">
                        <h2 class="font-semibold text-blue-800 text-lg flex items-center gap-2"><i data-lucide="file-spreadsheet" class="w-5 h-5"></i> Migrasi Data Historis (Import Excel)</h2>
                        <p class="text-sm text-blue-600 mt-1">Fasilitas untuk memasukkan data stok atau transaksi lama (sejak tahun 2000).</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4">
                                <h4 class="font-semibold text-yellow-800 text-sm flex items-center gap-2 mb-2"><i data-lucide="alert-circle" class="w-4 h-4"></i> Petunjuk Migrasi Data Lama</h4>
                                <ul class="text-sm text-yellow-700 space-y-1.5 list-disc pl-5">
                                    <li>Disarankan hanya meng-import <b>Saldo Stok Awal</b> saat ini.</li>
                                    <li>Format kolom wajib: Tanggal, Tipe, Jenis, Ukuran, Dimensi, Jumlah, Relasi, Asal Kayu.</li>
                                </ul>
                                <button class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium underline flex items-center gap-1"><i data-lucide="download" class="w-3 h-3"></i> Unduh Template Excel (.xlsx)</button>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Data yang Di-import:</label>
                            <div class="flex gap-4 mb-4">
                                <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="importType" checked class="text-blue-600" /> Saldo Stok Awal</label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="importType" class="text-blue-600" /> Transaksi Lama</label>
                            </div>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer mb-4">
                                <i data-lucide="upload" class="text-gray-400 mb-2 w-8 h-8"></i>
                                <p class="text-sm font-medium text-gray-600">Klik atau drag file Excel (.xlsx / .csv)</p>
                            </div>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm" onclick="alert('Fungsi ini akan aktif setelah disambungkan ke Backend Laravel.')">Mulai Import Data</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-masuk" class="tab-content hidden">
                <div class="max-w-4xl bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-100">
                        <h2 class="font-semibold text-emerald-800 text-lg flex items-center gap-2"><i data-lucide="arrow-down-to-line" class="w-5 h-5"></i> Form Penerimaan Kayu (Masuk)</h2>
                    </div>
                    <form class="p-6 space-y-6" onsubmit="event.preventDefault(); alert('Tersimpan!');">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-800 border-b pb-2">Informasi Penerimaan</h3>
                                <div><label class="block text-sm text-emerald-700 font-semibold mb-1">No. Surat Jalan / Nota</label><input type="text" placeholder="SJ-IN-..." class="w-full border border-emerald-300 bg-emerald-50/30 rounded-lg px-3 py-2 text-sm outline-none focus:border-emerald-500" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Tanggal Masuk</label><input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Nama Supplier (Manual)</label><input type="text" placeholder="Nama CV/PT" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Asal Kayu</label><input type="text" placeholder="Hutan Blora" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                            </div>
                            <div class="space-y-4">
                                <h3 class="font-medium text-gray-800 border-b pb-2">Detail Kayu</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="block text-sm text-gray-600 mb-1">Jenis Kayu</label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none"><option>Jati</option><option>Mahoni</option></select></div>
                                    <div><label class="block text-sm text-gray-600 mb-1">Ukuran (Size)</label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none"><option>Besar</option><option>Sedang</option></select></div>
                                </div>
                                <div><label class="block text-sm text-gray-600 mb-1">Dimensi (cm)</label><input type="text" placeholder="400x20x15" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="block text-sm text-gray-600 mb-1">Jumlah</label><input type="number" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                    <div><label class="block text-sm text-gray-600 mb-1">Satuan</label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none"><option>Batang</option><option>Lembar</option></select></div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">Simpan Barang Masuk</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-keluar" class="tab-content hidden">
                <div class="max-w-5xl bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row">
                    <div class="w-full md:w-1/2 p-6 bg-amber-50/30 border-b md:border-b-0 md:border-r border-gray-200">
                        <h2 class="font-semibold text-amber-800 text-lg flex items-center gap-2 mb-6"><i data-lucide="arrow-up-from-line" class="w-5 h-5"></i> Form Pengeluaran Kayu</h2>
                        <form class="space-y-5" onsubmit="event.preventDefault(); alert('Surat Jalan Berhasil Disubmit!');">
                            <div class="space-y-3 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div><label class="block text-sm text-amber-700 font-semibold mb-1">No. Surat Jalan (Keluar)</label><input type="text" placeholder="SJ-OUT-..." class="w-full border border-amber-300 bg-amber-50/30 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Tanggal Keluar</label><input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                                <div><label class="block text-sm text-gray-600 mb-1">Nama Customer</label><input type="text" placeholder="Bpk. Budi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required></div>
                            </div>
                            <div class="space-y-3 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <h3 class="font-medium text-gray-800 flex items-center gap-2"><i data-lucide="shopping-cart" class="w-4 h-4"></i> Pilih Kayu</h3>
                                <div><label class="block text-sm text-gray-600 mb-1">Pilih dari Stok</label><select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none"><option>Jati - Size Besar (400x20x15) - Sisa: 150</option></select></div>
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1"><label class="block text-sm text-gray-600 mb-1">Jumlah</label><input type="number" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none"></div>
                                    <button type="button" onclick="alert('Di Laravel nanti ini akan masuk ke tabel temporary / session sebelum disubmit')" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-900 transition-colors">Tambah Ke Keranjang</button>
                                </div>
                            </div>
                            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-bold shadow-md">Submit Surat Jalan Keluar</button>
                        </form>
                    </div>
                    <div class="w-full md:w-1/2 p-6 bg-white">
                        <h3 class="font-medium text-gray-800 mb-4 border-b pb-2">Daftar Kayu yang Akan Dikeluarkan</h3>
                        <div class="h-full flex items-center justify-center text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-lg min-h-[200px]">
                            Keranjang masih kosong. Pilih kayu di sebelah kiri.
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-laporan" class="tab-content hidden">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col min-h-[500px]">
                    <div class="border-b border-gray-100 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h2 class="font-semibold text-gray-800 text-lg">Laporan Riwayat Transaksi</h2>
                        <div class="flex gap-2 w-full md:w-auto">
                            <button class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 border border-emerald-200"><i data-lucide="download" class="w-4 h-4"></i> Excel</button>
                            <button class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 border border-red-200"><i data-lucide="download" class="w-4 h-4"></i> PDF</button>
                        </div>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 font-medium">No. Surat Jalan</th>
                                    <th class="px-6 py-3 font-medium">Tanggal</th>
                                    <th class="px-6 py-3 font-medium">Tipe</th>
                                    <th class="px-6 py-3 font-medium">Kayu & Ukuran</th>
                                    <th class="px-6 py-3 font-medium">Customer/Supplier</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-semibold text-gray-800">SJ-IN-001</td>
                                    <td class="px-6 py-3 text-gray-600">2026-04-07</td>
                                    <td class="px-6 py-3"><span class="px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-700">Masuk</span></td>
                                    <td class="px-6 py-3 text-gray-800">Jati (Besar) x 50</td>
                                    <td class="px-6 py-3 text-gray-600">CV. Kayu Makmur</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Menginisialisasi Ikon Lucide
        lucide.createIcons();

        // Fungsi untuk mengganti Tab Menu
        function switchTab(tabId) {
            // Sembunyikan semua konten tab
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            // Tampilkan tab yang dipilih
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            // Reset gaya semua tombol menu di Sidebar
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('bg-amber-800', 'text-white', 'font-medium', 'shadow-sm');
                btn.classList.add('text-amber-200', 'hover:bg-amber-800/50', 'hover:text-white');
                
                // Reset warna ikon
                const icon = btn.querySelector('.icon-active');
                if(icon) icon.classList.remove('text-amber-400');
            });

            // Beri gaya khusus untuk tombol menu yang aktif
            const activeBtn = document.getElementById('nav-' + tabId);
            activeBtn.classList.remove('text-amber-200', 'hover:bg-amber-800/50', 'hover:text-white');
            activeBtn.classList.add('bg-amber-800', 'text-white', 'font-medium', 'shadow-sm');
            
            // Beri warna khusus pada ikon menu yang aktif
            const activeIcon = activeBtn.querySelector('.icon-active');
            if(activeIcon) activeIcon.classList.add('text-amber-400');

            // Ubah Judul Header
            document.getElementById('header-title').innerText = activeBtn.getAttribute('data-title');

            // Tutup sidebar di layar HP setiap kali menu diklik
            if(window.innerWidth < 768) {
                toggleSidebar();
            }
        }

        // Fungsi untuk membuka/menutup Sidebar di Layar HP (Mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>