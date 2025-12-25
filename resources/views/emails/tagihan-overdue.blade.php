<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Melewati Jatuh Tempo</title>
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
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
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
        .alert-box {
            background: #fff5f5;
            border: 2px solid #f12711;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .alert-box .days {
            font-size: 48px;
            font-weight: bold;
            color: #f12711;
        }
        .info-box {
            background: white;
            border-left: 4px solid #f12711;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #f12711;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin: 20px 0;
        }
        .denda-info {
            background: #ffe6e6;
            border: 1px solid #f12711;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
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
        .warning-list {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px 15px 15px 35px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning-list li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚠️ TAGIHAN TERLAMBAT</h1>
        <p>Perhatian Segera Diperlukan</p>
    </div>
    
    <div class="content">
        <p>Yth. <strong>{{ $mahasiswa->nama ?? 'Mahasiswa' }}</strong>,</p>
        
        <div class="alert-box">
            <p style="margin: 0; font-size: 14px; color: #666;">Tagihan Anda telah melewati jatuh tempo selama</p>
            <div class="days">{{ $hariTerlambat }}</div>
            <p style="margin: 0; font-size: 14px; color: #666;">HARI</p>
        </div>
        
        <p>Kami menginformasikan bahwa tagihan Anda telah melewati batas waktu pembayaran:</p>
        
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
                <tr>
                    <td>Jatuh Tempo</td>
                    <td style="color: #f12711; font-weight: bold;">
                        {{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->locale('id')->isoFormat('D MMMM Y') }}
                    </td>
                </tr>
            </table>
        </div>
        
        @php
            $sisaTagihan = $tagihan->sisa_tagihan ?? ($tagihan->jumlah_tagihan - $tagihan->jumlah_dibayar);
            $denda = $tagihan->denda ?? 0;
            $total = $sisaTagihan + $denda;
        @endphp
        
        <div class="amount">
            <small style="font-size: 14px; color: #666; font-weight: normal;">Total yang harus dibayar:</small><br>
            Rp {{ number_format($total, 0, ',', '.') }}
        </div>
        
        @if($denda > 0)
        <div class="denda-info">
            <p style="margin: 0;">
                <strong>Rincian:</strong><br>
                Pokok Tagihan: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}<br>
                Denda Keterlambatan: <span style="color: #f12711;">Rp {{ number_format($denda, 0, ',', '.') }}</span>
            </p>
        </div>
        @endif
        
        <p style="text-align: center;">
            <a href="{{ url('/mahasiswa/tagihan/' . $tagihan->id) }}" class="btn">
                Bayar Sekarang
            </a>
        </p>
        
        <p style="margin-top: 30px;">
            <strong>⚠️ Konsekuensi Keterlambatan:</strong>
        </p>
        <ul class="warning-list">
            <li>Denda akan terus bertambah setiap harinya.</li>
            <li>Akses ke layanan akademik dapat dibatasi.</li>
            <li>Tidak dapat mengikuti ujian atau kegiatan akademik tertentu.</li>
            <li>Pengurusan administrasi akan terhambat.</li>
        </ul>
        
        <p>Mohon segera lakukan pembayaran untuk menghindari konsekuensi lebih lanjut.</p>
        
        <p>Jika Anda mengalami kesulitan dalam pembayaran, silakan hubungi bagian keuangan untuk mendiskusikan solusi yang tersedia.</p>
        
        <p>Terima kasih atas perhatiannya.</p>
        
        <p>Hormat kami,<br>
        <strong>Tim Keuangan SIAKAD</strong></p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
        <p>Untuk pertanyaan, silakan hubungi bagian keuangan.</p>
        <p>© {{ date('Y') }} SIAKAD - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
