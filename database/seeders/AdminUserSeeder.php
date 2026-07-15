<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat akun admin default agar bisa langsung login (untuk demo/ujian).
 *
 * firstOrCreate: kalau email sudah ada, tidak membuat ganda (aman di-seed ulang).
 *
 * KREDENSIAL DEFAULT:
 *   Email    : admin@sekawanjaya.test
 *   Password : password123
 *   (Sebaiknya diganti setelah login pertama.)
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sekawanjaya.test'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
