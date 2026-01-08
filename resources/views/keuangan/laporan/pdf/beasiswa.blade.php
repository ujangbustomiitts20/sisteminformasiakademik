<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Beasiswa</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        .title { text-align: center; margin: 20px 0; }
        .title h2 { font-size: 14px; margin: 0; color: #1976D2; }
        .info { margin-bottom: 15px; }
        .info p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #2196F3; color: white; padding: 8px; text-align: left; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 8px; }
        .badge-success { background: #4CAF50; color: white; }
        .badge-warning { background: #FF9800; color: white; }
        .badge-danger { background: #F44336; color: white; }
        .badge-secondary { background: #9E9E9E; color: white; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('nama_institusi', 'Institut Teknologi Tangerang Selatan') }}</h1>
        <p>Jl. Pendidikan No. 123, Kota Akademik 12345</p>
    </div>

    <div class="title">
        <h2>LAPORAN PENERIMA BEASISWA</h2>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Total Penerima:</strong> {{ $penerima->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">NIM</th>
                <th width="18%">Nama</th>
                <th width="12%">Prodi</th>
                <th width="15%">Beasiswa</th>
                <th width="10%">Nilai</th>
                <th width="10%">Tahun Akad</th>
                <th width="10%">Periode</th>
                <th width="11%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penerima as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->mahasiswa->nim ?? '-' }}</td>
                <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $item->mahasiswa->programStudi->nama ?? '-' }}</td>
                <td>{{ $item->beasiswa->nama ?? '-' }}</td>
                <td>
                    @if($item->beasiswa)
                        @if($item->beasiswa->tipe_potongan === 'Persen')
                            {{ $item->beasiswa->nilai_potongan }}%
                        @else
                            Rp {{ number_format($item->beasiswa->nilai_potongan, 0, ',', '.') }}
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                <td>{{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    @php
                        $statusClass = match($item->status) {
                            'Disetujui' => 'badge-success',
                            'Diajukan' => 'badge-warning',
                            'Ditolak' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $item->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem {{ setting('app_name', 'NADI ITTS') }}</p>
    </div>
</body>
</html>
