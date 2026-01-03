@extends('cetak.layouts.master')

@section('title', 'Preview Transkrip')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'TRANSKRIP AKADEMIK'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'TRANSKRIP AKADEMIK' }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 100px;">NIM</td>
            <td style="width: 10px;">:</td>
            <td style="width: 180px;">{{ $mahasiswa->nim }}</td>
            <td style="width: 100px;">Angkatan</td>
            <td style="width: 10px;">:</td>
            <td>{{ $mahasiswa->angkatan }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><strong>{{ $mahasiswa->nama }}</strong></td>
            <td>Status</td>
            <td>:</td>
            <td>{{ $mahasiswa->status }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->nama }} ({{ $mahasiswa->programStudi->jenjang }})</td>
            <td>Tempat/Tgl Lahir</td>
            <td>:</td>
            <td>{{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir ?? '-' }}</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->fakultas->nama }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="bordered striped" style="font-size: 10px;">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="50">Kode</th>
                <th>Mata Kuliah</th>
                <th width="30">SKS</th>
                <th width="35">Huruf</th>
                <th width="35">Bobot</th>
                <th width="45">SKS×Bobot</th>
                <th width="70">Semester</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalBobot = 0; 
                $no = 1;
            @endphp
            @foreach($krs as $k)
            @php
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $bobot = $k->nilai->bobot;
                $sksBobot = $sks * $bobot;
                $totalBobot += $sksBobot;
            @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->kode }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                <td class="text-center">{{ $sks }}</td>
                <td class="text-center">{{ $k->nilai->huruf }}</td>
                <td class="text-center">{{ number_format($bobot, 2) }}</td>
                <td class="text-center">{{ number_format($sksBobot, 2) }}</td>
                <td class="text-center">{{ $k->jadwalKuliah->tahunAkademik->tahun }} {{ substr($k->jadwalKuliah->tahunAkademik->semester, 0, 1) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-center">{{ $totalSks }}</th>
                <th colspan="2"></th>
                <th class="text-center">{{ number_format($totalBobot, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <table style="width: auto; margin-top: 15px;">
        <tr>
            <td style="width: 200px;"><strong>Indeks Prestasi Kumulatif (IPK)</strong></td>
            <td>: <strong>{{ number_format($ipk, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Total SKS Lulus</td>
            <td>: {{ $totalSks }}</td>
        </tr>
        <tr>
            <td>Predikat Kelulusan</td>
            <td>: 
                @if($ipk >= 3.50) Dengan Pujian (Cum Laude)
                @elseif($ipk >= 3.00) Sangat Memuaskan
                @elseif($ipk >= 2.50) Memuaskan
                @else Cukup
                @endif
            </td>
        </tr>
    </table>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Dekan',
            'nama' => $config->nama_ttd ?? $mahasiswa->programStudi->fakultas->dekan ?? 'Nama Dekan',
            'nip' => $config->nip_ttd ?? $mahasiswa->programStudi->fakultas->nip_dekan ?? null,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
