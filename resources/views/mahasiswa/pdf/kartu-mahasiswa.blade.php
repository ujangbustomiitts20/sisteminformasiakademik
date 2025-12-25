<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Mahasiswa - {{ $mahasiswa->nim }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; }
        .card {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
            border-radius: 10px;
            padding: 10px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .logo {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .photo-section {
            float: left;
            width: 55px;
            height: 70px;
            background: white;
            border-radius: 5px;
            margin-right: 10px;
            overflow: hidden;
        }
        .photo-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            width: 100%;
            height: 100%;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 24px;
            font-weight: bold;
        }
        .info {
            font-size: 7px;
            line-height: 1.4;
        }
        .info .name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .info .nim {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .info p {
            margin-bottom: 2px;
            opacity: 0.9;
        }
        .footer-card {
            position: absolute;
            bottom: 8px;
            left: 10px;
            right: 10px;
            font-size: 6px;
            opacity: 0.8;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 5px;
        }
        .status-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #22c55e;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-badge.inactive {
            background: #ef4444;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="status-badge {{ $mahasiswa->status != 'Aktif' ? 'inactive' : '' }}">
            {{ $mahasiswa->status }}
        </div>
        
        <div class="logo">
            SISTEM INFORMASI AKADEMIK
        </div>
        
        <div class="photo-section">
            @if($mahasiswa->foto && file_exists(public_path('storage/' . $mahasiswa->foto)))
            <img src="{{ public_path('storage/' . $mahasiswa->foto) }}" alt="Foto">
            @else
            <div class="photo-placeholder">
                {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
            </div>
            @endif
        </div>
        
        <div class="info">
            <div class="name">{{ strtoupper($mahasiswa->nama) }}</div>
            <div class="nim">{{ $mahasiswa->nim }}</div>
            <p><strong>{{ $mahasiswa->programStudi->nama ?? '-' }}</strong></p>
            <p>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</p>
            <p>Angkatan: {{ $mahasiswa->angkatan ?? '-' }}</p>
            @if($mahasiswa->tempat_lahir || $mahasiswa->tanggal_lahir)
            <p>{{ $mahasiswa->tempat_lahir }}{{ $mahasiswa->tanggal_lahir ? ', ' . $mahasiswa->tanggal_lahir->format('d/m/Y') : '' }}</p>
            @endif
        </div>
        
        <div class="footer-card">
            <p><strong>Kartu Mahasiswa Resmi</strong> - Berlaku selama terdaftar aktif.</p>
            <p>Dicetak: {{ now()->format('d F Y') }} | Validasi: <strong>{{ strtoupper(substr(md5($mahasiswa->nim), 0, 8)) }}</strong></p>
        </div>
    </div>
</body>
</html>
