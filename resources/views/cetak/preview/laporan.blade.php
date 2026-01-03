@extends('cetak.layouts.master')

@section('title', 'Preview Laporan')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $judul ?? 'LAPORAN'])
    @endif

    <div class="doc-title">
        <h3>{{ $judul ?? 'LAPORAN REKAP DATA' }}</h3>
        <p>{{ $periode ?? '' }}</p>
    </div>

    <table class="bordered striped">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Program Studi</th>
                <th width="100">Mahasiswa Aktif</th>
                <th width="100">Mahasiswa Cuti</th>
                <th width="100">Mahasiswa Lulus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td class="text-center">{{ $item->no }}</td>
                <td>{{ $item->prodi }}</td>
                <td class="text-center">{{ number_format($item->mahasiswa_aktif) }}</td>
                <td class="text-center">{{ number_format($item->mahasiswa_cuti) }}</td>
                <td class="text-center">{{ number_format($item->mahasiswa_lulus) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th class="text-center">{{ number_format($total->mahasiswa_aktif) }}</th>
                <th class="text-center">{{ number_format($total->mahasiswa_cuti) }}</th>
                <th class="text-center">{{ number_format($total->mahasiswa_lulus) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="stats-container mt-4">
        <div class="stats-box">
            <h3>{{ number_format($total->mahasiswa_aktif) }}</h3>
            <small>Total Aktif</small>
        </div>
        <div class="stats-box">
            <h3>{{ number_format($total->mahasiswa_cuti) }}</h3>
            <small>Total Cuti</small>
        </div>
        <div class="stats-box">
            <h3>{{ number_format($total->mahasiswa_lulus) }}</h3>
            <small>Total Lulus</small>
        </div>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    <div class="mt-4">
        <p class="text-small">Dicetak pada: {{ $tanggal_cetak }}</p>
    </div>

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Kepala Bagian Akademik',
            'nama' => $config->nama_ttd ?? 'Nama Pejabat',
            'nip' => $config->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
