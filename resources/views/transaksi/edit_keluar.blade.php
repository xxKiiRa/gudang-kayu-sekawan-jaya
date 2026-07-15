<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang Keluar - {{ $keluar->kode_transaksi }}</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-800">

    <div class="max-w-3xl mx-auto p-4 md:p-8">

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
        </a>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-4">
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-4 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-amber-50 px-6 py-4 border-b border-amber-100">
                <h1 class="font-semibold text-amber-800 text-lg flex items-center gap-2">
                    <i data-lucide="pencil" class="w-5 h-5"></i> Edit Barang Keluar
                </h1>
                <p class="text-sm text-amber-700/70 mt-0.5">Mengubah jumlah akan otomatis menyesuaikan stok.</p>
            </div>

            <form action="{{ route('barang-keluar.update', $keluar->id) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Jenis kayu dikunci demi konsistensi stok --}}
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jenis Kayu (tidak dapat diubah)</label>
                    <input type="text" value="{{ $keluar->kayu->jenis_kayu }} (Sisa stok: {{ $keluar->kayu->stok }})" class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-sm text-gray-500" disabled>
                    <p class="text-xs text-gray-400 mt-1">Untuk mengganti jenis kayu, hapus transaksi ini lalu buat baru.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm text-amber-700 font-semibold mb-1">No. Surat Jalan (Keluar)</label>
                        <input type="text" name="kode_transaksi" value="{{ old('kode_transaksi', $keluar->kode_transaksi) }}" class="w-full border border-amber-300 bg-amber-50/30 rounded-lg px-3 py-2 text-sm outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tanggal & Waktu Keluar</label>
                        <input type="datetime-local" name="waktu_keluar" value="{{ old('waktu_keluar', \Carbon\Carbon::parse($keluar->waktu_keluar)->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jenis Penggunaan</label>
                        <select name="jenis_penggunaan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" required>
                            <option value="diolah_sendiri" {{ old('jenis_penggunaan', $keluar->jenis_penggunaan) == 'diolah_sendiri' ? 'selected' : '' }}>Diolah Sendiri</option>
                            <option value="penggunaan_lain" {{ old('jenis_penggunaan', $keluar->jenis_penggunaan) == 'penggunaan_lain' ? 'selected' : '' }}>Penggunaan Lain</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nama Customer / Tujuan</label>
                        <input type="text" name="customer" value="{{ old('customer', $keluar->customer) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Panjang (m)</label>
                        <input type="number" name="panjang" id="panjang" step="0.01" value="{{ old('panjang', $keluar->panjang) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="hitungVolume()" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Diameter (cm)</label>
                        <input type="number" name="diameter" id="diameter" step="0.1" value="{{ old('diameter', $keluar->diameter) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="hitungVolume()" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jumlah (Batang)</label>
                        <input type="number" name="jumlah" id="jumlah" step="1" value="{{ old('jumlah', $keluar->jumlah) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none" oninput="hitungVolume()" required>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 p-3 rounded-lg flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Total Volume:</span>
                    <span class="text-lg font-bold text-orange-700" id="volumeDisplay">0.0000 m³</span>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function hitungVolume() {
            const panjang  = parseFloat(document.getElementById('panjang').value) || 0;
            const diameter = parseFloat(document.getElementById('diameter').value) || 0;
            const jumlah   = parseInt(document.getElementById('jumlah').value) || 0;
            let vol = 0;
            if (panjang > 0 && diameter > 0 && jumlah > 0) {
                const dM = diameter / 100;
                vol = (Math.PI / 4) * (dM ** 2) * panjang * jumlah;
            }
            document.getElementById('volumeDisplay').textContent = vol.toFixed(4) + ' m³';
        }
        hitungVolume();
    </script>
</body>
</html>
