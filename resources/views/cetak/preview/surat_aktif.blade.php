@extends('cetak.layouts.master')

@section('title', 'Preview Surat Keterangan Aktif')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'SURAT KETERANGAN AKTIF'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'SURAT KETERANGAN AKTIF KULIAH' }}</h3>
        <p>Nomor: {{ $surat->no_surat }}</p>
    </div>

    <div style="text-align: justify; line-height: 1.8;">
        <p style="margin-bottom: 15px;">Yang bertanda tangan di bawah ini, {{ $config->jabatan_ttd ?? 'Kepala Bagian Akademik' }} {{ setting('institution_name', 'Universitas') }}, dengan ini menerangkan bahwa:</p>

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
                <td>{{ $mahasiswa->programStudi->nama }} ({{ $mahasiswa->programStudi->jenjang }})</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>:</td>
                <td>{{ $mahasiswa->programStudi->fakultas->nama }}</td>
            </tr>
            <tr>
                <td>Angkatan</td>
                <td>:</td>
                <td>{{ $mahasiswa->angkatan }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $mahasiswa->semester_aktif }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $mahasiswa->alamat }}</td>
            </tr>
        </table>

        <p style="margin-bottom: 15px;">Adalah <strong>benar</strong> sebagai Mahasiswa aktif pada {{ setting('institution_name', 'Universitas') }} sampai dengan saat surat ini diterbitkan.</p>

        <p style="margin-bottom: 15px;">Surat keterangan ini dibuat untuk keperluan: <strong>{{ $surat->keperluan }}</strong></p>

        <p style="margin-bottom: 15px;">Surat ini berlaku sampai dengan: <strong>{{ $surat->berlaku_sampai }}</strong></p>

        <p style="margin-bottom: 15px;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

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
