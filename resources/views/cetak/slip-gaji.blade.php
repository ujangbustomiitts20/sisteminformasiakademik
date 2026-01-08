<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $slipGaji->no_slip }} - {{ setting('institution_name', setting('nama_institusi', 'Institut Teknologi Tangerang Selatan')) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
        }
        .slip-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .info-section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            padding: 5px;
            background: #e0e0e0;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ccc;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 60px 0 5px;
        }
        .note {
            margin-top: 20px;
            padding: 10px;
            border: 1px dashed #999;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            🖨️ Cetak Slip Gaji
        </button>
    </div>

    <div class="header">
        @if(setting('institution_logo'))
        <img src="{{ asset('storage/' . setting('institution_logo')) }}" alt="Logo" style="height: 60px; margin-bottom: 10px;">
        @endif
        @if(setting('institution_yayasan'))
        <h1 style="font-size: 14px; font-weight: normal;">{{ setting('institution_yayasan') }}</h1>
        @endif
        <h1>{{ strtoupper(setting('institution_name', 'INSTITUSI PENDIDIKAN')) }}</h1>
        <p>{{ setting('institution_address', 'Alamat Institusi') }}</p>
        <p style="font-size: 10px;">
            @if(setting('contact_phone'))Telp: {{ setting('contact_phone') }}@endif
            @if(setting('contact_email')) | Email: {{ setting('contact_email') }}@endif
            @if(setting('contact_website')) | {{ setting('contact_website') }}@endif
        </p>
    </div>

    <div class="slip-title">SLIP GAJI</div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">No. Slip</span>
            <span class="info-value">: {{ $slipGaji->no_slip }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-value">: {{ $slipGaji->periode }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">: {{ $slipGaji->tanggal_slip->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Nama</span>
            <span class="info-value">: {{ $slipGaji->nama_pegawai }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ $slipGaji->dosen ? 'NIDN' : 'NIP' }}</span>
            <span class="info-value">: {{ $slipGaji->dosen?->nidn ?? $slipGaji->pegawai?->nip ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jabatan/Unit</span>
            <span class="info-value">: {{ $slipGaji->dosen?->programStudi?->nama ?? $slipGaji->pegawai?->unitKerja?->nama ?? '-' }}</span>
        </div>
    </div>

    <div class="section-title">A. PENDAPATAN</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Komponen</th>
                <th width="30%" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Gaji Pokok</td>
                <td class="text-right">{{ number_format($slipGaji->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            @foreach($pendapatan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 2 }}</td>
                <td>{{ $item->nama_komponen }}</td>
                <td class="text-right">{{ number_format($item->nilai, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Pendapatan</th>
                <th class="text-right">{{ number_format($slipGaji->gaji_kotor, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">B. POTONGAN</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Komponen</th>
                <th width="30%" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($potongan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_komponen }}</td>
                <td class="text-right">{{ number_format($item->nilai, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada potongan</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Potongan</th>
                <th class="text-right">{{ number_format($slipGaji->total_potongan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span>Total Pendapatan (A)</span>
            <span>Rp {{ number_format($slipGaji->gaji_kotor, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Potongan (B)</span>
            <span>Rp {{ number_format($slipGaji->total_potongan, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row summary-total">
            <span>GAJI BERSIH (A - B)</span>
            <span>Rp {{ number_format($slipGaji->gaji_bersih, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($slipGaji->catatan)
    <div class="note">
        <strong>Catatan:</strong> {{ $slipGaji->catatan }}
    </div>
    @endif

    <div class="footer">
        <div class="signature">
            <p>Penerima</p>
            <div class="signature-line"></div>
            <p>{{ $slipGaji->nama_pegawai }}</p>
        </div>
        <div class="signature">
            @php
                $pejabatKeuangan = pejabat('kepala_keuangan');
            @endphp
            <p>{{ setting('kota_institusi', 'Jakarta') }}, {{ $slipGaji->tanggal_slip->translatedFormat('d F Y') }}</p>
            <p>{{ $pejabatKeuangan?->jabatan ?? setting('kepala_keuangan_jabatan', 'Kepala Bagian Keuangan') }}</p>
            @if($pejabatKeuangan?->tanda_tangan)
                <img src="{{ $pejabatKeuangan->tanda_tangan_url }}" alt="Tanda Tangan" style="height: 60px; margin: 10px 0;">
            @else
                <div class="signature-line"></div>
            @endif
            <p>{{ $pejabatKeuangan?->nama_lengkap ?? setting('kepala_keuangan_nama', '(...........................)') }}</p>
            @if($pejabatKeuangan?->nip)
                <p style="font-size: 10px;">NIP. {{ $pejabatKeuangan->nip }}</p>
            @endif
        </div>
    </div>

    <div class="note" style="margin-top: 30px;">
        <p>* Slip gaji ini dicetak secara otomatis dan sah tanpa tanda tangan basah.</p>
        <p>* Jika ada pertanyaan terkait slip gaji, silakan hubungi bagian SDM/Keuangan {{ setting('institution_name', '') }}.</p>
        @if(setting('contact_phone'))
        <p>* Kontak: {{ setting('contact_phone') }} @if(setting('contact_email'))| {{ setting('contact_email') }}@endif</p>
        @endif
    </div>
</body>
</html>
