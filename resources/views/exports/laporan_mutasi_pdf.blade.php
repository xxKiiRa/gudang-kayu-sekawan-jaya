@php
    use App\Services\MutasiReportService as M;
    $p = config('perusahaan');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Hasil Hutan - {{ $report['periode']['label'] }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
        }

        /* ---- Kop (tanpa garis) ---- */
        .kop { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kop td { padding: 1px 2px; vertical-align: top; }
        .kop .label { width: 130px; }
        .kop .sep { width: 8px; }
        .judul {
            font-size: 14px; font-weight: bold; text-decoration: underline;
            text-align: center; vertical-align: middle;
        }

        .section-title { font-weight: bold; margin: 4px 0 5px; }

        /* ---- Tabel utama (bergaris) ---- */
        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data th, table.data td {
            border: 1px solid #000; padding: 3px 2px; text-align: center;
            vertical-align: middle; word-wrap: break-word;
        }
        table.data thead th { background: #e8e8e8; font-weight: bold; font-size: 9px; }
        table.data td.left { text-align: left; }
        table.data tr.total td { font-weight: bold; background: #e8e8e8; }
        table.data tr.total td.left { text-align: left; }

        /* ---- Tanda tangan ---- */
        .ttd { width: 100%; border-collapse: collapse; margin-top: 26px; }
        .ttd td { vertical-align: top; padding: 2px; }
        .ttd .box { width: 260px; text-align: center; }
        .ttd .nama { font-weight: bold; text-decoration: underline; }

        .print-btn {
            background: #b45309; color: #fff; border: 0; padding: 9px 18px;
            border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer;
            margin-bottom: 14px;
        }
        @media print { .print-btn { display: none; } body { font-size: 9px; } }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">Cetak / Simpan sebagai PDF</button>

    {{-- ============================= KOP ============================= --}}
    <table class="kop">
        <tr>
            <td class="label">Nama Perusahaan</td><td class="sep">:</td>
            <td>{{ $p['nama'] }}</td>
            <td rowspan="6" class="judul">LAPORAN MUTASI HASIL HUTAN</td>
        </tr>
        <tr><td class="label">Alamat</td><td class="sep">:</td><td>{{ $p['alamat'] }}</td></tr>
        <tr><td class="label">Jenis Industri</td><td class="sep">:</td><td>{{ $p['jenis_industri'] }}</td></tr>
        <tr><td class="label">Lokasi Industri</td><td class="sep">:</td><td>{{ $p['lokasi_industri'] }}</td></tr>
        <tr><td class="label">Dinas Kehutanan</td><td class="sep">:</td><td>{{ $p['dinas_kehutanan'] }}</td></tr>
        <tr><td class="label">Propinsi</td><td class="sep">:</td><td>{{ $p['propinsi'] }}</td></tr>
    </table>

    <div class="section-title">A1. KAYU BULAT &mdash; Periode {{ $report['periode']['label'] }}</div>

    {{-- ========================= TABEL DATA ========================= --}}
    <table class="data">
        <colgroup>
            <col style="width:3%">
            <col style="width:6%"><col style="width:6%"><col style="width:7%">
            <col style="width:6%"><col style="width:6%"><col style="width:7%">
            <col style="width:6%"><col style="width:6%"><col style="width:7%">
            <col style="width:6%"><col style="width:7%">
            <col style="width:6%"><col style="width:6%"><col style="width:7%">
            <col style="width:8%">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="3">No</th>
                <th colspan="3" rowspan="2">Persediaan Akhir Bulan Lalu</th>
                <th colspan="3" rowspan="2">Perolehan Kayu Bulat</th>
                <th colspan="5">Penggunaan Kayu Bulat</th>
                <th colspan="3" rowspan="2">Persediaan Bulan Ini</th>
                <th rowspan="3">Keterangan</th>
            </tr>
            <tr>
                <th colspan="3">Diolah Sendiri</th>
                <th colspan="2">Penggunaan Lain</th>
            </tr>
            <tr>
                <th>Jenis Kayu</th><th>Jumlah Batang</th><th>Volume M3/Ton</th>
                <th>Jenis Kayu</th><th>Jumlah Batang</th><th>Volume M3/Ton</th>
                <th>Jenis Kayu</th><th>Jumlah Batang</th><th>Volume M3/Ton</th>
                <th>Jumlah Batang</th><th>Volume M3/Ton</th>
                <th>Jenis Kayu</th><th>Jumlah Batang</th><th>Volume M3/Ton</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)<th>{{ $i }}</th>@endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($report['rows'] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td class="left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ M::fmtBatang($row['awal_batang']) }}</td>
                    <td>{{ M::fmt($row['awal_volume']) }}</td>

                    <td class="left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ M::fmtBatang($row['masuk_batang']) }}</td>
                    <td>{{ M::fmt($row['masuk_volume']) }}</td>

                    <td class="left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ M::fmtBatang($row['diolah_batang']) }}</td>
                    <td>{{ M::fmt($row['diolah_volume']) }}</td>

                    <td>{{ M::fmtBatang($row['lain_batang']) }}</td>
                    <td>{{ M::fmt($row['lain_volume']) }}</td>

                    <td class="left">{{ $row['jenis_kayu'] }}</td>
                    <td>{{ M::fmtBatang($row['akhir_batang']) }}</td>
                    <td>{{ M::fmt($row['akhir_volume']) }}</td>

                    <td>{{ $row['keterangan'] }}</td>
                </tr>
            @empty
                <tr><td colspan="16" style="padding:14px">Tidak ada data pada periode ini.</td></tr>
            @endforelse

            <tr class="total">
                <td></td>
                <td class="left">Jumlah :</td>
                <td>{{ M::fmtBatang($report['totals']['awal_batang']) }}</td>
                <td>{{ M::fmt($report['totals']['awal_volume']) }}</td>
                <td class="left">Jumlah :</td>
                <td>{{ M::fmtBatang($report['totals']['masuk_batang']) }}</td>
                <td>{{ M::fmt($report['totals']['masuk_volume']) }}</td>
                <td class="left">Jumlah :</td>
                <td>{{ M::fmtBatang($report['totals']['diolah_batang']) }}</td>
                <td>{{ M::fmt($report['totals']['diolah_volume']) }}</td>
                <td>{{ M::fmtBatang($report['totals']['lain_batang']) }}</td>
                <td>{{ M::fmt($report['totals']['lain_volume']) }}</td>
                <td class="left">Jumlah :</td>
                <td>{{ M::fmtBatang($report['totals']['akhir_batang']) }}</td>
                <td>{{ M::fmt($report['totals']['akhir_volume']) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- ======================== TANDA TANGAN ======================== --}}
    <table class="ttd">
        <tr>
            <td></td>
            <td class="box">
                {{ $p['kota'] }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                {{ $p['jabatan'] }},
                <br><br><br><br>
                <span class="nama">{{ $p['pimpinan'] }}</span>
            </td>
        </tr>
    </table>

</body>
</html>
