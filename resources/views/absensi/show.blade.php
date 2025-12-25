@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="page-title">
    <h4>Rekap Absensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li class="breadcrumb-item active">Rekap</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Kelas
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="120">Mata Kuliah</td>
                        <td>: <strong>{{ $jadwalKuliah->mataKuliah->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td>Kode</td>
                        <td>: {{ $jadwalKuliah->mataKuliah->kode }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $jadwalKuliah->kelas }}</td>
                    </tr>
                    <tr>
                        <td>Dosen</td>
                        <td>: {{ $jadwalKuliah->dosen->nama }}</td>
                    </tr>
                    <tr>
                        <td>SKS</td>
                        <td>: {{ $jadwalKuliah->mataKuliah->sks }}</td>
                    </tr>
                    <tr>
                        <td>Jadwal</td>
                        <td>: {{ $jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Ruangan</td>
                        <td>: {{ $jadwalKuliah->ruangan->nama }}</td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>: {{ $jadwalKuliah->tahunAkademik->nama_lengkap }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-plus me-2"></i>Input Absensi
            </div>
            <div class="card-body">
                <a href="{{ route('absensi.create', $jadwalKuliah) }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i>Input Manual
                </a>
                <a href="{{ route('absensi.kode', $jadwalKuliah) }}" class="btn btn-success w-100 mb-3">
                    <i class="bi bi-qr-code me-1"></i>Absensi Mandiri (Kode)
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-list-ol me-2"></i>Daftar Pertemuan
            </div>
            <div class="card-body">
                @if($pertemuan->isNotEmpty())
                <div class="list-group">
                    @foreach($pertemuan as $p)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Pertemuan {{ $p->pertemuan }}</strong>
                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</small>
                            @if($p->materi)
                            <br><small>{{ Str::limit($p->materi, 30) }}</small>
                            @endif
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('absensi.edit', [$jadwalKuliah, $p->pertemuan]) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('absensi.destroy', [$jadwalKuliah, $p->pertemuan]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus absensi pertemuan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">Belum ada pertemuan</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2"></i>Rekap Absensi Mahasiswa</span>
                <span class="badge bg-primary">{{ $mahasiswa->count() }} Mahasiswa</span>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($mahasiswa->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Belum ada mahasiswa yang mengambil mata kuliah ini.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-center" width="40">No</th>
                                <th rowspan="2" class="align-middle">NIM</th>
                                <th rowspan="2" class="align-middle">Nama</th>
                                <th colspan="{{ $pertemuan->count() ?: 1 }}" class="text-center">Pertemuan</th>
                                <th rowspan="2" class="align-middle text-center" width="60">%</th>
                            </tr>
                            <tr>
                                @forelse($pertemuan as $p)
                                <th class="text-center" width="35">{{ $p->pertemuan }}</th>
                                @empty
                                <th class="text-center">-</th>
                                @endforelse
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswa as $index => $krs)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $krs->mahasiswa->nim }}</td>
                                <td>{{ $krs->mahasiswa->nama }}</td>
                                @forelse($pertemuan as $p)
                                    @php
                                        $absensi = $krs->absensi->where('pertemuan', $p->pertemuan)->first();
                                    @endphp
                                    <td class="text-center">
                                        @if($absensi)
                                            @if($absensi->status == 'Hadir')
                                            <span class="badge bg-success" title="Hadir">H</span>
                                            @elseif($absensi->status == 'Izin')
                                            <span class="badge bg-info" title="Izin">I</span>
                                            @elseif($absensi->status == 'Sakit')
                                            <span class="badge bg-warning" title="Sakit">S</span>
                                            @else
                                            <span class="badge bg-danger" title="Alpha">A</span>
                                            @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @empty
                                <td class="text-center">-</td>
                                @endforelse
                                <td class="text-center">
                                    @php
                                        $persen = $krs->persentaseKehadiran();
                                    @endphp
                                    <span class="badge {{ $persen >= 75 ? 'bg-success' : ($persen >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $persen }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Keterangan:</strong>
                        <span class="badge bg-success">H</span> Hadir |
                        <span class="badge bg-info">I</span> Izin |
                        <span class="badge bg-warning">S</span> Sakit |
                        <span class="badge bg-danger">A</span> Alpha
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection
