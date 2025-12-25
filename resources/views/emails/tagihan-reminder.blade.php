<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Tagihan</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
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
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        <h1>🔔 Reminder Tagihan</h1>
        <p>Sistem Informasi Akademik</p>
    </div>
    
    <div class="content">
        <p>Yth. <strong>{{ $mahasiswa->nama ?? 'Mahasiswa' }}</strong>,</p>
        
        <p>Kami ingin mengingatkan bahwa Anda memiliki tagihan yang akan segera jatuh tempo:</p>
        
        <div class="info-box">
            <table>
                <tr>
                    <td>Nomor Tagihan</td>
                    <td>{{ $tagihan->nomor_tagihan }}</td>
                </tr>
                <tr>
                    <td>Jenis Tagihan</td>
                    <td>{{ $tagihan->jenis_tagihan }}</td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td>{{ $tagihan->semester }}</td>
                </tr>
                <tr>
                    <td>Tahun Akademik</td>
                    <td>{{ $tagihan->tahun_akademik }}</td>
                </tr>
            </table>
        </div>
        
        <div class="amount">
            Rp {{ number_format($tagihan->sisa_tagihan ?? ($tagihan->jumlah_tagihan - $tagihan->jumlah_dibayar), 0, ',', '.') }}
        </div>
        
        <div class="deadline">
            <strong>⚠️ Jatuh Tempo:</strong><br>
            {{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->locale('id')->isoFormat('dddd, D MMMM Y') }}
            <br>
            <small>({{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->diffForHumans() }})</small>
        </div>
        
        <p style="text-align: center;">
            <a href="{{ url('/mahasiswa/tagihan/' . $tagihan->id) }}" class="btn">
                Lihat Detail & Bayar Sekarang
            </a>
        </p>
        
        <p style="margin-top: 30px;">
            <strong>Catatan Penting:</strong>
        </p>
        <ul>
            <li>Keterlambatan pembayaran akan dikenakan denda sesuai ketentuan yang berlaku.</li>
            <li>Pembayaran dapat dilakukan melalui Virtual Account atau transfer bank.</li>
            <li>Simpan bukti pembayaran sebagai referensi.</li>
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
