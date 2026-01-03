<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Yudisium</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 12pt;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            margin: 0;
        }
        .info {
            margin: 15px 0;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
            font-size: 9pt;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        td.center {
            text-align: center;
        }
        td.right {
            text-align: right;
        }
        .summary {
            margin-top: 20px;
            font-size: 10pt;
        }
        .summary table {
            width: auto;
            margin-top: 10px;
        }
        .summary td {
            padding: 3px 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            padding-right: 30px;
        }
        .footer p {
            margin: 3px 0;
        }
        .signature {
            margin-top: 50px;
        }
        .cum-laude {
            color: #28a745;
            font-weight: bold;
        }
        .sangat-memuaskan {
            color: #007bff;
            font-weight: bold;
        }
        .memuaskan {
            color: #17a2b8;
        }
        .cukup {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper(setting('institution_name', 'UNIVERSITAS')) }}</h2>
        <h3>LAPORAN DATA YUDISIUM</h3>
    </div>

    <div class="info">
        <p>Tanggal Cetak: {{ now()->format('d F Y H:i') }}</p>
        <p>Jumlah Data: {{ $yudisium->count() }} mahasiswa</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">No. Yudisium</th>
                <th style="width: 80px;">NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Program Studi</th>
                <th style="width: 50px;">IPK</th>
                <th style="width: 50px;">SKS</th>
                <th style="width: 70px;">Masa Studi</th>
                <th style="width: 90px;">Predikat</th>
                <th style="width: 70px;">Tgl. Lulus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($yudisium as $index => $y)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $y->no_yudisium }}</td>
                <td>{{ $y->mahasiswa->nim ?? '-' }}</td>
                <td>{{ $y->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $y->mahasiswa->programStudi->nama ?? '-' }}</td>
                <td class="center">{{ number_format($y->ipk_akhir, 2) }}</td>
                <td class="center">{{ $y->total_sks_lulus }}</td>
                <td class="center">{{ $y->masa_studi_format }}</td>
                <td class="center 
                    @if($y->predikat == 'Cum Laude') cum-laude 
                    @elseif($y->predikat == 'Sangat Memuaskan') sangat-memuaskan 
                    @elseif($y->predikat == 'Memuaskan') memuaskan 
                    @else cukup @endif">
                    {{ $y->predikat }}
                </td>
                <td class="center">{{ $y->tanggal_lulus?->format('d/m/Y') ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="center">Tidak ada data yudisium</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h4>Ringkasan:</h4>
        <table>
            <tr>
                <td>Total Lulusan</td>
                <td>:</td>
                <td><strong>{{ $yudisium->count() }}</strong> mahasiswa</td>
            </tr>
            <tr>
                <td>Cum Laude</td>
                <td>:</td>
                <td>{{ $yudisium->where('predikat', 'Cum Laude')->count() }} mahasiswa</td>
            </tr>
            <tr>
                <td>Sangat Memuaskan</td>
                <td>:</td>
                <td>{{ $yudisium->where('predikat', 'Sangat Memuaskan')->count() }} mahasiswa</td>
            </tr>
            <tr>
                <td>Memuaskan</td>
                <td>:</td>
                <td>{{ $yudisium->where('predikat', 'Memuaskan')->count() }} mahasiswa</td>
            </tr>
            <tr>
                <td>Cukup</td>
                <td>:</td>
                <td>{{ $yudisium->where('predikat', 'Cukup')->count() }} mahasiswa</td>
            </tr>
            <tr>
                <td>Rata-rata IPK</td>
                <td>:</td>
                <td><strong>{{ number_format($yudisium->avg('ipk_akhir'), 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>{{ setting('institution_city', 'Kota') }}, {{ now()->format('d F Y') }}</p>
        <p>Mengetahui,</p>
        <div class="signature">
            <p>_________________________</p>
            <p>Kepala Bagian Akademik</p>
        </div>
    </div>
</body>
</html>
