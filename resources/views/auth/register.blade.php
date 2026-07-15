<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Gudang Kayu UD Sekawan Jaya</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center p-4 font-sans text-gray-800">

    <div class="w-full max-w-md">

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-900 text-amber-300 mb-3 shadow-lg">
                <i data-lucide="database" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-bold text-amber-900">UD Sekawan Jaya</h1>
            <p class="text-sm text-amber-700/70">Sistem Manajemen Gudang Kayu</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-lg">Buat Akun Baru</h2>
                <p class="text-sm text-gray-500 mt-0.5">Daftarkan akun admin untuk mengelola gudang.</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="p-8 space-y-5">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-lg text-sm">
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama admin"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required autofocus>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@sekawanjaya.test"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>

                <button type="submit" class="w-full bg-amber-900 hover:bg-amber-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Daftar
                </button>

                <p class="text-center text-sm text-gray-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-amber-700 font-medium hover:underline">Masuk di sini</a>
                </p>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">&copy; {{ date('Y') }} UD Sekawan Jaya</p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
