<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip - {{ $mahasiswa->nim }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
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
            margin: 15px 0;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 100px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 3px;
        }
        .data-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-size: 9px;
        }
        .data-table td.center {
            text-align: center;
        }
        .summary {
            margin-top: 15px;
            width: 300px;
        }
        .summary td {
            padding: 3px 5px;
        }
        .footer {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .ttd-container {
            width: 100%;
            overflow: hidden;
        }
        .ttd {
            width: 200px;
            text-align: center;
            float: right;
        }
        .ttd .nama {
            margin-top: 50px;
            border-bottom: 1px solid #000;
        }
        .grade-table {
            width: 150px;
            float: left;
            font-size: 9px;
        }
        .grade-table td {
            padding: 1px 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>UNIVERSITAS SIAKAD</h2>
        <h3>{{ $mahasiswa->programStudi->fakultas->nama }}</h3>
        <p>Jl. Pendidikan No. 123, Kota Akademik | Telp: (021) 1234567</p>
    </div>

    <div class="title">TRANSKRIP AKADEMIK</div>

    <table class="info-table">
        <tr>
            <td>NIM</td>
            <td>: {{ $mahasiswa->nim }}</td>
            <td>Angkatan</td>
            <td>: {{ $mahasiswa->angkatan }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $mahasiswa->nama }}</td>
            <td>Status</td>
            <td>: {{ $mahasiswa->status }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $mahasiswa->programStudi->nama }} ({{ $mahasiswa->programStudi->jenjang }})</td>
            <td>Tempat/Tgl Lahir</td>
            <td>: {{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>: {{ $mahasiswa->programStudi->fakultas->nama }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="60">Kode</th>
                <th>Mata Kuliah</th>
                <th width="30">SKS</th>
                <th width="35">Huruf</th>
                <th width="35">Bobot</th>
                <th width="45">SKS x Bobot</th>
                <th width="70">Semester</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalBobot = 0; 
                $no = 1;
            @endphp
            @foreach($krs as $k)
            @php
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $bobot = $k->nilai->bobot;
                $sksBobot = $sks * $bobot;
                $totalBobot += $sksBobot;
            @endphp
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->kode }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                <td class="center">{{ $sks }}</td>
                <td class="center">{{ $k->nilai->huruf }}</td>
                <td class="center">{{ number_format($bobot, 2) }}</td>
                <td class="center">{{ number_format($sksBobot, 2) }}</td>
                <td class="center">{{ $k->jadwalKuliah->tahunAkademik->tahun }} {{ substr($k->jadwalKuliah->tahunAkademik->semester, 0, 1) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
                <td class="center" style="font-weight: bold;">{{ $totalSks }}</td>
                <td colspan="2"></td>
                <td class="center" style="font-weight: bold;">{{ number_format($totalBobot, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td><strong>Indeks Prestasi Kumulatif (IPK)</strong></td>
            <td>: <strong>{{ number_format($ipk, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Total SKS Lulus</td>
            <td>: {{ $totalSks }}</td>
        </tr>
        <tr>
            <td>Predikat Kelulusan</td>
            <td>: 
                @if($ipk >= 3.50) Dengan Pujian (Cum Laude)
                @elseif($ipk >= 3.00) Sangat Memuaskan
                @elseif($ipk >= 2.50) Memuaskan
                @else Cukup
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        <div class="ttd-container">
            <table class="grade-table">
                <tr><td colspan="3"><strong>Keterangan Nilai:</strong></td></tr>
                <tr><td>A</td><td>= 4.00</td><td>(80-100)</td></tr>
                <tr><td>B+</td><td>= 3.50</td><td>(75-79)</td></tr>
                <tr><td>B</td><td>= 3.00</td><td>(70-74)</td></tr>
                <tr><td>C+</td><td>= 2.50</td><td>(65-69)</td></tr>
                <tr><td>C</td><td>= 2.00</td><td>(60-64)</td></tr>
                <tr><td>D</td><td>= 1.00</td><td>(50-59)</td></tr>
                <tr><td>E</td><td>= 0.00</td><td>(0-49)</td></tr>
            </table>
            <div class="ttd">
                <p>{{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p>Dekan,</p>
                <p class="nama">{{ $mahasiswa->programStudi->fakultas->dekan ?? '_______________' }}</p>
                <p>NIP. _______________</p>
            </div>
        </div>
    </div>
</body>
</html>
