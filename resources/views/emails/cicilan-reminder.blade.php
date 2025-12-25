<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Cicilan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .info-box {
            background: white;
            border-left: 4px solid #11998e;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .cicilan-badge {
            display: inline-block;
            background: #11998e;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #11998e;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin: 20px 0;
        }
        .deadline {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .progress-container {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .progress-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            height: 100%;
            transition: width 0.3s ease;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            background: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 10px 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📅 Reminder Cicilan</h1>
        <p>Sistem Informasi Akademik</p>
    </div>
    
    <div class="content">
        <p>Yth. <strong>{{ $mahasiswa->nama ?? 'Mahasiswa' }}</strong>,</p>
        
        <p>Kami ingin mengingatkan bahwa cicilan Anda akan segera jatuh tempo:</p>
        
        <div class="info-box">
            <span class="cicilan-badge">Cicilan Ke-{{ $detail->cicilan_ke }} dari {{ $cicilan->skemaCicilan->jumlah_cicilan ?? $cicilan->jumlah_cicilan }}</span>
            <table>
                <tr>
                    <td>Nomor Cicilan</td>
                    <td>{{ $cicilan->nomor_cicilan }}</td>
                </tr>
                <tr>
                    <td>Tagihan</td>
                    <td>{{ $cicilan->tagihan->jenis_tagihan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Skema</td>
                    <td>{{ $cicilan->skemaCicilan->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>
        
        <div class="amount">
            Rp {{ number_format($detail->jumlah, 0, ',', '.') }}
        </div>
        
        <div class="deadline">
            <strong>⚠️ Jatuh Tempo Cicilan:</strong><br>
            {{ \Carbon\Carbon::parse($detail->jatuh_tempo)->locale('id')->isoFormat('dddd, D MMMM Y') }}
            <br>
            <small>({{ \Carbon\Carbon::parse($detail->jatuh_tempo)->diffForHumans() }})</small>
        </div>
        
        @php
            $totalCicilan = $cicilan->skemaCicilan->jumlah_cicilan ?? $cicilan->jumlah_cicilan ?? 1;
            $cicilanTerbayar = $cicilan->detailCicilan->where('status', 'lunas')->count();
            $progress = ($cicilanTerbayar / $totalCicilan) * 100;
        @endphp
        
        <div class="progress-container">
            <p style="margin: 0 0 10px 0;"><strong>Progress Pembayaran:</strong></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $progress }}%;"></div>
            </div>
            <p style="text-align: center; margin: 10px 0 0 0; color: #666;">
                {{ $cicilanTerbayar }} dari {{ $totalCicilan }} cicilan terbayar ({{ number_format($progress, 0) }}%)
            </p>
        </div>
        
        <p style="text-align: center;">
            <a href="{{ url('/mahasiswa/cicilan/' . $cicilan->id) }}" class="btn">
                Lihat Detail & Bayar Sekarang
            </a>
        </p>
        
        <p style="margin-top: 30px;">
            <strong>Informasi Penting:</strong>
        </p>
        <ul>
            <li>Keterlambatan pembayaran cicilan akan dikenakan denda.</li>
            <li>Pastikan pembayaran dilakukan sebelum tanggal jatuh tempo.</li>
            <li>Simpan bukti pembayaran untuk referensi.</li>
        </ul>
        
        <p>Jika Anda sudah melakukan pembayaran, mohon abaikan email ini.</p>
        
        <p>Terima kasih atas perhatian dan kerjasamanya.</p>
        
        <p>Salam,<br>
        <strong>Tim Keuangan SIAKAD</strong></p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
        <p>© {{ date('Y') }} SIAKAD - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
