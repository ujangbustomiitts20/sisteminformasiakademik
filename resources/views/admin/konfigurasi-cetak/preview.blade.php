@extends('cetak.layouts.master')

@section('title', 'Preview - ' . $konfigurasiCetak->nama)

@section('page-margin', $konfigurasiCetak->getMarginCss())

@section('content')
    @if($konfigurasiCetak->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $konfigurasiCetak->judul ?? $konfigurasiCetak->nama])
    @endif

    <div class="doc-title">
        <h3>{{ $konfigurasiCetak->judul ?? $konfigurasiCetak->nama }}</h3>
        <p>Nomor: XXX/UN/AKAD/{{ date('Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><strong>NAMA MAHASISWA CONTOH</strong></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>2024001001</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>Teknik Informatika</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td>Fakultas Teknik</td>
        </tr>
    </table>

    <div class="section-title">Contoh Data Tabel</div>
    <table class="bordered striped">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Kode</th>
                <th>Nama Mata Kuliah</th>
                <th width="50">SKS</th>
                <th width="60">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>INF101</td>
                <td>Algoritma dan Pemrograman</td>
                <td class="text-center">3</td>
                <td class="text-center"><span class="badge badge-success">A</span></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>INF102</td>
                <td>Basis Data</td>
                <td class="text-center">3</td>
                <td class="text-center"><span class="badge badge-success">A</span></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>INF103</td>
                <td>Struktur Data</td>
                <td class="text-center">3</td>
                <td class="text-center"><span class="badge badge-primary">B+</span></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>INF104</td>
                <td>Pemrograman Web</td>
                <td class="text-center">3</td>
                <td class="text-center"><span class="badge badge-primary">B</span></td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>INF105</td>
                <td>Jaringan Komputer</td>
                <td class="text-center">3</td>
                <td class="text-center"><span class="badge badge-info">B-</span></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total SKS</th>
                <th class="text-center">15</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="stats-container">
        <div class="stats-box">
            <h3>15</h3>
            <small>Total SKS</small>
        </div>
        <div class="stats-box">
            <h3>3.65</h3>
            <small>IPS</small>
        </div>
        <div class="stats-box">
            <h3>3.50</h3>
            <small>IPK</small>
        </div>
    </div>

    @if($konfigurasiCetak->catatan_kaki)
    <div class="mt-4">
        <p class="text-small text-muted">{{ $konfigurasiCetak->catatan_kaki }}</p>
    </div>
    @endif

    @if($konfigurasiCetak->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $konfigurasiCetak->jabatan_ttd ?? 'Pejabat Berwenang',
            'nama' => $konfigurasiCetak->nama_ttd ?? 'Nama Penandatangan',
            'nip' => $konfigurasiCetak->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($konfigurasiCetak->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
