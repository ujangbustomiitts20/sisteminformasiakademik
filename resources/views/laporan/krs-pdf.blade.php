<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengambilan KRS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .stats-box { display: inline-block; margin: 5px; padding: 10px; border: 1px solid #ddd; text-align: center; width: 100px; }
        .stats-box h3 { margin: 0; font-size: 20px; }
        .stats-box small { color: #666; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ setting('institution_name', 'SIAKAD') }}</h2>
        <h3>Laporan Pengambilan KRS</h3>
        @if($tahunAkademikAktif)
        <p>Tahun Akademik: {{ $tahunAkademikAktif->tahun ?? '' }} - {{ $tahunAkademikAktif->semester ?? '' }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="text-center">
        <div class="stats-box">
            <h3>{{ $summary['total_mk'] ?? 0 }}</h3>
            <small>Total MK</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['total_peserta'] ?? 0 }}</h3>
            <small>Total Peserta</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['rata_peserta'] ?? 0 }}</h3>
            <small>Rata-rata/MK</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['mk_penuh'] ?? 0 }}</h3>
            <small>Kelas Penuh</small>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Kelas</th>
                <th width="40">SKS</th>
                <th width="50">Peserta</th>
                <th width="50">Kapasitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($krsData as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $data['kode'] }}</td>
                <td>{{ $data['mata_kuliah'] }}</td>
                <td>{{ $data['dosen'] }}</td>
                <td>{{ $data['kelas'] }}</td>
                <td class="text-center">{{ $data['sks'] }}</td>
                <td class="text-center"><strong>{{ $data['peserta'] }}</strong></td>
                <td class="text-center">{{ $data['kapasitas'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data KRS</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ setting('institution_name', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
