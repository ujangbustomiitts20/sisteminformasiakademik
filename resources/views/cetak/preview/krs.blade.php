@extends('cetak.layouts.master')

@section('title', 'Preview KRS')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'KARTU RENCANA STUDI'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'KARTU RENCANA STUDI (KRS)' }}</h3>
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
                <th width="35">Kelas</th>
                <th width="120">Dosen Pengampu</th>
                <th width="80">Hari/Jam</th>
                <th width="50">Ruang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($krs as $index => $k)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->kode }}</td>
                <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                <td class="text-center">{{ $k->jadwalKuliah->mataKuliah->sks }}</td>
                <td class="text-center">{{ $k->jadwalKuliah->kelas }}</td>
                <td>{{ $k->jadwalKuliah->dosen->nama }}</td>
                <td class="text-center">{{ substr($k->jadwalKuliah->hari, 0, 3) }}, {{ $k->jadwalKuliah->jam_mulai }}</td>
                <td class="text-center">{{ $k->jadwalKuliah->ruangan->kode }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total SKS</th>
                <th class="text-center">{{ $totalSks }}</th>
                <th colspan="4"></th>
            </tr>
        </tfoot>
    </table>

    @if($config->catatan_kaki)
    <div class="mt-3">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd ?? 'Dosen Pembimbing Akademik',
            'nama' => $config->nama_ttd ?? $mahasiswa->dosenWali->nama ?? 'Nama Dosen',
            'nip' => $config->nip_ttd ?? $mahasiswa->dosenWali->nip ?? null,
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
