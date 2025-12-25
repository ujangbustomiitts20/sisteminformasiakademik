<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KRS - {{ $mahasiswa->nim }}</title>
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

    <div class="title">KARTU RENCANA STUDI (KRS)</div>

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
                <th width="50">Kelas</th>
                <th>Dosen Pengampu</th>
                <th width="80">Hari/Jam</th>
                <th width="60">Ruang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($krs as $index => $k)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->kode }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                <td class="center">{{ $k->jadwalKuliah->mataKuliah->sks }}</td>
                <td class="center">{{ $k->jadwalKuliah->kelas }}</td>
                <td>{{ $k->jadwalKuliah->dosen->nama }}</td>
                <td class="center">{{ substr($k->jadwalKuliah->hari, 0, 3) }}, {{ \Carbon\Carbon::parse($k->jadwalKuliah->jam_mulai)->format('H:i') }}</td>
                <td class="center">{{ $k->jadwalKuliah->ruangan->kode }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Total SKS</td>
                <td class="center" style="font-weight: bold;">{{ $totalSks }}</td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <p>{{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Dosen Pembimbing Akademik</p>
            <p class="nama">{{ $mahasiswa->dosenWali->nama ?? '_______________' }}</p>
            <p>NIDN. {{ $mahasiswa->dosenWali->nidn ?? '-' }}</p>
        </div>
    </div>
</body>
</html>
