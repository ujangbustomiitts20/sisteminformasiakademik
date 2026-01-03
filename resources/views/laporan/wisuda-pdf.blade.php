<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Wisuda & Lulusan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .stats-box { display: inline-block; margin: 10px; padding: 10px; border: 1px solid #ddd; text-align: center; width: 120px; }
        .stats-box h3 { margin: 0; font-size: 24px; }
        .stats-box small { color: #666; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ setting('nama_institusi', 'SIAKAD') }}</h2>
        <h3>Laporan Wisuda & Lulusan</h3>
        @if($periodeAktif)
        <p>Periode: {{ $periodeAktif->nama_periode ?? '' }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="text-center">
        <div class="stats-box">
            <h3>{{ $summary['cum_laude'] ?? 0 }}</h3>
            <small>Cum Laude</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['sangat_memuaskan'] ?? 0 }}</h3>
            <small>Sangat Memuaskan</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['memuaskan'] ?? 0 }}</h3>
            <small>Memuaskan</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['total_lulusan'] ?? 0 }}</h3>
            <small>Total Lulusan</small>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th width="50">IPK</th>
                <th width="60">SKS</th>
                <th>Predikat</th>
                <th>Tanggal Lulus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($yudisium as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $data->mahasiswa->nim ?? '-' }}</td>
                <td>{{ $data->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $data->mahasiswa->programStudi->nama ?? '-' }}</td>
                <td class="text-center"><strong>{{ number_format($data->ipk_akhir ?? 0, 2) }}</strong></td>
                <td class="text-center">{{ $data->total_sks ?? 0 }}</td>
                <td>
                    <span class="badge badge-{{ $data->predikat == 'Cum Laude' ? 'success' : ($data->predikat == 'Sangat Memuaskan' ? 'primary' : 'info') }}">
                        {{ $data->predikat }}
                    </span>
                </td>
                <td>{{ $data->tanggal_lulus ? $data->tanggal_lulus->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data lulusan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ setting('nama_institusi', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
