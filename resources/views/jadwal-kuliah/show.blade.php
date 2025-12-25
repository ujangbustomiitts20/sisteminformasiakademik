@extends('layouts.app')

@section('title', 'Detail Jadwal Kuliah')

@section('content')
<div class="page-title">
    <h4>Detail Jadwal Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jadwal-kuliah.index') }}">Jadwal Kuliah</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar3 me-2"></i>Informasi Jadwal
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Mata Kuliah</td>
                        <td>:</td>
                        <td>
                            <strong>{{ $jadwalKuliah->mataKuliah->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $jadwalKuliah->mataKuliah->kode ?? '' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS</td>
                        <td>:</td>
                        <td><span class="badge bg-primary">{{ $jadwalKuliah->mataKuliah->sks ?? 0 }} SKS</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kelas</td>
                        <td>:</td>
                        <td><span class="badge bg-secondary">{{ $jadwalKuliah->kelas }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen</td>
                        <td>:</td>
                        <td>{{ $jadwalKuliah->dosen->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hari</td>
                        <td>:</td>
                        <td>{{ $jadwalKuliah->hari }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jam</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ruangan</td>
                        <td>:</td>
                        <td>{{ $jadwalKuliah->ruangan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kuota</td>
                        <td>:</td>
                        <td>{{ $jadwalKuliah->kuota ?? '-' }} orang</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>:</td>
                        <td>{{ $jadwalKuliah->tahunAkademik->nama_lengkap ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>Daftar Mahasiswa Terdaftar</span>
                <span class="badge bg-primary">{{ $jadwalKuliah->krs->count() ?? 0 }} mahasiswa</span>
            </div>
            <div class="card-body">
                @if($jadwalKuliah->krs && $jadwalKuliah->krs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="120">NIM</th>
                                <th>Nama</th>
                                <th width="120">Status</th>
                                <th width="80">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalKuliah->krs as $index => $krs)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $krs->mahasiswa->nim ?? '-' }}</code></td>
                                <td>{{ $krs->mahasiswa->nama ?? '-' }}</td>
                                <td>
                                    @if($krs->status == 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                    @elseif($krs->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @else
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($krs->nilai)
                                    <span class="badge bg-{{ in_array($krs->nilai->huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($krs->nilai->huruf, ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                        {{ $krs->nilai->huruf }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada mahasiswa terdaftar</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('jadwal-kuliah.edit', $jadwalKuliah) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection
