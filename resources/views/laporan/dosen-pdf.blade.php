<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Dosen</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        .stats-box { display: inline-block; margin: 5px; padding: 8px; border: 1px solid #ddd; text-align: center; width: 90px; }
        .stats-box h3 { margin: 0; font-size: 18px; }
        .stats-box small { color: #666; font-size: 9px; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .section-title { background: #f5f5f5; padding: 8px; margin: 15px 0 10px; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ setting('institution_name', 'SIAKAD') }}</h2>
        <h3>Laporan Data Dosen & Beban Mengajar</h3>
        @if($tahunAkademikAktif)
        <p>Tahun Akademik: {{ $tahunAkademikAktif->tahun ?? '' }} - {{ $tahunAkademikAktif->semester ?? '' }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="text-center">
        <div class="stats-box">
            <h3>{{ $summary['total_dosen'] ?? 0 }}</h3>
            <small>Total Dosen</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['total_sks'] ?? 0 }}</h3>
            <small>Total SKS</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['rata_sks'] ?? 0 }}</h3>
            <small>Rata-rata SKS</small>
        </div>
        <div class="stats-box">
            <h3>{{ $summary['dosen_overload'] ?? 0 }}</h3>
            <small>Overload (>16)</small>
        </div>
    </div>

    <div class="section-title">Daftar Dosen</div>
    <table>
        <thead>
            <tr>
                <th width="25">No</th>
                <th>NIDN</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th width="50">SKS</th>
                <th width="50">MK</th>
                <th width="50">Bimbingan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dosenData as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $data['dosen']->nidn }}</td>
                <td>{{ $data['dosen']->nama }}</td>
                <td>{{ $data['dosen']->programStudi->nama ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $data['total_sks'] > 16 ? 'danger' : 'info' }}">{{ $data['total_sks'] }}</span>
                </td>
                <td class="text-center">{{ $data['jumlah_mk'] }}</td>
                <td class="text-center">{{ $data['mahasiswa_bimbingan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data dosen</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @foreach($dosenData as $data)
    @if($data['jadwal'] && $data['jadwal']->count() > 0)
    <div class="section-title">{{ $data['dosen']->nama }} ({{ $data['dosen']->nidn }}) - {{ $data['total_sks'] }} SKS</div>
    <table>
        <thead>
            <tr>
                <th>Mata Kuliah</th>
                <th>Kelas</th>
                <th width="40">SKS</th>
                <th>Hari</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['jadwal'] as $jadwal)
            <tr>
                <td>{{ $jadwal->mataKuliah->nama ?? '-' }}</td>
                <td>{{ $jadwal->kelas ?? '-' }}</td>
                <td class="text-center">{{ $jadwal->mataKuliah->sks ?? 0 }}</td>
                <td>{{ $jadwal->hari ?? '-' }}</td>
                <td>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endforeach

    <div class="footer">
        <p>{{ setting('institution_name', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
                <th>Hari</th>
                <th>Jam</th>
                <th>Ruangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dsn->jadwalKuliahs as $jadwal)
            <tr>
                <td>{{ $jadwal->mataKuliah->nama ?? '-' }}</td>
                <td>{{ $jadwal->kelas ?? '-' }}</td>
                <td class="text-center">{{ $jadwal->mataKuliah->sks ?? 0 }}</td>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endforeach

    <div class="footer">
        <p>{{ setting('institution_name', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    </div>
</body>
</html>
