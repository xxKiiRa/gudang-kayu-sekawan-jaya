<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class, // akun admin untuk login
            GudangSeeder::class,    // data contoh kayu & transaksi
        ]);
    }
}