@extends('layouts.app')

@section('title', 'Seminar Proposal')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Seminar Proposal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Seminar Proposal</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.tugas-akhir.seminar.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\SeminarProposal::getStatusOptions() as $key => $val)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="NIM / Nama / Judul..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.tugas-akhir.seminar.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Seminar</th>
                            <th>Mahasiswa</th>
                            <th>Judul TA</th>
                            <th>Jadwal</th>
                            <th>Ruangan</th>
                            <th>Status</th>
                            <th>Hasil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seminars as $key => $seminar)
                        <tr>
                            <td>{{ $seminars->firstItem() + $key }}</td>
                            <td><code>{{ $seminar->nomor_seminar }}</code></td>
                            <td>
                                <strong>{{ $seminar->tugasAkhir->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $seminar->tugasAkhir->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>
                                <span title="{{ $seminar->tugasAkhir->judul ?? '-' }}">
                                    {{ Str::limit($seminar->tugasAkhir->judul ?? '-', 50) }}
                                </span>
                            </td>
                            <td>
                                @if($seminar->tanggal)
                                {{ $seminar->tanggal->format('d/m/Y') }}<br>
                                <small>{{ $seminar->waktu_mulai }} - {{ $seminar->waktu_selesai }}</small>
                                @else
                                <span class="text-muted">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td>{{ $seminar->ruangan ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $seminar->status_badge }}">
                                    {{ $seminar->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($seminar->hasil)
                                <span class="badge bg-{{ $seminar->hasil == 'lulus' ? 'success' : ($seminar->hasil == 'lulus_revisi' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $seminar->hasil)) }}
                                </span>
                                @if($seminar->nilai_akhir)
                                <br><small class="text-muted">Nilai: {{ number_format($seminar->nilai_akhir, 2) }}</small>
                                @endif
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $seminar->hashid }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($seminar->status == 'diajukan')
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#jadwalModal{{ $seminar->hashid }}">
                                        <i class="bi bi-calendar-plus"></i>
                                    </button>
                                    @endif
                                    @if($seminar->status == 'dijadwalkan' || $seminar->status == 'berlangsung')
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#nilaiModal{{ $seminar->hashid }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $seminar->hashid }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Seminar</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Informasi Mahasiswa</h6>
                                                <table class="table table-sm">
                                                    <tr><td>NIM</td><td>{{ $seminar->tugasAkhir->mahasiswa->nim ?? '-' }}</td></tr>
                                                    <tr><td>Nama</td><td>{{ $seminar->tugasAkhir->mahasiswa->nama ?? '-' }}</td></tr>
                                                    <tr><td>Program Studi</td><td>{{ $seminar->tugasAkhir->mahasiswa->programStudi->nama ?? '-' }}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Informasi Seminar</h6>
                                                <table class="table table-sm">
                                                    <tr><td>Tanggal</td><td>{{ $seminar->tanggal?->format('d F Y') ?? '-' }}</td></tr>
                                                    <tr><td>Waktu</td><td>{{ $seminar->waktu_mulai ?? '-' }} - {{ $seminar->waktu_selesai ?? '-' }}</td></tr>
                                                    <tr><td>Ruangan</td><td>{{ $seminar->ruangan ?? '-' }}</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6>Judul Tugas Akhir</h6>
                                        <p>{{ $seminar->tugasAkhir->judul ?? '-' }}</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Pembimbing</h6>
                                                <ul class="list-unstyled">
                                                    <li>1. {{ $seminar->tugasAkhir->pembimbing1->nama ?? '-' }}</li>
                                                    <li>2. {{ $seminar->tugasAkhir->pembimbing2->nama ?? '-' }}</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Penguji</h6>
                                                <ul class="list-unstyled">
                                                    <li>1. {{ $seminar->penguji1->nama ?? '-' }}</li>
                                                    <li>2. {{ $seminar->penguji2->nama ?? '-' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                        @if($seminar->nilai_akhir)
                                        <hr>
                                        <h6>Penilaian</h6>
                                        <table class="table table-sm">
                                            <tr><td>Nilai Pembimbing 1</td><td>{{ $seminar->nilai_pembimbing_1 ?? '-' }}</td></tr>
                                            <tr><td>Nilai Pembimbing 2</td><td>{{ $seminar->nilai_pembimbing_2 ?? '-' }}</td></tr>
                                            <tr><td>Nilai Penguji 1</td><td>{{ $seminar->nilai_penguji_1 ?? '-' }}</td></tr>
                                            <tr><td>Nilai Penguji 2</td><td>{{ $seminar->nilai_penguji_2 ?? '-' }}</td></tr>
                                            <tr class="fw-bold"><td>Nilai Akhir</td><td>{{ number_format($seminar->nilai_akhir, 2) }}</td></tr>
                                            <tr class="fw-bold"><td>Hasil</td><td>{{ ucfirst(str_replace('_', ' ', $seminar->hasil)) }}</td></tr>
                                        </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Jadwal Modal -->
                        @if($seminar->status == 'diajukan')
                        <div class="modal fade" id="jadwalModal{{ $seminar->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.tugas-akhir.seminar.jadwalkan', $seminar->tugasAkhir->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Jadwalkan Seminar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Waktu Mulai</label>
                                                    <input type="time" name="waktu_mulai" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Waktu Selesai</label>
                                                    <input type="time" name="waktu_selesai" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="mb-3 mt-3">
                                                <label class="form-label">Ruangan</label>
                                                <input type="text" name="ruangan" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Penguji 1</label>
                                                <select name="penguji_1_id" class="form-select" required>
                                                    <option value="">-- Pilih Penguji 1 --</option>
                                                    @foreach($dosens ?? [] as $dosen)
                                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Penguji 2</label>
                                                <select name="penguji_2_id" class="form-select" required>
                                                    <option value="">-- Pilih Penguji 2 --</option>
                                                    @foreach($dosens ?? [] as $dosen)
                                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Nilai Modal -->
                        @if($seminar->status == 'dijadwalkan' || $seminar->status == 'berlangsung')
                        <div class="modal fade" id="nilaiModal{{ $seminar->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.tugas-akhir.seminar.nilai', $seminar->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Input Nilai Seminar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Nilai Pembimbing 1</label>
                                                    <input type="number" name="nilai_pembimbing_1" class="form-control" 
                                                        min="0" max="100" value="{{ $seminar->nilai_pembimbing_1 }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nilai Pembimbing 2</label>
                                                    <input type="number" name="nilai_pembimbing_2" class="form-control" 
                                                        min="0" max="100" value="{{ $seminar->nilai_pembimbing_2 }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nilai Penguji 1</label>
                                                    <input type="number" name="nilai_penguji_1" class="form-control" 
                                                        min="0" max="100" value="{{ $seminar->nilai_penguji_1 }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nilai Penguji 2</label>
                                                    <input type="number" name="nilai_penguji_2" class="form-control" 
                                                        min="0" max="100" value="{{ $seminar->nilai_penguji_2 }}" required>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Hasil</label>
                                                    <select name="hasil" class="form-select" required>
                                                        <option value="">-- Pilih Hasil --</option>
                                                        <option value="lulus" {{ $seminar->hasil == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                                        <option value="lulus_revisi" {{ $seminar->hasil == 'lulus_revisi' ? 'selected' : '' }}>Lulus dengan Revisi</option>
                                                        <option value="tidak_lulus" {{ $seminar->hasil == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Catatan Revisi</label>
                                                    <textarea name="catatan_revisi" class="form-control" rows="3">{{ $seminar->catatan_revisi }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Belum ada data seminar proposal
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $seminars->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
