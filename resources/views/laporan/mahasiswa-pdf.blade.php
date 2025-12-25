<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mahasiswa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; font-size: 10px; color: #666; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-secondary { background: #e9ecef; color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA MAHASISWA</h2>
        <p>Sistem Informasi Akademik</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <p>
        <strong>Filter:</strong>
        @if($request->program_studi_id) Program Studi: {{ \App\Models\ProgramStudi::find($request->program_studi_id)?->nama }} | @endif
        @if($request->status) Status: {{ $request->status }} | @endif
        @if($request->angkatan) Angkatan: {{ $request->angkatan }} @endif
    </p>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th width="80">NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th width="60">Angkatan</th>
                <th width="60">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswa as $index => $mhs)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->nama }}</td>
                <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                <td class="text-center">{{ $mhs->angkatan }}</td>
                <td class="text-center">{{ $mhs->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $mahasiswa->count() }} mahasiswa</p>
    </div>
</body>
</html>
