<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .summary { margin-bottom: 20px; }
        .summary-box { display: inline-block; padding: 10px 20px; background: #f5f5f5; margin-right: 10px; }
        .footer { margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEUANGAN</h2>
        <p>Sistem Informasi Akademik</p>
        <p>Periode: {{ $tahunAkademikAktif->tahun ?? '-' }} {{ $tahunAkademikAktif->semester ?? '' }}</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <p><strong>Ringkasan:</strong></p>
        <p>Total Tagihan: Rp {{ number_format($summary['total'], 0, ',', '.') }}</p>
        <p>Total Lunas: Rp {{ number_format($summary['lunas'], 0, ',', '.') }} ({{ $summary['count_lunas'] }} transaksi)</p>
        <p>Belum Lunas: Rp {{ number_format($summary['belum_lunas'], 0, ',', '.') }} ({{ $summary['count_belum'] }} transaksi)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="25">No</th>
                <th width="70">NIM</th>
                <th>Nama Mahasiswa</th>
                <th width="80">Jenis</th>
                <th width="90" class="text-right">Jumlah</th>
                <th width="60" class="text-center">Status</th>
                <th width="70" class="text-center">Tgl Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayaran as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $p->mahasiswa->nim }}</td>
                <td>{{ $p->mahasiswa->nama }}</td>
                <td>{{ $p->jenis }}</td>
                <td class="text-right">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                <td class="text-center">{{ $p->status }}</td>
                <td class="text-center">{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $pembayaran->count() }} transaksi</p>
    </div>
</body>
</html>
