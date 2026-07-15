<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Gudang Kayu UD Sekawan Jaya</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center p-4 font-sans text-gray-800">

    <div class="w-full max-w-md">

        {{-- Logo / judul --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-900 text-amber-300 mb-3 shadow-lg">
                <i data-lucide="database" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-bold text-amber-900">UD Sekawan Jaya</h1>
            <p class="text-sm text-amber-700/70">Sistem Manajemen Gudang Kayu</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-lg">Masuk ke Akun</h2>
                <p class="text-sm text-gray-500 mt-0.5">Silakan login untuk mengakses dashboard.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="p-8 space-y-5">
                @csrf

                {{-- Pesan sukses (mis. setelah logout) --}}
                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Pesan error (mis. password salah) --}}
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@sekawanjaya.test"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required autofocus>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="w-full bg-amber-900 hover:bg-amber-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Masuk
                </button>

                <p class="text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-amber-700 font-medium hover:underline">Daftar di sini</a>
                </p>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">&copy; {{ date('Y') }} UD Sekawan Jaya</p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
