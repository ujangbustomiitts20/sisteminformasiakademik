@extends('cetak.layouts.master')

@section('title', 'Preview Daftar Hadir')

@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @else
        @include('cetak.components.header-simple', ['title' => $config->judul ?? 'DAFTAR HADIR PERKULIAHAN'])
    @endif

    <div class="doc-title">
        <h3>{{ $config->judul ?? 'DAFTAR HADIR PERKULIAHAN' }}</h3>
    </div>

    <table class="info-table" style="font-size: 10px;">
        <tr>
            <td style="width: 100px;">Mata Kuliah</td>
            <td style="width: 10px;">:</td>
            <td style="width: 250px;"><strong>{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</strong></td>
            <td style="width: 80px;">Tahun Akademik</td>
            <td style="width: 10px;">:</td>
            <td>{{ $tahunAkademik->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $kelas }}</td>
            <td>SKS</td>
            <td>:</td>
            <td>{{ $mataKuliah->sks }}</td>
        </tr>
        <tr>
            <td>Dosen Pengampu</td>
            <td>:</td>
            <td colspan="4">{{ $dosen->nama }} ({{ $dosen->nidn }})</td>
        </tr>
    </table>

    <table class="bordered" style="font-size: 9px; margin-top: 10px;">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="70">NIM</th>
                <th width="120">Nama Mahasiswa</th>
                @for($i = 1; $i <= $pertemuan; $i++)
                <th width="18">{{ $i }}</th>
                @endfor
                <th width="25">H</th>
                <th width="25">I</th>
                <th width="25">A</th>
                <th width="30">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswaList as $index => $mhs)
            @php
                $countH = collect($mhs->kehadiran)->filter(fn($v) => $v == 'H')->count();
                $countI = collect($mhs->kehadiran)->filter(fn($v) => $v == 'I')->count();
                $countA = collect($mhs->kehadiran)->filter(fn($v) => $v == 'A')->count();
                $persentase = $pertemuan > 0 ? round(($countH / $pertemuan) * 100) : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->nama }}</td>
                @for($i = 1; $i <= $pertemuan; $i++)
                <td class="text-center" style="background: {{ $mhs->kehadiran[$i] == 'H' ? '#d4edda' : ($mhs->kehadiran[$i] == 'I' ? '#fff3cd' : '#f8d7da') }}">
                    {{ $mhs->kehadiran[$i] }}
                </td>
                @endfor
                <td class="text-center" style="background: #d4edda;">{{ $countH }}</td>
                <td class="text-center" style="background: #fff3cd;">{{ $countI }}</td>
                <td class="text-center" style="background: #f8d7da;">{{ $countA }}</td>
                <td class="text-center text-bold">{{ $persentase }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3" style="font-size: 9px;">
        <p><strong>Keterangan:</strong> H = Hadir, I = Izin, A = Alpa (Tanpa Keterangan)</p>
    </div>

    @if($config->catatan_kaki)
    <div class="mt-2">
        <p class="text-small text-muted">{{ $config->catatan_kaki }}</p>
    </div>
    @endif

    @if($config->tampilkan_ttd)
        @include('cetak.components.signature-double', [
            'left' => [
                'title' => 'Mengetahui,',
                'jabatan' => 'Ketua Program Studi',
                'nama' => 'Nama Kaprodi',
                'nip' => null,
            ],
            'right' => [
                'jabatan' => $config->jabatan_ttd ?? 'Dosen Pengampu',
                'nama' => $config->nama_ttd ?? $dosen->nama,
                'nip' => $config->nip_ttd,
            ],
            'kota' => setting('kota_institusi', 'Jakarta')
        ])
    @endif

    @if($config->tampilkan_footer)
        @include('cetak.components.footer')
    @endif
@endsection
