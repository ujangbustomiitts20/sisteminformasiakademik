<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SKP - {{ $skp->no_skp }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 14px;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 12px;
            font-weight: normal;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-label {
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: left;
        }
        table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            margin: 15px 0 8px;
            padding: 5px;
            background: #e0e0e0;
        }
        .nilai-box {
            display: inline-block;
            padding: 8px 15px;
            border: 2px solid #333;
            font-weight: bold;
            font-size: 14px;
            margin: 5px 10px;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ccc;
        }
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-space {
            height: 60px;
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
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">
            🖨️ Cetak
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">
            ✖ Tutup
        </button>
    </div>

    <div class="header">
        <h1>{{ strtoupper(setting('nama_institusi', 'INSTITUT TEKNOLOGI')) }}</h1>
        <h2>{{ setting('alamat_institusi', '') }}</h2>
    </div>

    <div class="title">
        SASARAN KINERJA PEGAWAI (SKP)<br>
        TAHUN {{ $skp->tahun }}
    </div>

    <div class="info-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <strong>I. PEJABAT PENILAI</strong>
                    <table style="border: none; margin-top: 5px;">
                        <tr>
                            <td style="border: none; width: 100px;">Nama</td>
                            <td style="border: none;">: {{ $skp->atasan_penilai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Jabatan</td>
                            <td style="border: none;">: {{ $skp->jabatan_penilai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Unit Kerja</td>
                            <td style="border: none;">: {{ $skp->unit_kerja ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <strong>II. PEGAWAI YANG DINILAI</strong>
                    <table style="border: none; margin-top: 5px;">
                        <tr>
                            <td style="border: none; width: 100px;">Nama</td>
                            <td style="border: none;">: {{ $skp->nama_pegawai }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">NIDN/NIP</td>
                            <td style="border: none;">: {{ $skp->dosen->nidn ?? $skp->dosen->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Unit Kerja</td>
                            <td style="border: none;">: {{ $skp->dosen->programStudi->nama ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">III. TARGET KINERJA</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th>Uraian Kegiatan</th>
                <th class="text-center" width="80">Target</th>
                <th class="text-center" width="80">Realisasi</th>
                <th class="text-center" width="60">Satuan</th>
                <th class="text-center" width="60">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($skp->targetSkp as $index => $target)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $target->uraian_kegiatan }}</td>
                <td class="text-center">{{ $target->target_kuantitas ?? '-' }}</td>
                <td class="text-center">{{ $target->realisasi_kuantitas ?? '-' }}</td>
                <td class="text-center">{{ $target->satuan ?? '-' }}</td>
                <td class="text-center">{{ $target->nilai_capaian ? number_format($target->nilai_capaian, 2) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada target</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">IV. PENILAIAN PERILAKU KERJA</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th>Aspek Perilaku</th>
                <th class="text-center" width="80">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Orientasi Pelayanan</td>
                <td class="text-center">{{ $skp->orientasi_pelayanan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Integritas</td>
                <td class="text-center">{{ $skp->integritas ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Komitmen</td>
                <td class="text-center">{{ $skp->komitmen ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Disiplin</td>
                <td class="text-center">{{ $skp->disiplin ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Kerjasama</td>
                <td class="text-center">{{ $skp->kerjasama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Kepemimpinan</td>
                <td class="text-center">{{ $skp->kepemimpinan ?? '-' }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Rata-rata Nilai Perilaku</th>
                <th class="text-center">{{ $skp->nilai_perilaku ? number_format($skp->nilai_perilaku, 2) : '-' }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">V. REKAPITULASI PENILAIAN</div>
    <div class="summary">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; width: 40%;">Nilai SKP (60%)</td>
                <td style="border: none; width: 30%;">: {{ $skp->nilai_skp ? number_format($skp->nilai_skp, 2) : '-' }}</td>
                <td style="border: none; width: 30%;">x 60% = {{ $skp->nilai_skp ? number_format($skp->nilai_skp * 0.6, 2) : '-' }}</td>
            </tr>
            <tr>
                <td style="border: none;">Nilai Perilaku Kerja (40%)</td>
                <td style="border: none;">: {{ $skp->nilai_perilaku ? number_format($skp->nilai_perilaku, 2) : '-' }}</td>
                <td style="border: none;">x 40% = {{ $skp->nilai_perilaku ? number_format($skp->nilai_perilaku * 0.4, 2) : '-' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding-top: 10px;"><strong>NILAI PRESTASI KERJA</strong></td>
                <td style="border: none; padding-top: 10px;" colspan="2">
                    <strong>: {{ $skp->nilai_akhir ? number_format($skp->nilai_akhir, 2) : '-' }}</strong>
                    @if($skp->predikat)
                    <strong>({{ \App\Models\SkpPegawai::PREDIKAT[$skp->predikat] ?? $skp->predikat }})</strong>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($skp->catatan)
    <div style="margin-top: 15px;">
        <strong>Catatan/Saran:</strong>
        <p style="margin-top: 5px;">{{ $skp->catatan }}</p>
    </div>
    @endif

    <div class="footer">
        <div class="signature-row">
            <div class="signature-box">
                <p>Pejabat Penilai</p>
                <div class="signature-space"></div>
                <p><strong>{{ $skp->atasan_penilai ?? '................................' }}</strong></p>
                <p>NIP. ................................</p>
            </div>
            <div class="signature-box">
                <p>{{ setting('kota_institusi', 'Kota') }}, {{ $skp->tanggal_penilaian ? $skp->tanggal_penilaian->format('d F Y') : now()->format('d F Y') }}</p>
                <p>Pegawai Yang Dinilai</p>
                <div class="signature-space"></div>
                <p><strong>{{ $skp->nama_pegawai }}</strong></p>
                <p>{{ $skp->dosen->nidn ? 'NIDN. ' . $skp->dosen->nidn : ($skp->dosen->nip ? 'NIP. ' . $skp->dosen->nip : '') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
