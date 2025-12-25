<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Tagihan - {{ $mahasiswa->nim }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 16px; margin-bottom: 5px; }
        .header h2 { font-size: 14px; font-weight: normal; margin-bottom: 5px; }
        .header p { font-size: 9px; color: #666; }
        .info-box { background: #f5f5f5; padding: 10px; margin-bottom: 15px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 5px; }
        .info-box td:first-child { width: 120px; color: #666; }
        .info-box td:last-child { font-weight: bold; }
        table.tagihan { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.tagihan th, table.tagihan td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.tagihan th { background: #4f46e5; color: white; font-size: 9px; }
        table.tagihan td { font-size: 9px; }
        table.tagihan tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; }
        .badge-success { background: #22c55e; color: white; }
        .badge-danger { background: #ef4444; color: white; }
        .badge-warning { background: #f59e0b; color: white; }
        .footer { margin-top: 30px; font-size: 8px; color: #666; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
        .summary { background: #e0e7ff; padding: 10px; margin-top: 15px; }
        .summary table { width: 100%; }
        .summary td { padding: 3px 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('institution_name', 'UNIVERSITAS') }}</h1>
        <h2>KARTU TAGIHAN MAHASISWA</h2>
        <p>{{ setting('institution_address', '') }}</p>
    </div>
    
    <div class="info-box">
        <table>
            <tr>
                <td>NIM</td>
                <td>{{ $mahasiswa->nim }}</td>
                <td>Program Studi</td>
                <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>{{ $mahasiswa->nama }}</td>
                <td>Angkatan</td>
                <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                <td>Status</td>
                <td>{{ $mahasiswa->status }}</td>
            </tr>
        </table>
    </div>
    
    <table class="tagihan">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No. Tagihan</th>
                <th>Jenis</th>
                <th>Periode</th>
                <th>Jatuh Tempo</th>
                <th class="text-right">Nominal</th>
                <th class="text-right">Dibayar</th>
                <th class="text-right">Sisa</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNominal = 0; $totalDibayar = 0; $totalSisa = 0; @endphp
            @forelse($tagihan as $index => $t)
            @php
                $totalNominal += $t->nominal_tagihan;
                $totalDibayar += $t->jumlah_dibayar;
                $totalSisa += $t->sisa_tagihan;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $t->no_tagihan }}</td>
                <td>{{ $t->jenis_tagihan }}</td>
                <td>{{ $t->tahunAkademik->nama_lengkap ?? '-' }}</td>
                <td>{{ $t->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right">{{ number_format($t->nominal_tagihan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($t->jumlah_dibayar, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($t->status == 'lunas')
                    <span class="badge badge-success">Lunas</span>
                    @elseif($t->jatuh_tempo && $t->jatuh_tempo->isPast())
                    <span class="badge badge-danger">Jatuh Tempo</span>
                    @else
                    <span class="badge badge-warning">Belum Lunas</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada tagihan</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f0f0f0; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalDibayar, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="summary">
        <table>
            <tr>
                <td style="width: 70%;">Total Tagihan</td>
                <td class="text-right"><strong>Rp {{ number_format($totalNominal, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td class="text-right" style="color: #22c55e;"><strong>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Sisa Tagihan</td>
                <td class="text-right" style="color: #ef4444;"><strong>Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }} WIB</p>
        <p>Dokumen ini digenerate secara otomatis oleh sistem {{ setting('app_name', 'SIAKAD') }}</p>
    </div>
</body>
</html>
