@extends('cetak.layouts.master')

@section('title', 'Preview Surat Cuti')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'SURAT KETERANGAN CUTI'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'SURAT KETERANGAN CUTI AKADEMIK' }}</h3>
        <p>Nomor: {{ $cuti->no_surat }}</p>
    </div>

    <div style="text-align: justify; line-height: 1.8;">
        <p style="margin-bottom: 15px;">Yang bertanda tangan di bawah ini, {{ $config->jabatan_ttd ?? 'Wakil Rektor Bidang Akademik' }} {{ setting('institution_name', 'Universitas') }}, dengan ini menerangkan bahwa:</p>

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
                <td>Angkatan</td>
                <td>:</td>
                <td>{{ $mahasiswa->angkatan }}</td>
            </tr>
        </table>

        <p style="margin-bottom: 15px;">Mahasiswa tersebut di atas telah disetujui untuk mengambil <strong>Cuti Akademik</strong> dengan rincian sebagai berikut:</p>

        <table class="info-table" style="margin: 20px 0;">
            <tr>
                <td style="width: 150px;">Lama Cuti</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $cuti->lama_cuti }}</strong></td>
            </tr>
            <tr>
                <td>Semester Mulai</td>
                <td>:</td>
                <td>{{ $cuti->semester_mulai }}</td>
            </tr>
            <tr>
                <td>Semester Selesai</td>
                <td>:</td>
                <td>{{ $cuti->semester_selesai }}</td>
            </tr>
            <tr>
                <td>Alasan Cuti</td>
                <td>:</td>
                <td>{{ $cuti->alasan }}</td>
            </tr>
            <tr>
                <td>Tanggal Disetujui</td>
                <td>:</td>
                <td>{{ $cuti->tanggal_disetujui }}</td>
            </tr>
        </table>

        <p style="margin-bottom: 15px;">Demikian surat keterangan ini dibuat untuk digunakan sebagaimana mestinya.</p>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Wakil Rektor Bidang Akademik',
            'nama' => $config->nama_ttd ?? 'Nama Pejabat',
            'nip' => $config->nip_ttd,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
