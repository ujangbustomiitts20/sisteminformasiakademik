<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Cuti Akademik - {{ $cuti->mahasiswa->nim }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 16pt;
        }
        
        .header h3 {
            margin: 5px 0;
            font-size: 14pt;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 10pt;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .title {
            text-align: center;
            margin: 30px 0;
        }
        
        .title h3 {
            margin: 0;
            text-decoration: underline;
            font-size: 14pt;
        }
        
        .title p {
            margin: 5px 0;
            font-size: 11pt;
        }
        
        .content {
            text-align: justify;
            margin: 20px 0;
        }
        
        .data-table {
            margin: 15px 0 15px 30px;
        }
        
        .data-table tr td:first-child {
            width: 150px;
            vertical-align: top;
        }
        
        .data-table tr td:nth-child(2) {
            width: 10px;
            vertical-align: top;
        }
        
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-space {
            height: 80px;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 10pt;
            color: #666;
            text-align: center;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Cetak Surat
    </button>

    @php
        $pejabatDekan = \App\Models\PejabatPenandatangan::where('kode', 'dekan_' . strtolower($cuti->mahasiswa->programStudi->fakultas->kode ?? 'fti'))
            ->orWhere('kode', 'dekan_fti')
            ->where('aktif', true)
            ->first();
        $pejabatKaprodi = \App\Models\PejabatPenandatangan::where('kode', 'kaprodi_' . strtolower($cuti->mahasiswa->programStudi->kode ?? 'ti'))
            ->where('aktif', true)
            ->first();
    @endphp

    <div class="header">
        <h2>{{ strtoupper(setting('institution_name', 'UNIVERSITAS')) }}</h2>
        <h3>FAKULTAS {{ strtoupper($cuti->mahasiswa->programStudi->fakultas->nama ?? 'TEKNOLOGI INFORMASI') }}</h3>
        <p>{{ setting('institution_address', 'Alamat Institusi') }}</p>
        <p>Telp: {{ setting('contact_phone', '-') }} | Email: {{ setting('contact_email', '-') }}</p>
    </div>

    <div class="title">
        <h3>SURAT KETERANGAN CUTI AKADEMIK</h3>
        <p>Nomor: {{ $cuti->nomor_surat ?? '-' }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, {{ $pejabatDekan?->jabatan ?? ('Dekan ' . ($cuti->mahasiswa->programStudi->fakultas->nama ?? 'Fakultas')) }} {{ setting('institution_name', 'Universitas') }}, menerangkan bahwa:</p>
        
        <table class="data-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $cuti->mahasiswa->nama }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $cuti->mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $cuti->mahasiswa->programStudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $cuti->mahasiswa->semester ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tahun Akademik</td>
                <td>:</td>
                <td>{{ $cuti->tahunAkademik->nama ?? '-' }}</td>
            </tr>
        </table>

        <p>Telah disetujui untuk mengambil <strong>Cuti Akademik</strong> dengan rincian sebagai berikut:</p>

        <table class="data-table">
            <tr>
                <td>Alasan Cuti</td>
                <td>:</td>
                <td>{{ $cuti->alasan }}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>{{ $cuti->keterangan }}</td>
            </tr>
            <tr>
                <td>Lama Cuti</td>
                <td>:</td>
                <td>{{ $cuti->jumlah_semester }} Semester</td>
            </tr>
            <tr>
                <td>Periode Cuti</td>
                <td>:</td>
                <td>{{ $cuti->tanggal_mulai->format('d F Y') }} s/d {{ $cuti->tanggal_selesai->format('d F Y') }}</td>
            </tr>
        </table>

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p>{{ $pejabatKaprodi?->jabatan ?? 'Ketua Program Studi' }}</p>
            <div class="signature-space"></div>
            <p><u>{{ $pejabatKaprodi?->nama_lengkap ?? $cuti->disetujuiKaprodiOleh->name ?? '.........................' }}</u></p>
            <p>NIP. {{ $pejabatKaprodi?->nip ?? $cuti->disetujuiKaprodiOleh->nip ?? '.........................' }}</p>
        </div>
        <div class="signature-box">
            <p>{{ setting('kota_institusi', 'Jakarta') }}, {{ $cuti->tanggal_persetujuan_dekan?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</p>
            <p>{{ $pejabatDekan?->jabatan ?? 'Dekan' }}</p>
            <div class="signature-space"></div>
            <p><u>{{ $pejabatDekan?->nama_lengkap ?? $cuti->disetujuiDekanOleh->name ?? '.........................' }}</u></p>
            <p>NIP. {{ $pejabatDekan?->nip ?? $cuti->disetujuiDekanOleh->nip ?? '.........................' }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Surat ini dibuat secara otomatis oleh Sistem Informasi Akademik (SIAKAD)</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
