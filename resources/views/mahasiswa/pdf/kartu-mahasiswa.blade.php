<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Mahasiswa - {{ $mahasiswa->nim }}</title>
    <style>
        @page {
            margin: 0;
        }
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }
        .card {
            width: 323px;
            height: 204px;
            background-color: #1e40af;
            padding: 15px;
            color: white;
            position: relative;
        }
        .header {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #60a5fa;
            padding-bottom: 8px;
        }
        .content {
            width: 100%;
        }
        .photo-cell {
            width: 70px;
            vertical-align: top;
            padding-right: 10px;
        }
        .photo-box {
            width: 60px;
            height: 80px;
            background-color: #ffffff;
            text-align: center;
            line-height: 80px;
            color: #1e40af;
            font-size: 28px;
            font-weight: bold;
        }
        .info-cell {
            vertical-align: top;
        }
        .name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .nim {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
            color: #93c5fd;
        }
        .detail {
            font-size: 9px;
            margin-bottom: 3px;
            color: #e0e7ff;
        }
        .detail strong {
            color: #ffffff;
        }
        .status-box {
            position: absolute;
            top: 12px;
            right: 12px;
            background-color: {{ $mahasiswa->status == 'Aktif' ? '#16a34a' : '#dc2626' }};
            color: white;
            padding: 3px 8px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer-section {
            position: absolute;
            bottom: 10px;
            left: 15px;
            right: 15px;
            font-size: 7px;
            color: #bfdbfe;
            border-top: 1px solid #60a5fa;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="status-box">{{ $mahasiswa->status }}</div>
        
        <div class="header">
            {{ setting('nama_institusi', 'SISTEM INFORMASI AKADEMIK') }}
        </div>
        
        <table class="content" cellpadding="0" cellspacing="0">
            <tr>
                <td class="photo-cell">
                    <div class="photo-box">
                        {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                    </div>
                </td>
                <td class="info-cell">
                    <div class="name">{{ $mahasiswa->nama }}</div>
                    <div class="nim">{{ $mahasiswa->nim }}</div>
                    <div class="detail"><strong>{{ $mahasiswa->programStudi->nama ?? '-' }}</strong></div>
                    <div class="detail">{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</div>
                    <div class="detail">Angkatan: {{ $mahasiswa->angkatan ?? '-' }}</div>
                    @if($mahasiswa->tempat_lahir || $mahasiswa->tanggal_lahir)
                    <div class="detail">{{ $mahasiswa->tempat_lahir }}{{ $mahasiswa->tanggal_lahir ? ', ' . $mahasiswa->tanggal_lahir->format('d/m/Y') : '' }}</div>
                    @endif
                </td>
            </tr>
        </table>
        
        <div class="footer-section">
            Kartu Mahasiswa Resmi - Berlaku selama terdaftar aktif | Kode: {{ strtoupper(substr(md5($mahasiswa->nim), 0, 8)) }}
        </div>
    </div>
</body>
</html>
