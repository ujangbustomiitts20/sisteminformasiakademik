<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        .title { text-align: center; margin: 20px 0; }
        .title h2 { font-size: 14px; margin: 0; }
        .info { margin-bottom: 15px; }
        .info p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #4CAF50; color: white; padding: 8px; text-align: left; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #E8F5E9 !important; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS SIAKAD</h1>
        <p>Jl. Pendidikan No. 123, Kota Akademik 12345</p>
    </div>

    <div class="title">
        <h2>LAPORAN PENDAPATAN</h2>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> 
            @if($request->tanggal_mulai && $request->tanggal_selesai)
                {{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
        </p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">No. Transaksi</th>
                <th width="10%">Tanggal</th>
                <th width="10%">NIM</th>
                <th width="20%">Nama</th>
                <th width="15%">Program Studi</th>
                <th width="12%">Metode</th>
                <th width="16%" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $trx->no_transaksi }}</td>
                <td>{{ $trx->tanggal_bayar->format('d/m/Y') }}</td>
                <td>{{ $trx->mahasiswa->nim ?? '-' }}</td>
                <td>{{ $trx->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $trx->mahasiswa->programStudi->nama ?? '-' }}</td>
                <td>{{ $trx->metode_pembayaran }}</td>
                <td class="text-right">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" class="text-right"><strong>TOTAL PENDAPATAN</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem SIAKAD</p>
    </div>
</body>
</html>
