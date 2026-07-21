<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * ============================================================================
 * CONTROLLER: AuthController  (Login / Register / Logout)
 * ----------------------------------------------------------------------------
 * BUAT APA?
 *   Menangani autentikasi memakai fitur bawaan Laravel (facade Auth), tanpa
 *   paket tambahan. Satu peran saja: setiap user yang login = admin dengan
 *   akses penuh (tidak ada peran operator).
 *
 * ALUR SINGKAT:
 *   - Login   : cek email+password → kalau cocok, buat sesi → ke dashboard
 *   - Register: buat user baru (password di-hash) → langsung login
 *   - Logout  : hapus sesi → kembali ke halaman login
 * ============================================================================
 */
class AuthController extends Controller
{
    /** Tampilkan halaman login (kalau sudah login, langsung ke dashboard). */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /** Proses percobaan login. */
    public function login(Request $request)
    {
        // 1) Validasi input
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [], [
            'email'    => 'email',
            'password' => 'password',
        ]);

        // 2) Auth::attempt mencocokkan email + password (otomatis membandingkan
        //    password ter-hash). Parameter kedua = fitur "ingat saya".
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // 3) Ganti ID sesi untuk mencegah session fixation (keamanan)
            $request->session()->regenerate();

            // intended() = kembali ke halaman yang tadi dituju sebelum login
            return redirect()->intended(route('dashboard'));
        }

        // 4) Gagal → kembali dengan pesan error (tanpa membocorkan field mana yg salah)
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }



    /** Logout: hapus sesi & token, kembali ke login. */
    public function logout(Request $request)
    {
        Auth::logout();

        // Batalkan sesi lama + buat token CSRF baru (keamanan)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
