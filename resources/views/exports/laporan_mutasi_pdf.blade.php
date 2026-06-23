<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Hutan</title>
    <style>
        /* Pengaturan Dasar Kertas & Font */
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
        
        /* Tabel Kop & Tanda Tangan (Tanpa Garis) */
        .table-no-border { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-no-border td { padding: 2px; vertical-align: top; }
        
        /* Tabel Utama (Dengan Garis Pejal) */
        .table-data { width: 100%; border-collapse: collapse; }
        .table-data th, .table-data td { border: 1px solid black; padding: 4px; text-align: center; vertical-align: middle; }
        .table-data th { font-weight: bold; background-color: #f9f9f9; }
        
        /* Teks Khusus */
        .judul-dokumen { font-size: 14px; font-weight: bold; text-decoration: underline; text-align: center; }
        .text-left { text-align: left !important; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>

    <table class="table-no-border">
        <tr>
            <td width="18%">Nama Perusahaan</td>
            <td width="32%">: UD. SEKAWAN JAYA</td>
            <td width="50%" class="judul-dokumen">LAPORAN MUTASI HASIL HUTAN</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Bantul</td>
            <td></td>
        </tr>
        <tr>
            <td>Jenis Industri</td>
            <td>: Kayu Jati & Sengon</td>
            <td></td>
        </tr>
        <tr>
            <td>Lokasi Industri</td>
            <td>: Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Pg. Harjo, Bantul</td>
            <td></td>
        </tr>
        <tr>
            <td>Dinas Kehutanan</td>
            <td>: -</td>
            <td></td>
        </tr>
        <tr>
            <td>Propinsi</td>
            <td>: D.I Yogyakarta</td>
            <td></td>
        </tr>
    </table>

    <p class="bold" style="margin-bottom: 5px;">A1. KAYU BULAT</p>

    <table class="table-data">
        <thead>
            <tr>
                <th rowspan="3">No</th>
                <th colspan="3">Persediaan Akhir Bulan Lalu</th>
                <th colspan="3">Perolehan Kayu Bulat</th>
                <th colspan="3">Diolah Sendiri</th>
                <th colspan="2">Penggunaan Lain</th>
                <th colspan="3">Persediaan Bulan Ini</th>
                <th rowspan="3">Keterangan</th>
            </tr>
            <tr>
                <th rowspan="2">Jenis Kayu</th>
                <th rowspan="2">Jumlah Batang</th>
                <th rowspan="2">Volume M3/Ton</th>
                <th rowspan="2">Jenis Kayu</th>
                <th rowspan="2">Jumlah Batang</th>
                <th rowspan="2">Volume M3/Ton</th>
                <th rowspan="2">Jenis Kayu</th>
                <th rowspan="2">Jumlah Batang</th>
                <th rowspan="2">Volume M3/Ton</th>
                <th rowspan="2">Jumlah Batang</th>
                <th rowspan="2">Volume M3/Ton</th>
                <th rowspan="2">Jenis Kayu</th>
                <th rowspan="2">Jumlah Batang</th>
                <th rowspan="2">Volume M3/Ton</th>
            </tr>
            <tr></tr>
            <tr>
                @for($i=1; $i<=16; $i++)
                    <td>{{ $i }}</td>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php 
                $totAwal = 0; $totMasuk = 0; $totOlahSendiri = 0; $totPenggunaanLain = 0; $totAkhir = 0; 
            @endphp
            @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    
                    <td class="text-left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ $row['awal_batang'] }}</td>
                    <td>{{ $row['volume_awal'] }}</td>

                    <td class="text-left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ $row['masuk_batang'] }}</td>
                    <td>{{ $row['volume_masuk'] }}</td>

                    <td class="text-left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ $row['jumlah_olah_sendiri'] }}</td>
                    <td>{{ $row['volume_olah_sendiri'] }}</td>
                    
                    <td>{{ $row['jumlah_penggunaan_lain'] }}</td>
                    <td>{{ $row['volume_penggunaan_lain'] }}</td>

                    <td class="text-left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ $row['akhir_batang'] }}</td>
                    <td>{{ $row['volume_akhir'] }}</td>
                    
                    <td>{{ $row['keterangan'] }}</td>
                </tr>
                @php
                    $totAwal += $row['awal_batang'];
                    $totMasuk += $row['masuk_batang'];
                    $totOlahSendiri += $row['jumlah_olah_sendiri'];
                    $totPenggunaanLain += $row['jumlah_penggunaan_lain'];
                    $totAkhir += $row['akhir_batang'];
                @endphp
            @endforeach
            
            <tr>
                <td colspan="2" class="bold text-left">Jumlah:</td>
                <td class="bold">{{ $totAwal }}</td>
                <td class="bold">-</td>
                <td></td>
                <td class="bold">{{ $totMasuk }}</td>
                <td class="bold">-</td>
                <td></td>
                <td class="bold">{{ $totOlahSendiri }}</td>
                <td class="bold">-</td>
                <td class="bold">{{ $totPenggunaanLain }}</td>
                <td class="bold">-</td>
                <td></td>
                <td class="bold">{{ $totAkhir }}</td>
                <td class="bold">-</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="table-no-border" style="margin-top: 30px;">
        <tr>
            <td width="70%"></td>
            <td width="30%" style="text-align: center;">
                Yogyakarta, {{ $tanggal }}<br>
                Pimpinan/Pemilik,<br>
                <br><br><br><br> <span class="bold" style="text-decoration: underline;">RUSDIYANTO</span>
            </td>
        </tr>
    </table>

</body>
</html>