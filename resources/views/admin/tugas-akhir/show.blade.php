@extends('layouts.app')

@section('title', 'Detail Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Tugas Akhir</h1>
        <a href="{{ route('admin.tugas-akhir.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Info Mahasiswa & TA -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">NIM</td>
                            <td>{{ $tugasAkhir->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td>{{ $tugasAkhir->mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td>{{ $tugasAkhir->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $badgeClass = match($tugasAkhir->status) {
                                        'draft' => 'secondary',
                                        'diajukan' => 'warning',
                                        'judul_disetujui' => 'info',
                                        'judul_ditolak' => 'danger',
                                        'proposal_diajukan', 'proposal_revisi' => 'primary',
                                        'penelitian', 'penulisan' => 'info',
                                        'sidang_diajukan', 'sidang_dijadwalkan' => 'primary',
                                        'lulus', 'lulus_revisi', 'selesai' => 'success',
                                        'tidak_lulus' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $tugasAkhir->getStatusLabel() }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Pembimbing -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dosen Pembimbing</h6>
                </div>
                <div class="card-body">
                    <p><strong>Pembimbing 1:</strong><br>{{ $tugasAkhir->pembimbing1->nama ?? '-' }}</p>
                    <p class="mb-0"><strong>Pembimbing 2:</strong><br>{{ $tugasAkhir->pembimbing2->nama ?? '-' }}</p>
                </div>
            </div>

            <!-- Aksi -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    @if($tugasAkhir->status == 'diajukan')
                    <!-- Form Approval Judul -->
                    <form action="{{ route('admin.tugas-akhir.approval-judul', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pembimbing 1</label>
                            <select name="pembimbing_1_id" class="form-select" required>
                                <option value="">Pilih Pembimbing 1</option>
                                @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pembimbing 2</label>
                            <select name="pembimbing_2_id" class="form-select">
                                <option value="">Pilih Pembimbing 2 (opsional)</option>
                                @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan_pembimbing" class="form-control" rows="2"></textarea>
                        </div>
                        <input type="hidden" name="status" value="judul_disetujui">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Setujui Judul
                        </button>
                    </form>
                    <form action="{{ route('admin.tugas-akhir.approval-judul', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="judul_ditolak">
                        <div class="mb-3">
                            <textarea name="catatan_pembimbing" class="form-control" rows="2" placeholder="Alasan penolakan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-x-circle"></i> Tolak Judul
                        </button>
                    </form>
                    @endif

                    @if($tugasAkhir->status == 'judul_disetujui')
                    <!-- Mahasiswa perlu upload proposal -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Menunggu mahasiswa mengupload proposal dan mengajukan seminar.
                    </div>
                    <form action="{{ route('admin.tugas-akhir.update-status', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="proposal_diajukan">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-right"></i> Set ke Proposal Diajukan
                        </button>
                    </form>
                    @endif

                    @if($tugasAkhir->status == 'proposal_diajukan')
                    <!-- Jadwalkan Seminar -->
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#seminarModal">
                        <i class="bi bi-calendar-event"></i> Jadwalkan Seminar Proposal
                    </button>
                    @endif

                    @if($tugasAkhir->status == 'proposal_revisi')
                    <!-- Revisi proposal selesai -->
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Mahasiswa sedang melakukan revisi proposal.
                    </div>
                    <form action="{{ route('admin.tugas-akhir.update-status', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="penelitian">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check"></i> Revisi Selesai, Lanjut Penelitian
                        </button>
                    </form>
                    @endif

                    @if($tugasAkhir->status == 'penelitian')
                    <!-- Sedang penelitian -->
                    <div class="alert alert-info">
                        <i class="bi bi-flask"></i> Mahasiswa sedang dalam tahap penelitian.
                    </div>
                    <form action="{{ route('admin.tugas-akhir.update-status', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="sidang_diajukan">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-right"></i> Ajukan Sidang
                        </button>
                    </form>
                    @endif

                    @if($tugasAkhir->status == 'sidang_diajukan')
                    <!-- Jadwalkan Sidang -->
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#sidangModal">
                        <i class="bi bi-mortarboard"></i> Jadwalkan Sidang TA
                    </button>
                    @endif

                    @if($tugasAkhir->status == 'lulus_revisi')
                    <!-- Lulus dengan revisi -->
                    <div class="alert alert-warning">
                        <i class="bi bi-pencil"></i> Mahasiswa sedang menyelesaikan revisi sidang.
                    </div>
                    <form action="{{ route('admin.tugas-akhir.update-status', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Revisi Selesai, Tandai Selesai
                        </button>
                    </form>
                    @endif

                    @if($tugasAkhir->status == 'lulus')
                    <!-- Lulus langsung -->
                    <form action="{{ route('admin.tugas-akhir.update-status', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Tandai Selesai
                        </button>
                    </form>
                    @endif

                    @if($tugasAkhir->status == 'selesai')
                    <div class="alert alert-success">
                        <i class="bi bi-trophy"></i> Tugas Akhir telah selesai!
                    </div>
                    @endif

                    @if($tugasAkhir->status == 'judul_ditolak')
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle"></i> Judul ditolak. Mahasiswa dapat mengajukan judul baru.
                    </div>
                    @endif

                    @if($tugasAkhir->status == 'tidak_lulus')
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle"></i> Tidak lulus sidang. Mahasiswa perlu sidang ulang.
                    </div>
                    <form action="{{ route('admin.tugas-akhir.update-status', $tugasAkhir->hashid) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="sidang_diajukan">
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-arrow-repeat"></i> Ajukan Sidang Ulang
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Judul -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Tugas Akhir</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $tugasAkhir->judul }}</h5>
                    @if($tugasAkhir->bidang_kajian)
                    <p><span class="badge bg-info">{{ $tugasAkhir->bidang_kajian }}</span></p>
                    @endif
                    
                    @if($tugasAkhir->abstrak)
                    <h6 class="mt-4">Abstrak</h6>
                    <p>{!! nl2br(e($tugasAkhir->abstrak)) !!}</p>
                    @endif
                    
                    @if($tugasAkhir->latar_belakang)
                    <h6 class="mt-4">Latar Belakang</h6>
                    <p>{!! nl2br(e($tugasAkhir->latar_belakang)) !!}</p>
                    @endif

                    @if($tugasAkhir->rumusan_masalah)
                    <h6 class="mt-4">Rumusan Masalah</h6>
                    <p>{!! nl2br(e($tugasAkhir->rumusan_masalah)) !!}</p>
                    @endif

                    @if($tugasAkhir->metodologi)
                    <h6 class="mt-4">Metodologi</h6>
                    <p>{!! nl2br(e($tugasAkhir->metodologi)) !!}</p>
                    @endif

                    @if($tugasAkhir->catatan_pembimbing)
                    <div class="alert alert-info mt-4">
                        <strong>Catatan:</strong><br>
                        {{ $tugasAkhir->catatan_pembimbing }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Bimbingan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Bimbingan ({{ $tugasAkhir->bimbingan->count() }} kali)</h6>
                </div>
                <div class="card-body">
                    @if($tugasAkhir->bimbingan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pembimbing</th>
                                    <th>Materi</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tugasAkhir->bimbingan as $bimbingan)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($bimbingan->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $bimbingan->dosen->nama ?? '-' }}</td>
                                    <td>{{ Str::limit($bimbingan->materi_bimbingan, 50) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $bimbingan->persentase_progress }}%">
                                                {{ $bimbingan->persentase_progress }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($bimbingan->status == 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                        @elseif($bimbingan->status == 'dijadwalkan')
                                        <span class="badge bg-warning">Dijadwalkan</span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($bimbingan->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">Belum ada riwayat bimbingan.</p>
                    @endif
                </div>
            </div>

            <!-- Seminar Proposal -->
            @if($tugasAkhir->seminarProposal)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seminar Proposal</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tugasAkhir->seminarProposal->tanggal)->format('d/m/Y') }}</p>
                            <p><strong>Waktu:</strong> {{ $tugasAkhir->seminarProposal->waktu_mulai }} - {{ $tugasAkhir->seminarProposal->waktu_selesai }}</p>
                            <p><strong>Ruangan:</strong> {{ $tugasAkhir->seminarProposal->ruangan }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Penguji 1:</strong> {{ $tugasAkhir->seminarProposal->penguji1->nama ?? '-' }}</p>
                            <p><strong>Penguji 2:</strong> {{ $tugasAkhir->seminarProposal->penguji2->nama ?? '-' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $tugasAkhir->seminarProposal->status == 'selesai' ? 'success' : 'warning' }}">
                                    {{ ucfirst($tugasAkhir->seminarProposal->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($tugasAkhir->seminarProposal->nilai_akhir)
                    <hr>
                    <p><strong>Nilai Akhir:</strong> {{ number_format($tugasAkhir->seminarProposal->nilai_akhir, 2) }}</p>
                    <p><strong>Hasil:</strong> {{ ucfirst(str_replace('_', ' ', $tugasAkhir->seminarProposal->hasil)) }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Sidang -->
            @if($tugasAkhir->sidang)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sidang Tugas Akhir</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tugasAkhir->sidang->tanggal)->format('d/m/Y') }}</p>
                            <p><strong>Waktu:</strong> {{ $tugasAkhir->sidang->waktu_mulai }} - {{ $tugasAkhir->sidang->waktu_selesai }}</p>
                            <p><strong>Ruangan:</strong> {{ $tugasAkhir->sidang->ruangan }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ketua Penguji:</strong> {{ $tugasAkhir->sidang->ketuaPenguji->nama ?? '-' }}</p>
                            <p><strong>Penguji 1:</strong> {{ $tugasAkhir->sidang->penguji1->nama ?? '-' }}</p>
                            <p><strong>Penguji 2:</strong> {{ $tugasAkhir->sidang->penguji2->nama ?? '-' }}</p>
                        </div>
                    </div>
                    @if($tugasAkhir->sidang->nilai_akhir)
                    <hr>
                    <p><strong>Nilai Akhir:</strong> {{ number_format($tugasAkhir->sidang->nilai_akhir, 2) }} ({{ $tugasAkhir->sidang->grade ?? '-' }})</p>
                    <p><strong>Hasil:</strong> {{ ucfirst(str_replace('_', ' ', $tugasAkhir->sidang->hasil)) }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Jadwalkan Seminar -->
<div class="modal fade" id="seminarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tugas-akhir.seminar.jadwalkan', $tugasAkhir->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Jadwalkan Seminar Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ruangan</label>
                            <input type="text" name="ruangan" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penguji 1</label>
                        <select name="penguji_1_id" class="form-select" required>
                            <option value="">Pilih Penguji 1</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penguji 2</label>
                        <select name="penguji_2_id" class="form-select" required>
                            <option value="">Pilih Penguji 2</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Jadwalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Jadwalkan Sidang -->
<div class="modal fade" id="sidangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tugas-akhir.sidang.jadwalkan', $tugasAkhir->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Jadwalkan Sidang Tugas Akhir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ruangan</label>
                            <input type="text" name="ruangan" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ketua Penguji</label>
                        <select name="ketua_penguji_id" class="form-select" required>
                            <option value="">Pilih Ketua Penguji</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penguji 1</label>
                        <select name="penguji_1_id" class="form-select" required>
                            <option value="">Pilih Penguji 1</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penguji 2</label>
                        <select name="penguji_2_id" class="form-select" required>
                            <option value="">Pilih Penguji 2</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Jadwalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
