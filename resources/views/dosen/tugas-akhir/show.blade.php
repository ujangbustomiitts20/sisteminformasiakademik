@extends('layouts.app')

@section('title', 'Detail Mahasiswa Bimbingan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Mahasiswa Bimbingan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dosen.tugas-akhir.index') }}">Bimbingan TA</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Info Mahasiswa & TA -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="35%">NIM</td>
                            <td>: <strong>{{ $tugasAkhir->mahasiswa->nim ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: <strong>{{ $tugasAkhir->mahasiswa->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Prodi</td>
                            <td>: {{ $tugasAkhir->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>No HP</td>
                            <td>: {{ $tugasAkhir->mahasiswa->no_telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: {{ $tugasAkhir->mahasiswa->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Status TA</h6>
                </div>
                <div class="card-body text-center">
                    @php
                        $badgeClass = match($tugasAkhir->status) {
                            'draft' => 'secondary',
                            'diajukan' => 'info',
                            'judul_disetujui' => 'success',
                            'judul_ditolak' => 'danger',
                            'proposal_diajukan' => 'info',
                            'penelitian' => 'primary',
                            'sidang_diajukan' => 'info',
                            'sidang_dijadwalkan' => 'warning',
                            'lulus', 'selesai' => 'success',
                            'lulus_revisi' => 'warning',
                            'tidak_lulus' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <h4><span class="badge bg-{{ $badgeClass }}">{{ $tugasAkhir->getStatusLabel() }}</span></h4>
                    
                    <div class="mt-3">
                        <small class="text-muted">Peran Anda:</small><br>
                        <span class="badge bg-primary">
                            {{ $dosen->id == $tugasAkhir->pembimbing_1_id ? 'Pembimbing 1' : 'Pembimbing 2' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Dosen Pembimbing</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Pembimbing 1:</strong><br>
                        {{ $tugasAkhir->pembimbing1->nama ?? '-' }}
                    </p>
                    <p class="mb-0">
                        <strong>Pembimbing 2:</strong><br>
                        {{ $tugasAkhir->pembimbing2->nama ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Detail TA -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Detail Tugas Akhir</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $tugasAkhir->judul }}</h5>
                    <p class="text-muted">{{ $tugasAkhir->nomor_ta ?? 'Belum ada nomor' }}</p>
                    
                    @if($tugasAkhir->bidang_kajian)
                    <p><span class="badge bg-info">{{ $tugasAkhir->bidang_kajian }}</span></p>
                    @endif
                    
                    @if($tugasAkhir->abstrak)
                    <h6 class="mt-4">Abstrak</h6>
                    <p>{{ $tugasAkhir->abstrak }}</p>
                    @endif
                    
                    @if($tugasAkhir->metodologi)
                    <h6 class="mt-4">Metodologi</h6>
                    <p>{{ $tugasAkhir->metodologi }}</p>
                    @endif
                </div>
            </div>

            <!-- Riwayat Bimbingan -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Riwayat Bimbingan</h6>
                    <div>
                        <span class="badge bg-primary me-2">{{ $tugasAkhir->bimbingan->count() }} kali</span>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#buatJadwalModal">
                            <i class="bi bi-plus"></i> Buat Jadwal
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($tugasAkhir->bimbingan as $bimbingan)
                    <div class="card mb-2 {{ $bimbingan->dosen_id == $dosen->id ? 'border-primary' : '' }}">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $bimbingan->tanggal->format('d M Y') }}</strong>
                                    @if($bimbingan->waktu_mulai)
                                    <span class="text-muted">{{ $bimbingan->waktu_mulai }}</span>
                                    @endif
                                    <span class="badge bg-{{ $bimbingan->status == 'selesai' ? 'success' : ($bimbingan->status == 'dibatalkan' ? 'danger' : 'warning') }} ms-2">
                                        {{ ucfirst($bimbingan->status) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">Dengan: {{ $bimbingan->dosen->nama ?? '-' }}</small>
                                </div>
                                @if($bimbingan->persentase_progress)
                                <span class="badge bg-info">{{ $bimbingan->persentase_progress }}%</span>
                                @endif
                            </div>
                            <p class="mb-1 mt-2"><strong>Materi:</strong> {{ $bimbingan->materi_bimbingan }}</p>
                            @if($bimbingan->hasil_bimbingan)
                            <p class="mb-1"><strong>Hasil:</strong> {{ $bimbingan->hasil_bimbingan }}</p>
                            @endif
                            @if($bimbingan->catatan_dosen)
                            <p class="mb-0 text-muted"><small><strong>Catatan:</strong> {{ $bimbingan->catatan_dosen }}</small></p>
                            @endif

                            @if($bimbingan->dosen_id == $dosen->id && $bimbingan->status == 'dijadwalkan')
                            <hr class="my-2">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#inputBimbinganModal{{ $bimbingan->id }}">
                                    <i class="bi bi-check"></i> Input Hasil
                                </button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $bimbingan->id }}">
                                    <i class="bi bi-calendar"></i> Reschedule
                                </button>
                            </div>

                            <!-- Modal Input Bimbingan -->
                            <div class="modal fade" id="inputBimbinganModal{{ $bimbingan->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('dosen.tugas-akhir.bimbingan.input', $bimbingan->hashid) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Input Hasil Bimbingan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Hasil Bimbingan <span class="text-danger">*</span></label>
                                                    <textarea name="hasil_bimbingan" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="catatan_dosen" class="form-control" rows="2"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Rencana Selanjutnya</label>
                                                    <textarea name="rencana_selanjutnya" class="form-control" rows="2"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Progress (%) <span class="text-danger">*</span></label>
                                                    <input type="number" name="persentase_progress" class="form-control" min="0" max="100" required>
                                                </div>
                                                <input type="hidden" name="status" value="selesai">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Reschedule -->
                            <div class="modal fade" id="rescheduleModal{{ $bimbingan->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('dosen.tugas-akhir.bimbingan.reschedule', $bimbingan->hashid) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Jadwalkan Ulang</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                                                    <input type="date" name="tanggal" class="form-control" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Waktu Mulai</label>
                                                            <input type="time" name="waktu_mulai" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Waktu Selesai</label>
                                                            <input type="time" name="waktu_selesai" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tempat</label>
                                                    <input type="text" name="tempat" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="catatan_dosen" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Jadwalkan Ulang</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Belum ada riwayat bimbingan</p>
                    @endforelse
                </div>
            </div>

            <!-- Sidang Info (jika ada) -->
            @if($tugasAkhir->sidang)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Sidang</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td>Tanggal</td>
                                    <td>: {{ $tugasAkhir->sidang->tanggal->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu</td>
                                    <td>: {{ $tugasAkhir->sidang->waktu_mulai }} - {{ $tugasAkhir->sidang->waktu_selesai }}</td>
                                </tr>
                                <tr>
                                    <td>Ruangan</td>
                                    <td>: {{ $tugasAkhir->sidang->ruangan }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>: <span class="badge bg-info">{{ $tugasAkhir->sidang->status }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Tim Penguji</h6>
                            <ul class="list-unstyled">
                                <li>Ketua: {{ $tugasAkhir->sidang->ketuaPenguji->nama ?? '-' }}</li>
                                <li>Penguji 1: {{ $tugasAkhir->sidang->penguji1->nama ?? '-' }}</li>
                                <li>Penguji 2: {{ $tugasAkhir->sidang->penguji2->nama ?? '-' }}</li>
                            </ul>
                        </div>
                    </div>

                    @if($tugasAkhir->sidang->nilai_akhir)
                    <hr>
                    <div class="text-center">
                        <h4>Nilai Akhir: <span class="badge bg-success">{{ number_format($tugasAkhir->sidang->nilai_akhir, 2) }}</span></h4>
                        <h5>Grade: <span class="badge bg-primary">{{ $tugasAkhir->sidang->grade ?? '-' }}</span></h5>
                    </div>
                    @endif

                    <!-- Revisi -->
                    @if($tugasAkhir->sidang->revisi && $tugasAkhir->sidang->revisi->count() > 0)
                    <hr>
                    <h6>Daftar Revisi</h6>
                    @foreach($tugasAkhir->sidang->revisi as $revisi)
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $revisi->dosen->nama ?? '-' }}</strong>
                                    <span class="badge bg-{{ $revisi->sudah_diperbaiki ? 'success' : 'warning' }}">
                                        {{ $revisi->sudah_diperbaiki ? 'Selesai' : 'Pending' }}
                                    </span>
                                </div>
                                @if($revisi->dosen_id == $dosen->id && !$revisi->sudah_diperbaiki)
                                <form action="{{ route('dosen.tugas-akhir.revisi.verifikasi', $revisi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check"></i> Verifikasi
                                    </button>
                                </form>
                                @endif
                            </div>
                            <p class="mb-0 mt-1">{{ $revisi->catatan_revisi }}</p>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Buat Jadwal Bimbingan -->
<div class="modal fade" id="buatJadwalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dosen.tugas-akhir.bimbingan.store', $tugasAkhir->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Buat Jadwal Bimbingan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Mahasiswa:</strong> {{ $tugasAkhir->mahasiswa->nama }}<br>
                        <strong>Judul:</strong> {{ Str::limit($tugasAkhir->judul, 50) }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tempat</label>
                        <input type="text" name="tempat" class="form-control" placeholder="Ruangan/Online">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Materi yang akan dibahas</label>
                        <textarea name="materi_bimbingan" class="form-control" rows="3" placeholder="Deskripsi topik/materi bimbingan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-plus me-1"></i>Buat Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
