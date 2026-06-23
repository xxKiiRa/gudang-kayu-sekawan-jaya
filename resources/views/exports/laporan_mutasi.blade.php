<table>
    <tr>
        <td colspan="3">Nama Perusahaan</td>
        <td colspan="5">: UD. SEKAWAN JAYA</td>
        <td colspan="8">LAPORAN MUTASI HASIL HUTAN</td>
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
    <tr><td colspan="16"></td></tr>

    <tr><td colspan="16" style="font-weight: bold;">A1. KAYU BULAT</td></tr>

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

    @php 
        $totAwal = 0; $totMasuk = 0; $totOlahSendiri = 0; $totPenggunaanLain = 0; $totAkhir = 0; 
    @endphp

    @foreach($data as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            
            <td>{{ $row['jenis_kayu'] }}</td>
            <td>{{ $row['awal_batang'] }}</td>
            <td>{{ $row['volume_awal'] }}</td>

            <td>{{ $row['jenis_kayu'] }}</td>
            <td>{{ $row['masuk_batang'] }}</td>
            <td>{{ $row['volume_masuk'] }}</td>

            <td>{{ $row['jenis_kayu'] }}</td>
            <td>{{ $row['jumlah_olah_sendiri'] }}</td>
            <td>{{ $row['volume_olah_sendiri'] }}</td>
            
            <td>{{ $row['jumlah_penggunaan_lain'] }}</td>
            <td>{{ $row['volume_penggunaan_lain'] }}</td>

            <td>{{ $row['jenis_kayu'] }}</td>
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
        <td colspan="2" style="font-weight: bold;">Jumlah:</td>
        <td style="font-weight: bold;">{{ $totAwal }}</td>
        <td style="font-weight: bold;">-</td>
        <td></td>
        <td style="font-weight: bold;">{{ $totMasuk }}</td>
        <td style="font-weight: bold;">-</td>
        <td></td>
        <td style="font-weight: bold;">{{ $totOlahSendiri }}</td>
        <td style="font-weight: bold;">-</td>
        <td style="font-weight: bold;">{{ $totPenggunaanLain }}</td>
        <td style="font-weight: bold;">-</td>
        <td></td>
        <td style="font-weight: bold;">{{ $totAkhir }}</td>
        <td style="font-weight: bold;">-</td>
        <td></td>
    </tr>

    <tr><td colspan="16"></td></tr>
    <tr><td colspan="16"></td></tr>
    <tr>
        <td colspan="12"></td>
        <td colspan="4">Yogyakarta, {{ $tanggal }}</td>
    </tr>
    <tr>
        <td colspan="12"></td>
        <td colspan="4">Pimpinan/Pemilik,</td>
    </tr>
    <tr><td colspan="16"></td></tr>
    <tr><td colspan="16"></td></tr>
    <tr><td colspan="16"></td></tr>
    <tr>
        <td colspan="12"></td>
        <td colspan="4"><b><u>RUSDIYANTO</u></b></td>
    </tr>
</table>