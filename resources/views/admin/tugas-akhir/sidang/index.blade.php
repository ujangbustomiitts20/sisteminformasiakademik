@extends('layouts.app')

@section('title', 'Sidang Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Sidang Tugas Akhir</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sidang TA</li>
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

    <!-- Stats Cards -->
    <div class="row mb-4">
        @php
            $stats = \App\Models\SidangTA::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "diajukan" THEN 1 ELSE 0 END) as diajukan,
                SUM(CASE WHEN status = "dijadwalkan" THEN 1 ELSE 0 END) as dijadwalkan,
                SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN hasil = "lulus" THEN 1 ELSE 0 END) as lulus,
                SUM(CASE WHEN hasil = "lulus_revisi" THEN 1 ELSE 0 END) as lulus_revisi,
                SUM(CASE WHEN hasil = "tidak_lulus" THEN 1 ELSE 0 END) as tidak_lulus
            ')->first();
        @endphp
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Sidang</h6>
                            <h3 class="mb-0">{{ $stats->total ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-mortarboard fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Menunggu Jadwal</h6>
                            <h3 class="mb-0">{{ $stats->diajukan ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-calendar-plus fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Terjadwal</h6>
                            <h3 class="mb-0">{{ $stats->dijadwalkan ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Lulus</h6>
                            <h3 class="mb-0">{{ ($stats->lulus ?? 0) + ($stats->lulus_revisi ?? 0) }}</h3>
                        </div>
                        <i class="bi bi-trophy fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.tugas-akhir.sidang.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\SidangTA::getStatusOptions() as $key => $val)
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
                    <a href="{{ route('admin.tugas-akhir.sidang.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @forelse($sidangs as $key => $sidang)
            <div class="border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-1 text-center">
                        <span class="badge bg-secondary fs-6">{{ $sidangs->firstItem() + $key }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Mahasiswa</small>
                        <div class="fw-bold">{{ $sidang->tugasAkhir->mahasiswa->nama ?? '-' }}</div>
                        <code>{{ $sidang->tugasAkhir->mahasiswa->nim ?? '-' }}</code>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Judul TA</small>
                        <div>{{ Str::limit($sidang->tugasAkhir->judul ?? '-', 80) }}</div>
                        <small class="text-muted">{{ $sidang->nomor_sidang }}</small>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Jadwal</small>
                        @if($sidang->tanggal)
                        <div>{{ $sidang->tanggal->format('d/m/Y') }}</div>
                        <small>{{ $sidang->waktu_mulai ? \Carbon\Carbon::parse($sidang->waktu_mulai)->format('H:i') : '-' }} | {{ $sidang->ruangan ?: '-' }}</small>
                        @else
                        <div class="text-muted">Belum dijadwalkan</div>
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        <div class="mb-2">
                            <span class="badge bg-{{ $sidang->status_badge }}">{{ $sidang->status_label }}</span>
                            @if($sidang->hasil)
                            <span class="badge bg-{{ $sidang->hasil == 'lulus' ? 'success' : ($sidang->hasil == 'lulus_revisi' ? 'warning' : 'danger') }}">{{ ucfirst(str_replace('_', ' ', $sidang->hasil)) }}</span>
                            @endif
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $sidang->hashid }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if($sidang->status == 'diajukan')
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#jadwalModal{{ $sidang->hashid }}">
                                <i class="bi bi-calendar-plus"></i>
                            </button>
                            @endif
                            @if($sidang->status == 'dijadwalkan' || $sidang->status == 'berlangsung')
                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#nilaiModal{{ $sidang->hashid }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
                        
                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $sidang->hashid }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Sidang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Informasi Mahasiswa</h6>
                                                <table class="table table-sm">
                                                    <tr><td>NIM</td><td>{{ $sidang->tugasAkhir->mahasiswa->nim ?? '-' }}</td></tr>
                                                    <tr><td>Nama</td><td>{{ $sidang->tugasAkhir->mahasiswa->nama ?? '-' }}</td></tr>
                                                    <tr><td>Program Studi</td><td>{{ $sidang->tugasAkhir->mahasiswa->programStudi->nama ?? '-' }}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Informasi Sidang</h6>
                                                <table class="table table-sm">
                                                    <tr><td>Tanggal</td><td>{{ $sidang->tanggal?->format('d F Y') ?? '-' }}</td></tr>
                                                    <tr><td>Waktu</td><td>{{ $sidang->waktu_mulai ?? '-' }} - {{ $sidang->waktu_selesai ?? '-' }}</td></tr>
                                                    <tr><td>Ruangan</td><td>{{ $sidang->ruangan ?? '-' }}</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6>Judul Tugas Akhir</h6>
                                        <p>{{ $sidang->tugasAkhir->judul ?? '-' }}</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Pembimbing</h6>
                                                <ul class="list-unstyled">
                                                    <li>1. {{ $sidang->tugasAkhir->pembimbing1->nama ?? '-' }}</li>
                                                    <li>2. {{ $sidang->tugasAkhir->pembimbing2->nama ?? '-' }}</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Tim Penguji</h6>
                                                <ul class="list-unstyled">
                                                    <li>Ketua: {{ $sidang->ketuaPenguji->nama ?? '-' }}</li>
                                                    <li>Penguji 1: {{ $sidang->penguji1->nama ?? '-' }}</li>
                                                    <li>Penguji 2: {{ $sidang->penguji2->nama ?? '-' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                        @if($sidang->nilai_akhir)
                                        <hr>
                                        <h6>Penilaian</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    <tr><td>Nilai Presentasi</td><td>{{ $sidang->nilai_presentasi ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Penguasaan Materi</td><td>{{ $sidang->nilai_penguasaan_materi ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Tanya Jawab</td><td>{{ $sidang->nilai_tanya_jawab ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Dokumen</td><td>{{ $sidang->nilai_dokumen ?? '-' }}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm">
                                                    <tr><td>Nilai Ketua Penguji</td><td>{{ $sidang->nilai_ketua ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Penguji 1</td><td>{{ $sidang->nilai_penguji_1 ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Penguji 2</td><td>{{ $sidang->nilai_penguji_2 ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Pembimbing 1</td><td>{{ $sidang->nilai_pembimbing_1 ?? '-' }}</td></tr>
                                                    <tr><td>Nilai Pembimbing 2</td><td>{{ $sidang->nilai_pembimbing_2 ?? '-' }}</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                        <table class="table table-sm">
                                            <tr class="fw-bold table-primary"><td>Nilai Akhir</td><td>{{ number_format($sidang->nilai_akhir, 2) }}</td></tr>
                                            <tr class="fw-bold"><td>Grade</td><td>{{ $sidang->grade }}</td></tr>
                                            <tr class="fw-bold"><td>Hasil</td><td>{{ ucfirst(str_replace('_', ' ', $sidang->hasil)) }}</td></tr>
                                        </table>
                                        @if($sidang->revisi_selesai)
                                        <div class="alert alert-success">
                                            <i class="bi bi-check-circle me-1"></i>Revisi telah selesai pada {{ $sidang->tanggal_revisi_selesai?->format('d F Y') }}
                                        </div>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Jadwal Modal -->
                        @if($sidang->status == 'diajukan')
                        <div class="modal fade" id="jadwalModal{{ $sidang->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.tugas-akhir.sidang.jadwalkan', $sidang->tugasAkhir->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Jadwalkan Sidang</h5>
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
                                                <label class="form-label">Ketua Penguji</label>
                                                <select name="ketua_penguji_id" class="form-select" required>
                                                    <option value="">-- Pilih Ketua --</option>
                                                    @foreach($dosens ?? [] as $dosen)
                                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                                    @endforeach
                                                </select>
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
                        @if($sidang->status == 'dijadwalkan' || $sidang->status == 'berlangsung')
                        <div class="modal fade" id="nilaiModal{{ $sidang->hashid }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.tugas-akhir.sidang.nilai', $sidang->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Input Nilai Sidang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6>Nilai Komponen</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-3">
                                                    <label class="form-label">Presentasi</label>
                                                    <input type="number" name="nilai_presentasi" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_presentasi }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Penguasaan Materi</label>
                                                    <input type="number" name="nilai_penguasaan_materi" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_penguasaan_materi }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Tanya Jawab</label>
                                                    <input type="number" name="nilai_tanya_jawab" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_tanya_jawab }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Dokumen</label>
                                                    <input type="number" name="nilai_dokumen" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_dokumen }}" required>
                                                </div>
                                            </div>
                                            
                                            <h6>Nilai Penguji & Pembimbing</h6>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Nilai Ketua Penguji</label>
                                                    <input type="number" name="nilai_ketua" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_ketua }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Nilai Penguji 1</label>
                                                    <input type="number" name="nilai_penguji_1" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_penguji_1 }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Nilai Penguji 2</label>
                                                    <input type="number" name="nilai_penguji_2" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_penguji_2 }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nilai Pembimbing 1</label>
                                                    <input type="number" name="nilai_pembimbing_1" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_pembimbing_1 }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nilai Pembimbing 2</label>
                                                    <input type="number" name="nilai_pembimbing_2" class="form-control" 
                                                        min="0" max="100" value="{{ $sidang->nilai_pembimbing_2 }}" required>
                                                </div>
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Hasil</label>
                                                    <select name="hasil" class="form-select" required>
                                                        <option value="">-- Pilih Hasil --</option>
                                                        <option value="lulus" {{ $sidang->hasil == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                                        <option value="lulus_revisi" {{ $sidang->hasil == 'lulus_revisi' ? 'selected' : '' }}>Lulus dengan Revisi</option>
                                                        <option value="tidak_lulus" {{ $sidang->hasil == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Deadline Revisi</label>
                                                    <input type="date" name="deadline_revisi" class="form-control" value="{{ $sidang->deadline_revisi?->format('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Catatan Revisi</label>
                                                    <textarea name="catatan_revisi" class="form-control" rows="3">{{ $sidang->catatan_revisi }}</textarea>
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
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 d-block mb-2 text-muted"></i>
                            <p class="text-muted">Belum ada data sidang tugas akhir</p>
                        </div>
                        @endforelse
            
            {{ $sidangs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
