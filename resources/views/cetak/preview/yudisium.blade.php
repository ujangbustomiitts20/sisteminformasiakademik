@extends('cetak.layouts.master')

@section('title', 'Preview Yudisium')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'SURAT KETERANGAN LULUS'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'SURAT KETERANGAN LULUS' }}</h3>
        <p>Nomor: {{ $yudisium->no_surat }}</p>
    </div>

    <div style="text-align: justify; line-height: 1.8;">
        <p style="margin-bottom: 15px;">Yang bertanda tangan di bawah ini, {{ $config->jabatan_ttd ?? 'Rektor' }} {{ setting('institution_name', 'Universitas') }}, dengan ini menerangkan bahwa:</p>

        <table class="info-table" style="margin: 20px 0;">
            <tr>
                <td style="width: 150px;">Nama</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $mahasiswa->nama }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Tempat/Tgl Lahir</td>
                <td>:</td>
                <td>{{ $mahasiswa->tempat_lahir }}, {{ $mahasiswa->tanggal_lahir }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $mahasiswa->programStudi->nama }}</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>:</td>
                <td>{{ $mahasiswa->programStudi->fakultas->nama }}</td>
            </tr>
            <tr>
                <td>Jenjang</td>
                <td>:</td>
                <td>{{ $mahasiswa->programStudi->jenjang }}</td>
            </tr>
        </table>

        <p style="margin-bottom: 15px;">Telah <strong>DINYATAKAN LULUS</strong> dalam Yudisium yang diselenggarakan pada tanggal <strong>{{ $yudisium->tanggal_yudisium }}</strong> dengan rincian sebagai berikut:</p>

        <table class="info-table" style="margin: 20px 0;">
            <tr>
                <td style="width: 180px;">Tanggal Lulus</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $yudisium->tanggal_lulus }}</strong></td>
            </tr>
            <tr>
                <td>Indeks Prestasi Kumulatif</td>
                <td>:</td>
                <td><strong>{{ number_format($yudisium->ipk, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Total SKS</td>
                <td>:</td>
                <td><strong>{{ $yudisium->total_sks }} SKS</strong></td>
            </tr>
            <tr>
                <td>Predikat Kelulusan</td>
                <td>:</td>
                <td><strong>{{ $yudisium->predikat }}</strong></td>
            </tr>
            <tr>
                <td>Judul Tugas Akhir/Skripsi</td>
                <td>:</td>
                <td><em>"{{ $yudisium->judul_skripsi }}"</em></td>
            </tr>
        </table>

        <p style="margin-bottom: 15px;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Rektor',
            'nama' => $config->nama_ttd ?? 'Nama Rektor',
            'nip' => $config->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
