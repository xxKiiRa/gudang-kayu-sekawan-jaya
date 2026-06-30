<!-- Cukup tambahkan border="1" di sini, Excel akan otomatis menggaris semua kolom -->
<table border="1" style="border-collapse: collapse;">
    <tr>
        <td colspan="3">Nama Perusahaan</td>
        <td colspan="5">: UD. SEKAWAN JAYA</td>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="3">Alamat</td>
        <td colspan="13">: Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Pg. Harjo, Bantul</td>
    </tr>
    <tr>
        <td colspan="3">Jenis Industri</td>
        <td colspan="13">: Kayu Jati &amp; Sengon</td>
    </tr>
    <tr>
        <td colspan="3">Propinsi</td>
        <td colspan="13">: D.I Yogyakarta</td>
    </tr>
    <tr>
        <td colspan="3">Lokasi Industri</td>
        <td colspan="13">: Ring Road Selatan, Jl. Kapuk, Tegal Krapyak, Pg. Harjo, Bantul</td>
    </tr>
    <tr>
        <td colspan="3">Dinas Kehutanan</td>
        <td colspan="13">: -</td>
    </tr>
    
    <!-- Judul Laporan -->
    <tr>
        <td colspan="16" style="text-align: center; font-weight: bold;">LAPORAN MUTASI HASIL HUTAN</td>
    </tr>

    <tr><td colspan="16" style="font-weight: bold;">A1. KAYU BULAT</td></tr>

    <tr>
        <th rowspan="3" style="text-align: center; vertical-align: middle;">No</th>
        <th colspan="3" style="text-align: center;">Persediaan Akhir Bulan Lalu</th>
        <th colspan="3" style="text-align: center;">Perolehan Kayu Bulat</th>
        <th colspan="3" style="text-align: center;">Diolah Sendiri</th>
        <th colspan="2" style="text-align: center;">Penggunaan Lain</th>
        <th colspan="3" style="text-align: center;">Persediaan Bulan Ini</th>
        <th rowspan="3" style="text-align: center; vertical-align: middle;">Keterangan</th>
    </tr>
    <tr>
        <th rowspan="2" style="text-align: center;">Jenis Kayu</th>
        <th rowspan="2" style="text-align: center;">Jumlah Batang</th>
        <th rowspan="2" style="text-align: center;">Volume M3/Ton</th>
        <th rowspan="2" style="text-align: center;">Jenis Kayu</th>
        <th rowspan="2" style="text-align: center;">Jumlah Batang</th>
        <th rowspan="2" style="text-align: center;">Volume M3/Ton</th>
        <th rowspan="2" style="text-align: center;">Jenis Kayu</th>
        <th rowspan="2" style="text-align: center;">Jumlah Batang</th>
        <th rowspan="2" style="text-align: center;">Volume M3/Ton</th>
        <th rowspan="2" style="text-align: center;">Jumlah Batang</th>
        <th rowspan="2" style="text-align: center;">Volume M3/Ton</th>
        <th rowspan="2" style="text-align: center;">Jenis Kayu</th>
        <th rowspan="2" style="text-align: center;">Jumlah Batang</th>
        <th rowspan="2" style="text-align: center;">Volume M3/Ton</th>
    </tr>
    <tr></tr>

    <tr>
        @for($i=1; $i<=16; $i++)
            <td style="text-align: center;">{{ $i }}</td>
        @endfor
    </tr>

    @php 
        $totAwal = 0; $totMasuk = 0; $totOlahSendiri = 0; $totPenggunaanLain = 0; $totAkhir = 0; 
    @endphp

    @foreach($data as $index => $row)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            
            <td>{{ $row['jenis_kayu'] }}</td>
            <td style="text-align: center;">{{ $row['awal_batang'] }}</td>
            <td style="text-align: center;">{{ $row['volume_awal'] }}</td>

            <td>{{ $row['jenis_kayu'] }}</td>
            <td style="text-align: center;">{{ $row['masuk_batang'] }}</td>
            <td style="text-align: center;">{{ $row['volume_masuk'] }}</td>

            <td>{{ $row['jenis_kayu'] }}</td>
            <td style="text-align: center;">{{ $row['jumlah_olah_sendiri'] }}</td>
            <td style="text-align: center;">{{ $row['volume_olah_sendiri'] }}</td>
            
            <td style="text-align: center;">{{ $row['jumlah_penggunaan_lain'] }}</td>
            <td style="text-align: center;">{{ $row['volume_penggunaan_lain'] }}</td>

            <td>{{ $row['jenis_kayu'] }}</td>
            <td style="text-align: center;">{{ $row['akhir_batang'] }}</td>
            <td style="text-align: center;">{{ $row['volume_akhir'] }}</td>
            
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
        <td colspan="2" style="font-weight: bold; text-align: right;">Jumlah:</td>
        <td style="font-weight: bold; text-align: center;">{{ $totAwal }}</td>
        <td style="font-weight: bold; text-align: center;">-</td>
        <td></td>
        <td style="font-weight: bold; text-align: center;">{{ $totMasuk }}</td>
        <td style="font-weight: bold; text-align: center;">-</td>
        <td></td>
        <td style="font-weight: bold; text-align: center;">{{ $totOlahSendiri }}</td>
        <td style="font-weight: bold; text-align: center;">-</td>
        <td style="font-weight: bold; text-align: center;">{{ $totPenggunaanLain }}</td>
        <td style="font-weight: bold; text-align: center;">-</td>
        <td></td>
        <td style="font-weight: bold; text-align: center;">{{ $totAkhir }}</td>
        <td style="font-weight: bold; text-align: center;">-</td>
        <td></td>
    </tr>

    <tr>
        <td colspan="12"></td>
        <td colspan="4" style="text-align: center;">Yogyakarta, {{ $tanggal ?? '30 June 2026' }}</td>
    </tr>
    <tr>
        <td colspan="12"></td>
        <td colspan="4" style="text-align: center;">Pimpinan/Pemilik,</td>
    </tr>
    <tr>
        <td colspan="12"></td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="12"></td>
        <td colspan="4" style="text-align: center; font-weight: bold;"><u>RUSDIYANTO</u></td>
    </tr>
</table>