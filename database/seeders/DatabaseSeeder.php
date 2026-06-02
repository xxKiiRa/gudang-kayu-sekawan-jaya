<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil seeder yang baru kita buat
        $this->call([
            GudangSeeder::class,
        ]);
    }
}
