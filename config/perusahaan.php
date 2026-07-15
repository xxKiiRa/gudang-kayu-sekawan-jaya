<?php

/*
|--------------------------------------------------------------------------
| Profil Perusahaan (Kop Laporan Mutasi Hasil Hutan)
|--------------------------------------------------------------------------
| Semua identitas perusahaan dipusatkan di sini agar tidak ditulis ulang
| (hardcode) di banyak tempat (view PDF, export Excel, dsb). Cukup ubah di
| file ini, seluruh laporan ikut berubah.
|
| Bisa juga dioverride lewat .env bila diperlukan.
*/

return [
    'nama'            => env('PERUSAHAAN_NAMA', 'UD. SEKAWAN JAYA'),
    'alamat'          => env('PERUSAHAAN_ALAMAT', 'Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Pg. Harjo, Bantul'),
    'jenis_industri'  => env('PERUSAHAAN_JENIS_INDUSTRI', 'Kayu Jati'),
    'lokasi_industri' => env('PERUSAHAAN_LOKASI_INDUSTRI', 'Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Pg. Harjo, Bantul'),
    'dinas_kehutanan' => env('PERUSAHAAN_DINAS_KEHUTANAN', '-'),
    'propinsi'        => env('PERUSAHAAN_PROPINSI', 'D.I Yogyakarta'),
    'kota'            => env('PERUSAHAAN_KOTA', 'Yogyakarta'),
    'pimpinan'        => env('PERUSAHAAN_PIMPINAN', 'RUSDIYANTO'),
    'jabatan'         => env('PERUSAHAAN_JABATAN', 'Pimpinan/Pemilik'),
];
