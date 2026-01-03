<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Ujian Batch - {{ $periodeUjian->nama }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 13pt;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 11pt;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .title {
            text-align: center;
            margin: 15px 0;
        }
        .title h1 {
            font-size: 14pt;
            margin: 0;
            text-decoration: underline;
        }
        .title p {
            margin: 3px 0;
            font-size: 10pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
            font-size: 10pt;
        }
        .info-table td:first-child {
            width: 100px;
        }
        .mk-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }
        .mk-table th, .mk-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        .mk-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .mk-table td.center {
            text-align: center;
        }
        .signature {
            float: right;
            width: 180px;
            text-align: center;
            font-size: 10pt;
        }
        .signature .line {
            border-bottom: 1px solid #000;
            margin: 50px 0 5px;
        }
        .photo-box {
            width: 2.5cm;
            height: 3.5cm;
            border: 1px solid #000;
            float: right;
            text-align: center;
            line-height: 3.5cm;
            font-size: 8pt;
            color: #666;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    @foreach($kartuList as $kartuUjian)
    <div class="header">
        <h2>UNIVERSITAS CONTOH</h2>
        <h3>{{ $kartuUjian->mahasiswa->programStudi->fakultas->nama ?? 'FAKULTAS' }}</h3>
        <p>Jl. Contoh No. 123, Kota</p>
    </div>

    <div class="title">
        <h1>KARTU PESERTA UJIAN</h1>
        <p>{{ $kartuUjian->periodeUjian->nama }} - {{ $kartuUjian->periodeUjian->tahunAkademik->nama ?? '-' }}</p>
    </div>

    <div class="photo-box">
        Foto 3x4
    </div>

    <table class="info-table">
        <tr>
            <td>No. Kartu</td>
            <td>: <strong>{{ $kartuUjian->nomor_kartu }}</strong></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>: {{ $kartuUjian->mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: <strong>{{ $kartuUjian->mahasiswa->nama }}</strong></td>
        </tr>
        <tr>
            <td>Prodi</td>
            <td>: {{ $kartuUjian->mahasiswa->programStudi->nama ?? '-' }}</td>
        </tr>
    </table>

    <div class="clear"></div>

    <table class="mk-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th>Mata Kuliah</th>
                <th width="35">SKS</th>
                <th width="80">Tanggal</th>
                <th width="60">Waktu</th>
                <th width="50">Ruang</th>
                <th width="50">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kartuUjian->detailKartuUjian as $i => $detail)
            @if($detail->eligible)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $detail->jadwalUjian->mataKuliah->nama ?? '-' }}</td>
                <td class="center">{{ $detail->jadwalUjian->mataKuliah->sks ?? '-' }}</td>
                <td class="center">{{ $detail->jadwalUjian->tanggal?->format('d/m/Y') ?? '-' }}</td>
                <td class="center">{{ $detail->jadwalUjian->waktu_mulai ?? '-' }}</td>
                <td class="center">{{ $detail->jadwalUjian->ruangan->nama ?? '-' }}</td>
                <td></td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="7" class="center">-</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <p>{{ now()->format('d F Y') }}</p>
        <p>Ka. Bag. Akademik</p>
        <div class="line"></div>
        <p>(....................)</p>
    </div>

    <div class="clear"></div>

    @if(!$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>
