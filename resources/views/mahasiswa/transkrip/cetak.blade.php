<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai - {{ $mahasiswa->nim }}</title>
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
        .semester-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        .summary-table {
            width: 50%;
            margin: 0 auto;
        }
        .summary-table td {
            padding: 3px 10px;
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
        <h3>Transkrip Akademik</h3>
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
            <td class="label">Fakultas</td>
            <td>: {{ $mahasiswa->programStudi->fakultas->nama ?? 'Ilmu Komputer' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat/Tgl Lahir</td>
            <td>: {{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d F Y') : '-' }}</td>
            <td class="label">Angkatan</td>
            <td>: {{ $mahasiswa->angkatan }}</td>
        </tr>
    </table>

    <table class="nilai-table">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="12%">Kode MK</th>
                <th width="40%">Nama Mata Kuliah</th>
                <th width="10%">SKS</th>
                <th width="10%">Nilai</th>
                <th width="10%">Bobot</th>
                <th width="10%">Mutu</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($nilaiPerSemester as $tahunAkademikId => $nilaiList)
            @php
                $ta = $nilaiList->first()->tahunAkademik;
            @endphp
            <tr class="semester-header">
                <td colspan="7">{{ $ta->nama ?? 'Semester ' . $tahunAkademikId }}</td>
            </tr>
            @foreach($nilaiList as $nilai)
            @php
                $bobot = match(strtoupper($nilai->nilai_huruf)) {
                    'A' => 4.0, 'A-' => 3.75, 'B+' => 3.5, 'B' => 3.0, 'B-' => 2.75,
                    'C+' => 2.5, 'C' => 2.0, 'C-' => 1.75, 'D+' => 1.5, 'D' => 1.0,
                    default => 0,
                };
                $sks = $nilai->mataKuliah->sks ?? 0;
                $mutu = $sks * $bobot;
            @endphp
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td class="center">{{ $nilai->mataKuliah->kode ?? '-' }}</td>
                <td>{{ $nilai->mataKuliah->nama ?? '-' }}</td>
                <td class="center">{{ $sks }}</td>
                <td class="center">{{ $nilai->nilai_huruf }}</td>
                <td class="center">{{ number_format($bobot, 2) }}</td>
                <td class="center">{{ number_format($mutu, 2) }}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td><strong>Total SKS Lulus</strong></td>
                <td>: {{ $stats['total_sks_lulus'] }} SKS</td>
            </tr>
            <tr>
                <td><strong>Indeks Prestasi Kumulatif (IPK)</strong></td>
                <td>: {{ number_format($stats['ipk'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Predikat Kelulusan</strong></td>
                <td>: {{ $stats['predikat'] }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Kota Contoh, {{ now()->format('d F Y') }}</p>
        <p>Dekan Fakultas Ilmu Komputer</p>
        <div class="ttd">
            <p><u><strong>Dr. Nama Dekan, M.Kom</strong></u></p>
            <p>NIP. 19700101 199903 1 001</p>
        </div>
    </div>
</body>
</html>
