@extends('layouts.app')

@section('title', 'Detail Dosen')

@section('content')
<div class="page-title">
    <h4>Detail Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Profil Dosen -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar avatar-xl bg-primary text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; line-height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $dosen->nama }}</h5>
                <p class="text-muted mb-2">{{ $dosen->nidn ?? '-' }}</p>
                <span class="badge bg-{{ $dosen->status == 'Aktif' ? 'success' : 'secondary' }}">
                    {{ $dosen->status ?? 'Aktif' }}
                </span>
            </div>
            <hr class="my-0">
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" width="40%">Email</td>
                        <td>{{ $dosen->email ?? ($dosen->user->email ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $dosen->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $dosen->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Golongan</td>
                        <td>{{ $dosen->golongan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $dosen->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pendidikan</td>
                        <td>{{ $dosen->pendidikan_terakhir ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bidang Keahlian</td>
                        <td>{{ $dosen->bidang_keahlian ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Statistik -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="mb-0 text-primary">{{ $stats['jumlah_matakuliah'] }}</h4>
                        <small class="text-muted">Mata Kuliah</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="mb-0 text-info">{{ $stats['jumlah_kelas'] }}</h4>
                        <small class="text-muted">Kelas Diampu</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-success">{{ $stats['mahasiswa_bimbingan'] }}</h4>
                        <small class="text-muted">Mhs Perwalian</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-warning">{{ $stats['tugas_akhir_bimbingan'] }}</h4>
                        <small class="text-muted">Bimbingan TA</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aksi -->
        <div class="card">
            <div class="card-body">
                <a href="{{ route('dekan.dosen.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Jadwal Mengajar -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Jadwal Mengajar {{ $tahunAktif->nama ?? '' }}</h6>
            </div>
            <div class="card-body">
                @if($jadwalMengajar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalMengajar as $jadwal)
                            <tr>
                                <td>{{ $jadwal->hari }}</td>
                                <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                <td>{{ $jadwal->mataKuliah->nama ?? '-' }}</td>
                                <td>{{ $jadwal->kelas ?? '-' }}</td>
                                <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada jadwal mengajar semester ini</p>
                @endif
            </div>
        </div>

        <!-- Mahasiswa Perwalian -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Mahasiswa Perwalian</h6>
                <span class="badge bg-primary">{{ $mahasiswaBimbingan->count() }} mahasiswa</span>
            </div>
            <div class="card-body">
                @if($mahasiswaBimbingan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Angkatan</th>
                                <th>Semester</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswaBimbingan->take(10) as $mhs)
                            <tr>
                                <td>{{ $mhs->nim }}</td>
                                <td>{{ $mhs->nama }}</td>
                                <td>{{ $mhs->angkatan }}</td>
                                <td>{{ $mhs->semester_aktif ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : 'secondary' }}">
                                        {{ $mhs->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($mahasiswaBimbingan->count() > 10)
                    <p class="text-muted text-center mb-0 mt-2">
                        <small>Menampilkan 10 dari {{ $mahasiswaBimbingan->count() }} mahasiswa</small>
                    </p>
                    @endif
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada mahasiswa perwalian</p>
                @endif
            </div>
        </div>

        <!-- Bimbingan Tugas Akhir -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>Bimbingan Tugas Akhir</h6>
                <span class="badge bg-warning">{{ $bimbinganTA->count() }} bimbingan</span>
            </div>
            <div class="card-body">
                @if($bimbinganTA->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Mahasiswa</th>
                                <th>Judul</th>
                                <th>Sebagai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bimbinganTA as $ta)
                            <tr>
                                <td>{{ $ta->mahasiswa->nim ?? '-' }}</td>
                                <td>{{ $ta->mahasiswa->nama ?? '-' }}</td>
                                <td title="{{ $ta->judul }}">
                                    {{ Str::limit($ta->judul, 40) }}
                                </td>
                                <td>
                                    @if($ta->pembimbing_1_id == $dosen->id)
                                        <span class="badge bg-primary">Pembimbing 1</span>
                                    @else
                                        <span class="badge bg-info">Pembimbing 2</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'diajukan' => 'warning',
                                            'judul_disetujui' => 'success',
                                            'judul_ditolak' => 'danger',
                                            'proposal_diajukan' => 'info',
                                            'proposal_disetujui' => 'success',
                                            'penelitian' => 'primary',
                                            'sidang_diajukan' => 'info',
                                            'lulus' => 'success',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$ta->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $ta->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada bimbingan tugas akhir aktif</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
