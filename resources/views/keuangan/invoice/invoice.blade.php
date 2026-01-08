<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $tagihan->no_tagihan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .invoice-title {
            text-align: center;
            margin: 20px 0;
        }
        .invoice-title h2 {
            font-size: 16px;
            background: #f0f0f0;
            padding: 10px;
            display: inline-block;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .info-box h4 {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 0;
        }
        .info-box td:first-child {
            width: 40%;
            color: #666;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table.items th {
            background: #4CAF50;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        table.items td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table.items tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background: #f0f0f0 !important;
            font-weight: bold;
        }
        .summary-box {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .summary-box .label {
            background: #f0f0f0;
        }
        .grand-total {
            background: #4CAF50 !important;
            color: white;
            font-size: 14px;
        }
        .status-box {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            border: 2px solid;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }
        .status-lunas {
            border-color: #4CAF50;
            color: #4CAF50;
            background: #E8F5E9;
        }
        .status-belum {
            border-color: #FF9800;
            color: #FF9800;
            background: #FFF3E0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background: #E3F2FD;
            border: 1px solid #90CAF9;
            border-radius: 5px;
        }
        .payment-info h4 {
            color: #1976D2;
            margin-bottom: 10px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ setting('nama_institusi', 'Institut Teknologi Tangerang Selatan') }}</h1>
            <p>{{ setting('alamat_institusi', 'Jl. Pendidikan No. 123') }}</p>
            <p>Telp: {{ setting('telepon_institusi', '(021) 1234567') }} | Email: {{ setting('email_keuangan', 'keuangan@itts.ac.id') }}</p>
        </div>

        <div class="invoice-title">
            <h2>INVOICE TAGIHAN</h2>
        </div>

        <div class="info-row">
            <div class="info-col">
                <div class="info-box">
                    <h4>DATA MAHASISWA</h4>
                    <table>
                        <tr>
                            <td>NIM</td>
                            <td>: {{ $tagihan->mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: {{ $tagihan->mahasiswa->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>: {{ $tagihan->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Angkatan</td>
                            <td>: {{ $tagihan->mahasiswa->angkatan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="info-col">
                <div class="info-box" style="margin-right: 0; margin-left: 10px;">
                    <h4>INFORMASI TAGIHAN</h4>
                    <table>
                        <tr>
                            <td>No. Invoice</td>
                            <td>: {{ $tagihan->no_tagihan }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ $tagihan->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Tahun Akademik</td>
                            <td>: {{ $tagihan->tahunAkademik->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Jatuh Tempo</td>
                            <td>: {{ $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="55%">Keterangan</th>
                    <th width="40%" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>{{ $tagihan->tarif->nama ?? 'Tagihan' }} - {{ $tagihan->tahunAkademik->nama ?? '' }}</td>
                    <td class="text-right">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                </tr>
                @if($tagihan->denda > 0)
                <tr>
                    <td class="text-center">2</td>
                    <td>Denda Keterlambatan</td>
                    <td class="text-right">Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="clearfix">
            <div class="summary-box">
                <table>
                    <tr>
                        <td class="label">Total Tagihan</td>
                        <td class="text-right">Rp {{ number_format($tagihan->nominal + ($tagihan->denda ?? 0), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total Bayar</td>
                        <td class="text-right">Rp {{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>SISA TAGIHAN</td>
                        <td class="text-right">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="clear: both;"></div>

        @if($tagihan->status === 'Lunas')
        <div class="status-box status-lunas">
            ✓ LUNAS
        </div>
        @else
        <div class="status-box status-belum">
            BELUM LUNAS - Sisa: Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
        </div>

        <div class="payment-info">
            <h4>Informasi Pembayaran:</h4>
            <p>Pembayaran dapat dilakukan melalui:</p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li>Transfer Bank BNI ke nomor Virtual Account mahasiswa</li>
                <li>Pembayaran tunai di loket keuangan kampus</li>
                <li>QRIS melalui aplikasi mobile banking</li>
            </ul>
        </div>
        @endif

        @if($tagihan->transaksi && $tagihan->transaksi->count() > 0)
        <h4 style="margin-top: 30px; margin-bottom: 10px;">Riwayat Pembayaran:</h4>
        <table class="items">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihan->transaksi as $trx)
                <tr>
                    <td>{{ $trx->no_transaksi }}</td>
                    <td>{{ $trx->tanggal_bayar->format('d/m/Y') }}</td>
                    <td>{{ $trx->metode_pembayaran }}</td>
                    <td class="text-right">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $trx->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="footer">
            <p>Dokumen ini dicetak secara otomatis oleh sistem {{ setting('app_name', 'NADI ITTS') }} pada {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Invoice ini sah tanpa tanda tangan</p>
        </div>
    </div>
</body>
</html>
