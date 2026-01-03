@extends('cetak.layouts.master')

@section('title', 'Preview KHS')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'KARTU HASIL STUDI'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'KARTU HASIL STUDI (KHS)' }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 120px;">NIM</td>
            <td style="width: 10px;">:</td>
            <td style="width: 200px;">{{ $mahasiswa->nim }}</td>
            <td style="width: 100px;">Semester</td>
            <td style="width: 10px;">:</td>
            <td>{{ $mahasiswa->semester_aktif }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><strong>{{ $mahasiswa->nama }}</strong></td>
            <td>Tahun Akademik</td>
            <td>:</td>
            <td>{{ $tahunAkademik->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->nama }}</td>
            <td>Dosen Wali</td>
            <td>:</td>
            <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="bordered striped">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="60">Kode</th>
                <th>Mata Kuliah</th>
                <th width="35">SKS</th>
                <th width="55">Nilai Angka</th>
                <th width="40">Huruf</th>
                <th width="40">Bobot</th>
                <th width="55">SKS×Bobot</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBobot = 0; $totalSksTerisi = 0; @endphp
            @foreach($krs as $index => $k)
            @php
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $bobot = $k->nilai?->bobot ?? 0;
                $sksBobot = $sks * $bobot;
                $totalBobot += $sksBobot;
                if ($k->nilai) $totalSksTerisi += $sks;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->kode }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                <td class="text-center">{{ $sks }}</td>
                <td class="text-center">{{ $k->nilai?->nilai_akhir ?? '-' }}</td>
                <td class="text-center">
                    @if($k->nilai)
                        <span class="badge {{ in_array($k->nilai->huruf, ['A', 'A-']) ? 'badge-success' : (in_array($k->nilai->huruf, ['B+', 'B']) ? 'badge-primary' : 'badge-info') }}">
                            {{ $k->nilai->huruf }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">{{ $k->nilai ? number_format($bobot, 2) : '-' }}</td>
                <td class="text-center">{{ $k->nilai ? number_format($sksBobot, 2) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-center">{{ $totalSks }}</th>
                <th colspan="3"></th>
                <th class="text-center">{{ number_format($totalBobot, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="stats-container">
        <div class="stats-box">
            <h3>{{ $totalSks }}</h3>
            <small>Total SKS</small>
        </div>
        <div class="stats-box">
            <h3>{{ $totalSksTerisi }}</h3>
            <small>SKS Lulus</small>
        </div>
        <div class="stats-box">
            <h3>{{ number_format($ips, 2) }}</h3>
            <small>IPS</small>
        </div>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Kepala Bagian Akademik',
            'nama' => $config->nama_ttd ?? 'Nama Penandatangan',
            'nip' => $config->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
