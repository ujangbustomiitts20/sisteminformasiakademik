<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tunggakan</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        .title { text-align: center; margin: 20px 0; }
        .title h2 { font-size: 14px; margin: 0; color: #D32F2F; }
        .info { margin-bottom: 15px; }
        .info p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #FF9800; color: white; padding: 8px; text-align: left; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #D32F2F; }
        .total-row { background: #FFEBEE !important; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS SIAKAD</h1>
        <p>Jl. Pendidikan No. 123, Kota Akademik 12345</p>
    </div>

    <div class="title">
        <h2>LAPORAN TUNGGAKAN</h2>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Total Mahasiswa:</strong> {{ $tagihan->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">No. Tagihan</th>
                <th width="10%">NIM</th>
                <th width="18%">Nama</th>
                <th width="12%">Prodi</th>
                <th width="10%">Tahun Akad</th>
                <th width="12%" class="text-right">Total Tagihan</th>
                <th width="12%" class="text-right">Sudah Bayar</th>
                <th width="12%" class="text-right">Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->no_tagihan }}</td>
                <td>{{ $item->mahasiswa->nim ?? '-' }}</td>
                <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $item->mahasiswa->programStudi->nama ?? '-' }}</td>
                <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</td>
                <td class="text-right text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="8" class="text-right"><strong>TOTAL TUNGGAKAN</strong></td>
                <td class="text-right text-danger"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem SIAKAD</p>
    </div>
</body>
</html>
