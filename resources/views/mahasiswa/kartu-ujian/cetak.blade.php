<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Ujian - {{ $kartuUjian->nomor_kartu }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
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
            font-size: 14pt;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 12pt;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h1 {
            font-size: 16pt;
            margin: 0;
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
            padding: 3px 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 120px;
        }
        .mk-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10pt;
        }
        .mk-table th, .mk-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .mk-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .mk-table td.center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
        }
        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature .line {
            border-bottom: 1px solid #000;
            margin: 60px 0 5px;
        }
        .note {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #000;
            font-size: 9pt;
        }
        .note h4 {
            margin: 0 0 5px;
        }
        .photo-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            float: right;
            text-align: center;
            line-height: 4cm;
            font-size: 9pt;
            color: #666;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    @php
        $pejabat = \App\Models\PejabatPenandatangan::where('kode', 'kepala_baak')
            ->where('aktif', true)
            ->first();
    @endphp
    <div class="header">
        <h2>{{ strtoupper(setting('institution_name', 'UNIVERSITAS')) }}</h2>
        <h3>{{ $kartuUjian->mahasiswa->programStudi->fakultas->nama ?? 'FAKULTAS' }}</h3>
        <p>{{ setting('institution_address', 'Alamat Institusi') }} - Telp. {{ setting('contact_phone', '-') }}</p>
    </div>

    <div class="title">
        <h1>KARTU PESERTA UJIAN</h1>
        <p>{{ $kartuUjian->periodeUjian->nama }}</p>
        <p>Tahun Akademik {{ $kartuUjian->periodeUjian->tahunAkademik->nama ?? '-' }}</p>
    </div>

    <div class="photo-box">
        Pas Foto<br>3x4
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
            <td>Program Studi</td>
            <td>: {{ $kartuUjian->mahasiswa->programStudi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>: {{ $kartuUjian->mahasiswa->semester ?? '-' }}</td>
        </tr>
    </table>

    <div class="clear"></div>

    <table class="mk-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Mata Kuliah</th>
                <th width="40">SKS</th>
                <th width="100">Tanggal</th>
                <th width="80">Waktu</th>
                <th width="60">Ruang</th>
                <th width="60">Paraf</th>
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
                <td colspan="7" class="center">Tidak ada jadwal ujian</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>{{ setting('kota_institusi', 'Jakarta') }}, {{ now()->translatedFormat('d F Y') }}</p>
            <p>{{ $pejabat?->jabatan ?? 'Kepala Bagian Akademik' }}</p>
            <div class="line"></div>
            <p>({{ $pejabat?->nama_lengkap ?? '.............................' }})</p>
            @if($pejabat?->nip)
            <p>NIP. {{ $pejabat->nip }}</p>
            @endif
        </div>
    </div>

    <div class="clear"></div>

    <div class="note">
        <h4>Perhatian:</h4>
        <ol style="margin: 0; padding-left: 20px;">
            <li>Kartu ini wajib dibawa saat mengikuti ujian</li>
            <li>Mahasiswa wajib menunjukkan kartu ini kepada pengawas</li>
            <li>Pengawas wajib membubuhkan paraf pada kolom yang tersedia</li>
            <li>Kartu yang rusak atau hilang segera melapor ke bagian akademik</li>
            <li>Kartu ini tidak dapat dipindahtangankan</li>
        </ol>
    </div>

    <p style="text-align: center; font-size: 9pt; margin-top: 20px; color: #666;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </p>
</body>
</html>
