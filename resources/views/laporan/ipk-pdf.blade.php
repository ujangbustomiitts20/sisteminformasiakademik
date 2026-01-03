<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi IPK</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .stats-box { display: inline-block; margin: 5px; padding: 8px; border: 1px solid #ddd; text-align: center; width: 100px; }
        .stats-box h3 { margin: 0; font-size: 20px; }
        .stats-box small { color: #666; font-size: 9px; }
        .text-center { text-align: center; }
        .summary { background: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; text-align: center; }
        .summary h1 { margin: 0; font-size: 36px; color: #007bff; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ setting('nama_institusi', 'SIAKAD') }}</h2>
        <h3>Laporan Distribusi IPK Mahasiswa</h3>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="text-center">
        <div class="stats-box">
            <h3>{{ $distribusi['cumlaude'] ?? 0 }}</h3>
            <small>Cum Laude (≥3.51)</small>
        </div>
        <div class="stats-box">
            <h3>{{ $distribusi['sangat_memuaskan'] ?? 0 }}</h3>
            <small>Sangat Memuaskan</small>
        </div>
        <div class="stats-box">
            <h3>{{ $distribusi['memuaskan'] ?? 0 }}</h3>
            <small>Memuaskan</small>
        </div>
        <div class="stats-box">
            <h3>{{ $distribusi['cukup'] ?? 0 }}</h3>
            <small>Cukup</small>
        </div>
    </div>

    <div class="summary">
        <h1>{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</h1>
        <p>Rata-rata IPK dari {{ $summary['total'] ?? 0 }} mahasiswa</p>
    </div>

    <h4>Detail IPK Mahasiswa:</h4>
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Angkatan</th>
                <th width="50">IPK</th>
                <th width="50">SKS</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswa as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $data['mahasiswa']->nim }}</td>
                <td>{{ $data['mahasiswa']->nama }}</td>
                <td>{{ $data['mahasiswa']->programStudi->nama ?? '-' }}</td>
                <td class="text-center">{{ $data['mahasiswa']->angkatan }}</td>
                <td class="text-center"><strong>{{ number_format($data['ipk'], 2) }}</strong></td>
                <td class="text-center">{{ $data['total_sks'] }}</td>
                <td>
                    <span class="badge badge-{{ $data['kategori'] == 'Cum Laude' ? 'success' : ($data['kategori'] == 'Sangat Memuaskan' ? 'primary' : ($data['kategori'] == 'Memuaskan' ? 'info' : 'warning')) }}">
                        {{ $data['kategori'] }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data mahasiswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ setting('nama_institusi', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
