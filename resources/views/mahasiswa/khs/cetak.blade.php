<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KHS - {{ $mahasiswa->nim }} - {{ $tahunAkademik->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
        }
        .header p {
            margin: 3px 0;
            font-size: 10pt;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .title p {
            margin: 5px 0;
            font-size: 11pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-table .label {
            width: 150px;
        }
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .nilai-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .nilai-table td.center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-box {
            border: 1px solid #000;
            padding: 10px 20px;
            text-align: center;
            width: 45%;
            display: inline-block;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .ttd {
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Universitas Contoh</h1>
        <h2>Fakultas Ilmu Komputer</h2>
        <p>Jl. Contoh No. 123, Kota Contoh 12345</p>
        <p>Telp: (021) 1234567 | Email: info@univ-contoh.ac.id</p>
    </div>

    <div class="title">
        <h3>Kartu Hasil Studi (KHS)</h3>
        <p>{{ $tahunAkademik->nama }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Mahasiswa</td>
            <td>: {{ $mahasiswa->nama }}</td>
            <td class="label">Program Studi</td>
            <td>: {{ $mahasiswa->programStudi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td>: {{ $mahasiswa->nim }}</td>
            <td class="label">Semester</td>
            <td>: {{ $mahasiswa->semester_aktif ?? $mahasiswa->semester ?? '-' }}</td>
        </tr>
    </table>

    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode MK</th>
                <th width="40%">Nama Mata Kuliah</th>
                <th width="10%">SKS</th>
                <th width="12%">Nilai Angka</th>
                <th width="10%">Nilai Huruf</th>
                <th width="11%">Bobot × SKS</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1;
                $totalMutu = 0;
            @endphp
            @foreach($krsList as $krs)
            @php
                $mataKuliah = $krs->jadwalKuliah->mataKuliah ?? null;
                $nilai = $krs->nilai;
                $huruf = $nilai->huruf ?? null;
                $bobot = $nilai->bobot ?? 0;
                $sks = $mataKuliah->sks ?? 0;
                $mutu = $sks * $bobot;
                $totalMutu += $mutu;
            @endphp
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td class="center">{{ $mataKuliah->kode ?? '-' }}</td>
                <td>{{ $mataKuliah->nama ?? '-' }}</td>
                <td class="center">{{ $sks }}</td>
                <td class="center">{{ $nilai->nilai_akhir ?? '-' }}</td>
                <td class="center">{{ $huruf ?? '-' }}</td>
                <td class="center">{{ number_format($mutu, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Jumlah</th>
                <th class="center">{{ $stats['sks_semester'] }}</th>
                <th colspan="2"></th>
                <th class="center">{{ number_format($totalMutu, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <table width="100%">
            <tr>
                <td width="50%">
                    <table border="1" cellpadding="10" style="border-collapse: collapse;">
                        <tr>
                            <td><strong>IP Semester (IPS)</strong></td>
                            <td style="text-align: center; font-size: 14pt;"><strong>{{ number_format($stats['ips'], 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>SKS Semester</strong></td>
                            <td style="text-align: center;">{{ $stats['sks_semester'] }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table border="1" cellpadding="10" style="border-collapse: collapse;">
                        <tr>
                            <td><strong>IP Kumulatif (IPK)</strong></td>
                            <td style="text-align: center; font-size: 14pt;"><strong>{{ number_format($stats['ipk'], 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>SKS Kumulatif</strong></td>
                            <td style="text-align: center;">{{ $stats['sks_kumulatif'] }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Kota Contoh, {{ now()->format('d F Y') }}</p>
        <p>Ketua Program Studi</p>
        <div class="ttd">
            <p><u><strong>Nama Kaprodi, M.Kom</strong></u></p>
            <p>NIDN. 0123456789</p>
        </div>
    </div>
</body>
</html>
