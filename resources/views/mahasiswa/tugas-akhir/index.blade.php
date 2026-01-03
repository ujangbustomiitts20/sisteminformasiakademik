@extends('layouts.app')

@section('title', 'Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tugas Akhir / Skripsi Saya</h1>
        @if(!$tugasAkhir)
        <a href="{{ route('mahasiswa.tugas-akhir.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajukan Judul
        </a>
        @endif
    </div>

    @if(!$tugasAkhir)
    <!-- Belum ada pengajuan -->
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="bi bi-mortarboard display-1 text-muted"></i>
            <h4 class="mt-4">Belum Ada Pengajuan Tugas Akhir</h4>
            <p class="text-muted">Silakan ajukan judul tugas akhir Anda untuk memulai.</p>
            <a href="{{ route('mahasiswa.tugas-akhir.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Ajukan Judul Sekarang
            </a>
        </div>
    </div>
    @else
    <!-- Ada pengajuan -->
    <div class="row">
        <!-- Status & Info -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Tugas Akhir</h6>
                </div>
                <div class="card-body text-center">
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
                    <h3><span class="badge bg-{{ $badgeClass }}">{{ $tugasAkhir->getStatusLabel() }}</span></h3>
                    
                    <!-- Progress -->
                    <div class="mt-4">
                        <div class="progress" style="height: 30px;">
                            @php
                                $progress = match($tugasAkhir->status) {
                                    'draft' => 5,
                                    'diajukan' => 10,
                                    'judul_disetujui' => 20,
                                    'proposal_diajukan' => 30,
                                    'proposal_revisi' => 35,
                                    'penelitian' => 50,
                                    'penulisan' => 70,
                                    'sidang_diajukan' => 80,
                                    'sidang_dijadwalkan' => 85,
                                    'lulus', 'lulus_revisi' => 95,
                                    'selesai' => 100,
                                    default => 0
                                };
                            @endphp
                            <div class="progress-bar bg-{{ $badgeClass }}" role="progressbar" style="width: {{ $progress }}%">
                                {{ $progress }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembimbing -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dosen Pembimbing</h6>
                </div>
                <div class="card-body">
                    @if($tugasAkhir->pembimbing1)
                    <p><strong>Pembimbing 1:</strong><br>{{ $tugasAkhir->pembimbing1->nama }}</p>
                    @else
                    <p class="text-muted">Pembimbing 1: Menunggu penetapan</p>
                    @endif
                    
                    @if($tugasAkhir->pembimbing2)
                    <p class="mb-0"><strong>Pembimbing 2:</strong><br>{{ $tugasAkhir->pembimbing2->nama }}</p>
                    @else
                    <p class="mb-0 text-muted">Pembimbing 2: -</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if(!in_array($tugasAkhir->status, ['draft', 'diajukan', 'judul_ditolak']))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('mahasiswa.tugas-akhir.bimbingan', $tugasAkhir) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-calendar-event"></i> Lihat/Request Bimbingan
                    </a>
                    <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-upload"></i> Upload Dokumen
                    </button>
                    <a href="{{ route('mahasiswa.tugas-akhir.cetak-kartu-bimbingan', $tugasAkhir) }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-printer"></i> Cetak Kartu Bimbingan
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Detail -->
        <div class="col-md-8">
            <!-- Judul & Deskripsi -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Tugas Akhir</h6>
                    @if(in_array($tugasAkhir->status, ['draft', 'judul_ditolak']))
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <h5>{{ $tugasAkhir->judul }}</h5>
                    <p><span class="badge bg-info">{{ $tugasAkhir->bidang_kajian }}</span></p>
                    
                    <h6 class="mt-4">Abstrak/Deskripsi</h6>
                    <p>{{ $tugasAkhir->abstrak }}</p>
                    
                    @if($tugasAkhir->catatan_pembimbing)
                    <div class="alert alert-info">
                        <strong>Catatan dari Admin/Pembimbing:</strong><br>
                        {{ $tugasAkhir->catatan_pembimbing }}
                    </div>
                    @endif

                    @if(in_array($tugasAkhir->status, ['draft', 'judul_ditolak']))
                    <hr>
                    <form action="{{ route('mahasiswa.tugas-akhir.ajukan', $tugasAkhir) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Ajukan judul ini untuk disetujui?')">
                            <i class="bi bi-send"></i> Ajukan untuk Review
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Tahapan Selanjutnya -->
            @if($tugasAkhir->status == 'judul_disetujui')
            <div class="card shadow mb-4 border-primary">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Tahap Selanjutnya: Seminar Proposal</h6>
                </div>
                <div class="card-body">
                    <p>Untuk mengajukan seminar proposal, Anda perlu:</p>
                    <ul>
                        <li>Minimal 3 kali bimbingan dengan pembimbing</li>
                        <li>Upload dokumen proposal (PDF)</li>
                    </ul>
                    
                    <p><strong>Bimbingan selesai:</strong> {{ $tugasAkhir->bimbingan->where('status', 'selesai')->count() }} / 3</p>
                    <p><strong>Dokumen proposal:</strong> {{ $tugasAkhir->dokumen_proposal ? 'Sudah diupload' : 'Belum diupload' }}</p>
                    
                    @php
                        $canAjukanSeminar = $tugasAkhir->bimbingan->where('status', 'selesai')->count() >= 3 && $tugasAkhir->dokumen_proposal;
                    @endphp
                    
                    <form action="{{ route('mahasiswa.tugas-akhir.ajukan-seminar', $tugasAkhir) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" {{ $canAjukanSeminar ? '' : 'disabled' }}>
                            <i class="bi bi-calendar-event"></i> Ajukan Seminar Proposal
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if(in_array($tugasAkhir->status, ['penelitian', 'penulisan']))
            <div class="card shadow mb-4 border-primary">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Tahap Selanjutnya: Sidang Tugas Akhir</h6>
                </div>
                <div class="card-body">
                    <p>Untuk mengajukan sidang TA, Anda perlu:</p>
                    <ul>
                        <li>Minimal 8 kali bimbingan total</li>
                        <li>Upload dokumen draft skripsi</li>
                    </ul>
                    
                    <p><strong>Bimbingan selesai:</strong> {{ $tugasAkhir->bimbingan->where('status', 'selesai')->count() }} / 8</p>
                    <p><strong>Dokumen draft:</strong> {{ $tugasAkhir->dokumen_draft ? 'Sudah diupload' : 'Belum diupload' }}</p>
                    
                    @php
                        $canAjukanSidang = $tugasAkhir->bimbingan->where('status', 'selesai')->count() >= 8 && $tugasAkhir->dokumen_draft;
                    @endphp
                    
                    <form action="{{ route('mahasiswa.tugas-akhir.ajukan-sidang', $tugasAkhir) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success" {{ $canAjukanSidang ? '' : 'disabled' }}>
                            <i class="bi bi-mortarboard"></i> Ajukan Sidang TA
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Seminar Proposal Info -->
            @if($tugasAkhir->seminarProposal)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seminar Proposal</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tugasAkhir->seminarProposal->tanggal)->format('d F Y') }}</p>
                            <p><strong>Waktu:</strong> {{ $tugasAkhir->seminarProposal->waktu_mulai }} - {{ $tugasAkhir->seminarProposal->waktu_selesai }}</p>
                            <p><strong>Ruangan:</strong> {{ $tugasAkhir->seminarProposal->ruangan }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Penguji 1:</strong> {{ $tugasAkhir->seminarProposal->penguji1->nama ?? '-' }}</p>
                            <p><strong>Penguji 2:</strong> {{ $tugasAkhir->seminarProposal->penguji2->nama ?? '-' }}</p>
                            @if($tugasAkhir->seminarProposal->nilai_akhir)
                            <p><strong>Nilai:</strong> {{ number_format($tugasAkhir->seminarProposal->nilai_akhir, 2) }}</p>
                            <p><strong>Hasil:</strong> 
                                <span class="badge bg-{{ $tugasAkhir->seminarProposal->hasil == 'lulus' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $tugasAkhir->seminarProposal->hasil)) }}
                                </span>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sidang Info -->
            @if($tugasAkhir->sidang)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sidang Tugas Akhir</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tugasAkhir->sidang->tanggal)->format('d F Y') }}</p>
                            <p><strong>Waktu:</strong> {{ $tugasAkhir->sidang->waktu_mulai }} - {{ $tugasAkhir->sidang->waktu_selesai }}</p>
                            <p><strong>Ruangan:</strong> {{ $tugasAkhir->sidang->ruangan }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ketua Penguji:</strong> {{ $tugasAkhir->sidang->ketuaPenguji->nama ?? '-' }}</p>
                            <p><strong>Penguji 1:</strong> {{ $tugasAkhir->sidang->penguji1->nama ?? '-' }}</p>
                            <p><strong>Penguji 2:</strong> {{ $tugasAkhir->sidang->penguji2->nama ?? '-' }}</p>
                            @if($tugasAkhir->sidang->nilai_akhir)
                            <p><strong>Nilai:</strong> {{ number_format($tugasAkhir->sidang->nilai_akhir, 2) }} ({{ $tugasAkhir->sidang->getNilaiHuruf() }})</p>
                            <p><strong>Hasil:</strong> 
                                <span class="badge bg-{{ $tugasAkhir->sidang->hasil == 'lulus' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $tugasAkhir->sidang->hasil)) }}
                                </span>
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Revisi -->
                    @if($tugasAkhir->sidang->revisi->count() > 0)
                    <hr>
                    <h6>Catatan Revisi:</h6>
                    <ul>
                        @foreach($tugasAkhir->sidang->revisi as $revisi)
                        <li>
                            <strong>{{ $revisi->dosen->nama }}:</strong> {{ $revisi->catatan_revisi }}
                            @if($revisi->sudah_diperbaiki)
                            <span class="badge bg-success">Selesai</span>
                            @else
                            <span class="badge bg-warning">Belum selesai</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    
                    @if($tugasAkhir->status == 'lulus_revisi')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadRevisiModal">
                        <i class="bi bi-upload"></i> Upload Dokumen Revisi
                    </button>
                    @endif
                    @endif
                </div>
            </div>
            @endif

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
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pembimbing</th>
                                    <th>Materi</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tugasAkhir->bimbingan as $index => $bimbingan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bimbingan->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $bimbingan->dosen->nama ?? '-' }}</td>
                                    <td>{{ Str::limit($bimbingan->materi_bimbingan, 40) }}</td>
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
        </div>
    </div>

    <!-- Modal Edit -->
    @if(in_array($tugasAkhir->status, ['draft', 'judul_ditolak']))
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('mahasiswa.tugas-akhir.update', $tugasAkhir) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Pengajuan Tugas Akhir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <textarea name="judul" class="form-control" rows="2" required>{{ $tugasAkhir->judul }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bidang Ilmu <span class="text-danger">*</span></label>
                            <input type="text" name="bidang_ilmu" class="form-control" value="{{ $tugasAkhir->bidang_kajian }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi/Abstrak <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="3" required>{{ $tugasAkhir->abstrak }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Latar Belakang</label>
                            <textarea name="latar_belakang" class="form-control" rows="3">{{ $tugasAkhir->latar_belakang }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rumusan Masalah</label>
                            <textarea name="rumusan_masalah" class="form-control" rows="3">{{ $tugasAkhir->rumusan_masalah }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan/Metodologi Penelitian</label>
                            <textarea name="tujuan_penelitian" class="form-control" rows="3">{{ $tugasAkhir->metodologi }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dokumen Proposal (PDF)</label>
                            <input type="file" name="dokumen_proposal" class="form-control" accept=".pdf">
                            @if($tugasAkhir->dokumen_proposal)
                            <small class="text-muted">File saat ini: {{ basename($tugasAkhir->dokumen_proposal) }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('mahasiswa.tugas-akhir.upload-dokumen', $tugasAkhir) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis Dokumen</label>
                            <select name="jenis_dokumen" class="form-select" required>
                                <option value="proposal">Proposal</option>
                                <option value="bab1">BAB 1</option>
                                <option value="bab2">BAB 2</option>
                                <option value="bab3">BAB 3</option>
                                <option value="bab4">BAB 4</option>
                                <option value="bab5">BAB 5</option>
                                <option value="full_draft">Draft Lengkap</option>
                                <option value="final">Dokumen Final</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File (PDF/DOC/DOCX, max 20MB)</label>
                            <input type="file" name="dokumen" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Upload Revisi -->
    @if($tugasAkhir->status == 'lulus_revisi')
    <div class="modal fade" id="uploadRevisiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('mahasiswa.tugas-akhir.upload-revisi', $tugasAkhir) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Dokumen Revisi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Dokumen Revisi (PDF/DOC/DOCX)</label>
                            <input type="file" name="dokumen_revisi" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan revisi yang telah dilakukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
