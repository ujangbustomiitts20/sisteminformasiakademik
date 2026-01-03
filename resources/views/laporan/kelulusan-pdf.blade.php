<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik Kelulusan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .stats-box { display: inline-block; margin: 5px; padding: 10px; border: 1px solid #ddd; text-align: center; width: 100px; }
        .stats-box h3 { margin: 0; font-size: 18px; }
        .stats-box small { color: #666; font-size: 9px; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .section-title { background: #f5f5f5; padding: 8px; margin: 20px 0 10px; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
        .summary { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ setting('nama_institusi', 'SIAKAD') }}</h2>
        <h3>Laporan Statistik Kelulusan - Tahun {{ $tahunFilter }}</h3>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="text-center">
        <div class="stats-box">
            <h3>{{ $summary['total_lulusan'] ?? 0 }}</h3>
            <small>Total Lulusan</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['cum_laude'] ?? 0 }}</h3>
            <small>Cum Laude</small>
        </div>
        <div class="stats-box">
            <h3>{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</h3>
            <small>Rata-rata IPK</small>
        </div>
        <div class="stats-box">
            <h3>{{ number_format($summary['masa_studi_rata'] ?? 0, 1) }}</h3>
            <small>Rata-rata Masa Studi</small>
        </div>
    </div>

    <div class="section-title">Data Kelulusan per Tahun</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th class="text-center">Jumlah</th>
                <th class="text-center">Cum Laude</th>
                <th class="text-center">Sangat Memuaskan</th>
                <th class="text-center">Memuaskan</th>
                <th class="text-center">Avg IPK</th>
                <th class="text-center">Avg Masa Studi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelulusanPerTahun as $data)
            <tr>
                <td><strong>{{ $data['tahun'] }}</strong></td>
                <td class="text-center">{{ $data['total'] }}</td>
                <td class="text-center">
                    <span class="badge badge-success">{{ $data['cumlaude'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-primary">{{ $data['sangat_memuaskan'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-info">{{ $data['memuaskan'] }}</span>
                </td>
                <td class="text-center">{{ number_format($data['avg_ipk'], 2) }}</td>
                <td class="text-center">{{ number_format($data['avg_masa_studi'], 1) }} thn</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background: #f9f9f9;">
            <tr>
                <th>Total/Rata-rata</th>
                <th class="text-center">{{ $summary['total_lulusan'] ?? 0 }}</th>
                <th class="text-center">{{ collect($kelulusanPerTahun)->sum('cumlaude') }}</th>
                <th class="text-center">{{ collect($kelulusanPerTahun)->sum('sangat_memuaskan') }}</th>
                <th class="text-center">{{ collect($kelulusanPerTahun)->sum('memuaskan') }}</th>
                <th class="text-center">{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</th>
                <th class="text-center">{{ number_format($summary['masa_studi_rata'] ?? 0, 1) }} thn</th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Kelulusan per Program Studi</div>
    <table>
        <thead>
            <tr>
                <th>Program Studi</th>
                <th class="text-center">Total</th>
                <th class="text-center">Cum Laude</th>
                <th class="text-center">Sangat Memuaskan</th>
                <th class="text-center">Memuaskan</th>
                <th class="text-center">Avg IPK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelulusanPerProdi as $data)
            <tr>
                <td><strong>{{ $data['prodi'] }}</strong></td>
                <td class="text-center">{{ $data['total'] }}</td>
                <td class="text-center">
                    <span class="badge badge-success">{{ $data['cumlaude'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-primary">{{ $data['sangat_memuaskan'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-info">{{ $data['memuaskan'] }}</span>
                </td>
                <td class="text-center">{{ number_format($data['avg_ipk'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ setting('nama_institusi', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
