@extends('cetak.layouts.master')

@section('title', 'Preview Invoice')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'INVOICE'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'INVOICE TAGIHAN' }}</h3>
        <p>No: {{ $tagihan->no_tagihan }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 120px;">NIM</td>
            <td style="width: 10px;">:</td>
            <td style="width: 200px;">{{ $mahasiswa->nim }}</td>
            <td style="width: 100px;">Tanggal</td>
            <td style="width: 10px;">:</td>
            <td>{{ $tagihan->tanggal }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><strong>{{ $mahasiswa->nama }}</strong></td>
            <td>Jatuh Tempo</td>
            <td>:</td>
            <td><span class="badge badge-warning">{{ $tagihan->jatuh_tempo }}</span></td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->nama }}</td>
            <td>Status</td>
            <td>:</td>
            <td>
                <span class="badge {{ $tagihan->status == 'Lunas' ? 'badge-success' : ($tagihan->status == 'Cicilan' ? 'badge-info' : 'badge-danger') }}">
                    {{ $tagihan->status }}
                </span>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Tagihan</div>
    
    <table class="bordered">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Komponen</th>
                <th width="150">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rincian as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Subtotal</th>
                <th class="text-right">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</th>
            </tr>
            @if($tagihan->diskon > 0)
            <tr>
                <td colspan="2" class="text-right">Diskon</td>
                <td class="text-right text-success">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($tagihan->denda > 0)
            <tr>
                <td colspan="2" class="text-right">Denda</td>
                <td class="text-right text-danger">+ Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <th colspan="2" class="text-right">Total Tagihan</th>
                <th class="text-right">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</th>
            </tr>
            @if($tagihan->jumlah_dibayar > 0)
            <tr>
                <td colspan="2" class="text-right">Sudah Dibayar</td>
                <td class="text-right">Rp {{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr style="background: #ffe5e5;">
                <th colspan="2" class="text-right">SISA TAGIHAN</th>
                <th class="text-right">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="mt-3">
        <p class="text-small"><strong>Terbilang:</strong> <em>{{ ucwords(\App\Helpers\Terbilang::make($tagihan->sisa_tagihan)) }} Rupiah</em></p>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Bagian Keuangan',
            'nama' => $config->nama_ttd ?? 'Nama Petugas',
            'nip' => $config->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
