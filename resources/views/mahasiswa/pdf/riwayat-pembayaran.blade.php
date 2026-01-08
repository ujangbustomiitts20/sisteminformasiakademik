<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Pembayaran - {{ $mahasiswa->nim }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 16px; margin-bottom: 5px; }
        .header h2 { font-size: 14px; font-weight: normal; margin-bottom: 5px; }
        .header p { font-size: 9px; color: #666; }
        .info-box { background: #f5f5f5; padding: 10px; margin-bottom: 15px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 5px; }
        .info-box td:first-child { width: 120px; color: #666; }
        .info-box td:last-child { font-weight: bold; }
        table.transaksi { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.transaksi th, table.transaksi td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.transaksi th { background: #22c55e; color: white; font-size: 9px; }
        table.transaksi td { font-size: 9px; }
        table.transaksi tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; font-size: 8px; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
        .total-box { background: #dcfce7; padding: 15px; margin-top: 15px; text-align: right; }
        .total-box h3 { color: #16a34a; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('institution_name', 'UNIVERSITAS') }}</h1>
        <h2>RIWAYAT PEMBAYARAN MAHASISWA</h2>
        <p>{{ setting('institution_address', '') }}</p>
    </div>
    
    <div class="info-box">
        <table>
            <tr>
                <td>NIM</td>
                <td>{{ $mahasiswa->nim }}</td>
                <td>Program Studi</td>
                <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>{{ $mahasiswa->nama }}</td>
                <td>Angkatan</td>
                <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
            </tr>
        </table>
    </div>
    
    <table class="transaksi">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Tanggal</th>
                <th>No. Transaksi</th>
                <th>Tagihan</th>
                <th>Metode</th>
                <th class="text-right">Jumlah Bayar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $t)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $t->tanggal_bayar->format('d/m/Y H:i') }}</td>
                <td>{{ $t->no_transaksi }}</td>
                <td>
                    {{ $t->tagihan->jenis_tagihan ?? '-' }}
                    <br><small style="color: #666;">{{ $t->tagihan->no_tagihan ?? '' }}</small>
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $t->metode_pembayaran ?? '-')) }}</td>
                <td class="text-right" style="color: #16a34a; font-weight: bold;">
                    Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                </td>
                <td>{{ $t->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada transaksi pembayaran</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="total-box">
        <h3>Total Pembayaran: Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h3>
        <p style="margin-top: 5px; color: #666;">Jumlah Transaksi: {{ $transaksi->count() }} transaksi</p>
    </div>
    
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }} WIB</p>
        <p>Dokumen ini digenerate secara otomatis oleh sistem {{ setting('app_name', 'NADI ITTS') }}</p>
    </div>
</body>
</html>
