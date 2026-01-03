@extends('cetak.layouts.master')

@section('title', 'Preview Kwitansi')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'KWITANSI'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'KWITANSI PEMBAYARAN' }}</h3>
        <p>No: {{ $transaksi->no_transaksi }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 150px;">Telah diterima dari</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $mahasiswa->nama }}</strong> ({{ $mahasiswa->nim }})</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->nama }} - {{ $mahasiswa->programStudi->fakultas->nama }}</td>
        </tr>
        <tr>
            <td>Uang Sejumlah</td>
            <td>:</td>
            <td><strong>Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Terbilang</td>
            <td>:</td>
            <td><em>{{ ucwords(\App\Helpers\Terbilang::make($transaksi->jumlah)) }} Rupiah</em></td>
        </tr>
        <tr>
            <td>Untuk Pembayaran</td>
            <td>:</td>
            <td>{{ $transaksi->keterangan }}</td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td>:</td>
            <td>{{ $transaksi->metode_pembayaran }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $transaksi->tanggal }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>
                <span class="badge badge-success">{{ $transaksi->status }}</span>
            </td>
        </tr>
    </table>

    <div class="mt-4" style="border: 2px solid #333; padding: 15px; text-align: center; background: #f9f9f9;">
        <p style="font-size: 12px; margin: 0;">JUMLAH YANG DITERIMA</p>
        <h2 style="font-size: 20px; margin: 10px 0; color: #333;">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</h2>
        <p style="font-size: 10px; margin: 0; color: #666;">{{ ucwords(\App\Helpers\Terbilang::make($transaksi->jumlah)) }} Rupiah</p>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Bendahara',
            'nama' => $config->nama_ttd ?? 'Nama Bendahara',
            'nip' => $config->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
