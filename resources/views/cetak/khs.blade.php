<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KHS - {{ $mahasiswa->nim }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 120px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        .data-table th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .data-table td.center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            width: 250px;
        }
        .summary td {
            padding: 3px 5px;
        }
        .footer {
            margin-top: 30px;
        }
        .ttd {
            float: right;
            width: 200px;
            text-align: center;
        }
        .ttd .nama {
            margin-top: 60px;
            border-bottom: 1px solid #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>UNIVERSITAS SIAKAD</h2>
        <h3>{{ $mahasiswa->programStudi->fakultas->nama }}</h3>
        <p>Jl. Pendidikan No. 123, Kota Akademik | Telp: (021) 1234567</p>
    </div>

    <div class="title">KARTU HASIL STUDI (KHS)</div>

    <table class="info-table">
        <tr>
            <td>NIM</td>
            <td>: {{ $mahasiswa->nim }}</td>
            <td>Semester</td>
            <td>: {{ $mahasiswa->semester_aktif }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $mahasiswa->nama }}</td>
            <td>Tahun Akademik</td>
            <td>: {{ $tahunAkademik->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $mahasiswa->programStudi->nama }}</td>
            <td>Dosen Wali</td>
            <td>: {{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="70">Kode</th>
                <th>Mata Kuliah</th>
                <th width="40">SKS</th>
                <th width="60">Nilai Angka</th>
                <th width="50">Huruf</th>
                <th width="50">Bobot</th>
                <th width="60">SKS x Bobot</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBobot = 0; $totalSksTerisi = 0; @endphp
            @foreach($krs as $index => $k)
            @php
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $bobot = $k->nilai?->bobot ?? 0;
                $sksBobot = $sks * $bobot;
                $totalBobot += $sksBobot;
                if ($k->nilai) $totalSksTerisi += $sks;
            @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->kode }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                <td class="center">{{ $sks }}</td>
                <td class="center">{{ $k->nilai?->nilai_akhir ?? '-' }}</td>
                <td class="center">{{ $k->nilai?->huruf ?? '-' }}</td>
                <td class="center">{{ $k->nilai ? number_format($bobot, 2) : '-' }}</td>
                <td class="center">{{ $k->nilai ? number_format($sksBobot, 2) : '-' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
                <td class="center" style="font-weight: bold;">{{ $totalSks }}</td>
                <td colspan="3"></td>
                <td class="center" style="font-weight: bold;">{{ number_format($totalBobot, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td><strong>Indeks Prestasi Semester (IPS)</strong></td>
            <td>: <strong>{{ number_format($ips, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Total SKS Diambil</td>
            <td>: {{ $totalSks }}</td>
        </tr>
        <tr>
            <td>Total SKS Lulus</td>
            <td>: {{ $totalSksTerisi }}</td>
        </tr>
    </table>

    <div class="footer">
        <div class="ttd">
            <p>{{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Ketua Program Studi</p>
            <p class="nama">{{ $mahasiswa->programStudi->kaprodi ?? '_______________' }}</p>
            <p>NIP. _______________</p>
        </div>
    </div>
</body>
</html>
