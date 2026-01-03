@extends('cetak.layouts.master')

@section('title', 'Preview Kartu Ujian')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'KARTU PESERTA UJIAN'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'KARTU PESERTA UJIAN' }}</h3>
        <p>{{ $periode_ujian }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 70%;">
                <table class="info-table">
                    <tr>
                        <td style="width: 120px;">NIM</td>
                        <td style="width: 10px;">:</td>
                        <td>{{ $mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $mahasiswa->nama }}</strong></td>
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
                        <td>Semester</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->semester_aktif }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 30%; text-align: center; vertical-align: top;">
                <div style="border: 1px solid #333; width: 90px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    @if($foto_url)
                        <img src="{{ $foto_url }}" alt="Foto" style="max-width: 88px; max-height: 118px;">
                    @else
                        <span style="font-size: 10px; color: #999;">Pas Foto<br>3x4</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Jadwal Ujian</div>

    <table class="bordered striped">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Hari/Tanggal</th>
                <th width="70">Waktu</th>
                <th width="55">Kode</th>
                <th>Mata Kuliah</th>
                <th width="70">Ruangan</th>
                <th width="100">Paraf Pengawas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalUjian as $index => $jadwal)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $jadwal->hari }},<br>{{ $jadwal->tanggal }}</td>
                <td class="text-center">{{ $jadwal->jam_mulai }} -<br>{{ $jadwal->jam_selesai }}</td>
                <td>{{ $jadwal->mataKuliah->kode }}</td>
                <td>{{ $jadwal->mataKuliah->nama }}</td>
                <td class="text-center">{{ $jadwal->ruangan }}</td>
                <td style="height: 40px;"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        <p class="text-small"><strong>Perhatian:</strong></p>
        <ol class="text-small" style="margin-left: 15px; padding-left: 0;">
            <li>Kartu ujian ini wajib dibawa setiap kali ujian</li>
            <li>Mahasiswa yang tidak membawa kartu ujian tidak diperkenankan mengikuti ujian</li>
            <li>Harap hadir 15 menit sebelum ujian dimulai</li>
            <li>Pakaian rapi dan sopan</li>
        </ol>
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
