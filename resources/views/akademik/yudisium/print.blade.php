<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Keterangan Yudisium - {{ $yudisium->mahasiswa->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
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
            margin: 30px 0;
        }
        .title h3 {
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .content {
            margin: 20px 0;
        }
        table.data {
            width: 100%;
            margin: 15px 0;
        }
        table.data td {
            padding: 3px 0;
            vertical-align: top;
        }
        table.data td:first-child {
            width: 150px;
        }
        table.data td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        .predikat {
            font-weight: bold;
            text-transform: uppercase;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
            padding-right: 50px;
        }
        .signature p {
            margin: 5px 0;
        }
        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
        .footer {
            margin-top: 30px;
            font-size: 10pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
        <h2>{{ strtoupper(setting('institution_name', 'UNIVERSITAS')) }}</h2>
        <h3>{{ strtoupper($yudisium->mahasiswa->programStudi->fakultas->nama ?? 'FAKULTAS') }}</h3>
        <h3>PROGRAM STUDI {{ strtoupper($yudisium->mahasiswa->programStudi->nama ?? '-') }}</h3>
        <p>{{ setting('institution_address', 'Alamat Kampus') }}</p>
        <p>Telepon: {{ setting('institution_phone', '-') }} | Email: {{ setting('institution_email', '-') }}</p>
    </div>

    <div class="title">
        <h3>SURAT KETERANGAN YUDISIUM</h3>
        <p>Nomor: {{ $yudisium->no_yudisium }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, menerangkan bahwa:</p>

        <table class="data">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $yudisium->mahasiswa->nama }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $yudisium->mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $yudisium->mahasiswa->tempat_lahir ?? '-' }}, {{ $yudisium->mahasiswa->tanggal_lahir?->format('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $yudisium->mahasiswa->programStudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>:</td>
                <td>{{ $yudisium->mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi/TA</td>
                <td>:</td>
                <td>{{ $yudisium->pendaftaranWisuda->judul_skripsi ?? '-' }}</td>
            </tr>
        </table>

        <p>Telah dinyatakan <strong>LULUS</strong> dalam sidang yudisium yang dilaksanakan pada tanggal 
            <strong>{{ $yudisium->tanggal_yudisium?->format('d F Y') }}</strong> dengan keterangan sebagai berikut:</p>

        <table class="data">
            <tr>
                <td>Indeks Prestasi Kumulatif (IPK)</td>
                <td>:</td>
                <td><strong>{{ number_format($yudisium->ipk_akhir, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Total SKS Lulus</td>
                <td>:</td>
                <td>{{ $yudisium->total_sks_lulus }} SKS</td>
            </tr>
            <tr>
                <td>Masa Studi</td>
                <td>:</td>
                <td>{{ $yudisium->masa_studi_format }}</td>
            </tr>
            <tr>
                <td>Predikat Kelulusan</td>
                <td>:</td>
                <td class="predikat">{{ $yudisium->predikat }}</td>
            </tr>
            @if($yudisium->no_ijazah)
            <tr>
                <td>Nomor Ijazah</td>
                <td>:</td>
                <td>{{ $yudisium->no_ijazah }}</td>
            </tr>
            @endif
            @if($yudisium->no_transkrip)
            <tr>
                <td>Nomor Transkrip</td>
                <td>:</td>
                <td>{{ $yudisium->no_transkrip }}</td>
            </tr>
            @endif
        </table>

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <p>{{ setting('institution_city', 'Kota') }}, {{ now()->format('d F Y') }}</p>
        <p>Dekan,</p>
        <p class="signature-name">{{ setting('dean_name', '............................') }}</p>
        <p>NIP. {{ setting('dean_nip', '............................') }}</p>
    </div>

    <div class="footer">
        <p>* Surat keterangan ini berlaku selama menunggu penerbitan ijazah dan transkrip asli</p>
    </div>
</body>
</html>
