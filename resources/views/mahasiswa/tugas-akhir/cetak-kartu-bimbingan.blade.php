<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Bimbingan - {{ $tugasAkhir->mahasiswa->nama ?? 'Mahasiswa' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
        }
        .bimbingan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .bimbingan-table th,
        .bimbingan-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .bimbingan-table th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .bimbingan-table td.center {
            text-align: center;
        }
        .signature {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 10px;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('institution_name', 'UNIVERSITAS') }}</h1>
        <h2>{{ $tugasAkhir->mahasiswa->programStudi->fakultas->nama ?? 'FAKULTAS' }}</h2>
        <h2>{{ $tugasAkhir->mahasiswa->programStudi->nama ?? 'PROGRAM STUDI' }}</h2>
        <p>{{ setting('institution_address', 'Alamat Kampus') }}</p>
    </div>

    <div class="title">
        <h3>Kartu Bimbingan Tugas Akhir/Skripsi</h3>
    </div>

    <table class="info-table">
        <tr>
            <td>NIM</td>
            <td>: {{ $tugasAkhir->mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama Mahasiswa</td>
            <td>: {{ $tugasAkhir->mahasiswa->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $tugasAkhir->mahasiswa->programStudi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Judul Tugas Akhir</td>
            <td>: {{ $tugasAkhir->judul }}</td>
        </tr>
        <tr>
            <td>Pembimbing 1</td>
            <td>: {{ $tugasAkhir->pembimbing1->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pembimbing 2</td>
            <td>: {{ $tugasAkhir->pembimbing2->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="bimbingan-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="30%">Materi Bimbingan</th>
                <th width="30%">Hasil/Catatan</th>
                <th width="10%">Progress</th>
                <th width="13%">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tugasAkhir->bimbingan->where('status', 'selesai') as $key => $bimbingan)
            <tr>
                <td class="center">{{ $key + 1 }}</td>
                <td class="center">{{ $bimbingan->tanggal->format('d/m/Y') }}</td>
                <td>{{ $bimbingan->materi_bimbingan }}</td>
                <td>{{ $bimbingan->hasil_bimbingan ?? $bimbingan->catatan_dosen ?? '-' }}</td>
                <td class="center">{{ $bimbingan->persentase_progress ?? '-' }}%</td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center">Belum ada data bimbingan</td>
            </tr>
            @endforelse
            @for($i = $tugasAkhir->bimbingan->where('status', 'selesai')->count(); $i < 10; $i++)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="signature">
        <table class="signature-table">
            <tr>
                <td>
                    <p>Pembimbing 1</p>
                    <div class="signature-line"></div>
                    <p>{{ $tugasAkhir->pembimbing1->nama ?? '............................' }}</p>
                    <p>NIP. {{ $tugasAkhir->pembimbing1->nip ?? '............................' }}</p>
                </td>
                <td>
                    <p>Pembimbing 2</p>
                    <div class="signature-line"></div>
                    <p>{{ $tugasAkhir->pembimbing2->nama ?? '............................' }}</p>
                    <p>NIP. {{ $tugasAkhir->pembimbing2->nip ?? '............................' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
