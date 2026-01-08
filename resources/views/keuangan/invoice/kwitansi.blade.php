<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $transaksi->no_transaksi }}</title>
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
        .kwitansi-title {
            text-align: center;
            margin: 20px 0;
        }
        .kwitansi-title h2 {
            font-size: 20px;
            letter-spacing: 5px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 10px 30px;
            display: inline-block;
            border-radius: 5px;
        }
        .kwitansi-no {
            text-align: center;
            margin-bottom: 20px;
        }
        .kwitansi-no span {
            font-size: 14px;
            font-weight: bold;
            color: #4CAF50;
        }
        .content-table {
            width: 100%;
            margin: 20px 0;
        }
        .content-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .content-table .label {
            width: 150px;
            font-weight: bold;
        }
        .content-table .colon {
            width: 20px;
            text-align: center;
        }
        .amount-box {
            background: #E8F5E9;
            border: 2px solid #4CAF50;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 10px;
        }
        .amount-box .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .amount-box .amount {
            font-size: 24px;
            font-weight: bold;
            color: #2E7D32;
        }
        .terbilang {
            font-style: italic;
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .purpose-box {
            background: #FFF3E0;
            border-left: 4px solid #FF9800;
            padding: 15px;
            margin: 20px 0;
        }
        .purpose-box strong {
            color: #E65100;
        }
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-box {
            padding: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin: 60px auto 10px;
        }
        .stamp-area {
            position: relative;
            height: 100px;
        }
        .paid-stamp {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) rotate(-15deg);
            border: 4px solid #4CAF50;
            color: #4CAF50;
            padding: 10px 20px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 10px;
            opacity: 0.8;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table th {
            background: #f0f0f0;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .detail-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
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

        <div class="kwitansi-title">
            <h2>KWITANSI</h2>
        </div>

        <div class="kwitansi-no">
            No: <span>{{ $transaksi->no_transaksi }}</span>
        </div>

        <table class="content-table">
            <tr>
                <td class="label">Telah Diterima Dari</td>
                <td class="colon">:</td>
                <td><strong>{{ $transaksi->mahasiswa->nama ?? '-' }}</strong> ({{ $transaksi->mahasiswa->nim ?? '-' }})</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td class="colon">:</td>
                <td>{{ $transaksi->mahasiswa->programStudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Akademik</td>
                <td class="colon">:</td>
                <td>{{ $transaksi->tagihan->tahunAkademik->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td class="colon">:</td>
                <td>{{ $transaksi->tanggal_bayar->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td class="colon">:</td>
                <td>{{ $transaksi->metode_pembayaran }}</td>
            </tr>
        </table>

        <div class="amount-box">
            <div class="label">JUMLAH PEMBAYARAN</div>
            <div class="amount">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</div>
        </div>

        <div class="terbilang">
            Terbilang: <strong>{{ ucwords(\App\Helpers\Terbilang::make($transaksi->jumlah)) }} Rupiah</strong>
        </div>

        <div class="purpose-box">
            <strong>Untuk Pembayaran:</strong><br>
            {{ $transaksi->tagihan->tarif->nama ?? 'Tagihan' }} - {{ $transaksi->tagihan->tahunAkademik->nama ?? '' }}
            <br>
            <small>No. Tagihan: {{ $transaksi->tagihan->no_tagihan ?? '-' }}</small>
        </div>

        <table class="detail-table">
            <tr>
                <th>Keterangan</th>
                <th class="text-right" width="150">Jumlah</th>
            </tr>
            <tr>
                <td>Pembayaran {{ $transaksi->tagihan->tarif->nama ?? 'Tagihan' }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="signature-section">
            <div class="signature-col">
                <div class="signature-box">
                    <div class="stamp-area">
                        @if($transaksi->status === 'Verified')
                        <div class="paid-stamp">LUNAS</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="signature-col">
                <div class="signature-box">
                    <p>Kota Akademik, {{ $transaksi->verified_at ? $transaksi->verified_at->format('d F Y') : now()->format('d F Y') }}</p>
                    <p>Petugas Keuangan</p>
                    <div class="signature-line"></div>
                    <p><strong>{{ $transaksi->verifier->name ?? 'Admin Keuangan' }}</strong></p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Kwitansi ini sah sebagai bukti pembayaran</p>
            <p>Dicetak oleh sistem {{ setting('app_name', 'NADI ITTS') }} pada {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
