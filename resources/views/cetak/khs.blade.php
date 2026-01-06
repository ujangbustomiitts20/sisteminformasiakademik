<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KHS - {{ $mahasiswa->nim }}</title>
    @php
        $pejabat = \App\Models\PejabatPenandatangan::where('kode', 'kaprodi_' . strtolower($mahasiswa->programStudi->kode ?? 'ti'))
            ->orWhere('kode', 'kepala_baak')
            ->where('aktif', true)
            ->first();
        
        $logoPath = setting('institution_logo');
        $logoFullPath = $logoPath ? storage_path('app/public/' . $logoPath) : null;
        $logoBase64 = null;
        if ($logoFullPath && file_exists($logoFullPath)) {
            $logoData = file_get_contents($logoFullPath);
            $logoMime = mime_content_type($logoFullPath);
            $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
        }
    @endphp
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
        .header table {
            width: 100%;
        }
        .header .logo {
            width: 80px;
            vertical-align: middle;
        }
        .header .logo img {
            max-width: 70px;
            max-height: 70px;
        }
        .header .institusi {
            text-align: center;
            vertical-align: middle;
        }
        .header h1 {
            margin: 0;
            font-size: 12px;
            font-weight: normal;
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
            width: 220px;
            text-align: center;
        }
        .ttd .nama {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
        .ttd .nip {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @endif
                </td>
                <td class="institusi">
                    @if(setting('institution_yayasan'))
                    <h1>{{ setting('institution_yayasan') }}</h1>
                    @endif
                    <h2>{{ strtoupper(setting('institution_name', 'UNIVERSITAS')) }}</h2>
                    <h3>{{ $mahasiswa->programStudi->fakultas->nama }}</h3>
                    <p>{{ setting('institution_address', 'Alamat Institusi') }}</p>
                    <p>
                        @if(setting('contact_phone'))Telp: {{ setting('contact_phone') }}@endif
                        @if(setting('contact_fax')) | Fax: {{ setting('contact_fax') }}@endif
                        @if(setting('contact_email')) | Email: {{ setting('contact_email') }}@endif
                    </p>
                </td>
                <td class="logo"></td>
            </tr>
        </table>
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
            <p>{{ setting('kota_institusi', 'Jakarta') }}, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>{{ $pejabat?->jabatan ?? 'Ketua Program Studi' }}</p>
            @if($pejabat?->tanda_tangan)
                <img src="{{ $pejabat->tanda_tangan_url }}" alt="TTD" style="height: 50px; margin: 5px 0;">
            @endif
            <p class="nama">{{ $pejabat?->nama_lengkap ?? $mahasiswa->programStudi->kaprodi ?? '_______________' }}</p>
            @if($pejabat?->nip)
            <p class="nip">NIP. {{ $pejabat->nip }}</p>
            @endif
        </div>
    </div>
</body>
</html>
