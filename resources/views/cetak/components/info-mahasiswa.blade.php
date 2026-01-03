{{-- 
    Komponen Info Mahasiswa
    Usage: @include('cetak.components.info-mahasiswa', ['mahasiswa' => $mahasiswa])
--}}
<table class="info-table">
    <tr>
        <td>Nama</td>
        <td>:</td>
        <td><strong>{{ $mahasiswa->nama ?? '-' }}</strong></td>
    </tr>
    <tr>
        <td>NIM</td>
        <td>:</td>
        <td>{{ $mahasiswa->nim ?? '-' }}</td>
    </tr>
    <tr>
        <td>Program Studi</td>
        <td>:</td>
        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
    </tr>
    <tr>
        <td>Fakultas</td>
        <td>:</td>
        <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
    </tr>
    @if(isset($showAngkatan) && $showAngkatan)
    <tr>
        <td>Angkatan</td>
        <td>:</td>
        <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
    </tr>
    @endif
    @if(isset($showSemester) && $showSemester)
    <tr>
        <td>Semester</td>
        <td>:</td>
        <td>{{ $mahasiswa->semester ?? '-' }}</td>
    </tr>
    @endif
    @if(isset($showDosenWali) && $showDosenWali && $mahasiswa->dosenWali)
    <tr>
        <td>Dosen Wali</td>
        <td>:</td>
        <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
    </tr>
    @endif
    @if(isset($tahunAkademik))
    <tr>
        <td>Tahun Akademik</td>
        <td>:</td>
        <td>{{ $tahunAkademik->tahun ?? '-' }} - Semester {{ $tahunAkademik->semester ?? '-' }}</td>
    </tr>
    @endif
</table>
