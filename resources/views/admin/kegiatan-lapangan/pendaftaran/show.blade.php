@extends('layouts.app')

@section('title', 'Detail Pendaftaran Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pendaftaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}">Pendaftaran</a></li>
                    <li class="breadcrumb-item active">{{ $pendaftaran->nomor_pendaftaran }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Info Pendaftaran -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Pendaftaran</h5>
                    <span class="badge bg-{{ $pendaftaran->status_badge }} fs-6">{{ $pendaftaran->status_label }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">No. Pendaftaran</td>
                                    <td><code>{{ $pendaftaran->nomor_pendaftaran }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mahasiswa</td>
                                    <td>
                                        <strong>{{ $pendaftaran->mahasiswa->nama ?? '-' }}</strong><br>
                                        <small>{{ $pendaftaran->mahasiswa->nim ?? '-' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Program Studi</td>
                                    <td>{{ $pendaftaran->mahasiswa->programStudi->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Kegiatan</td>
                                    <td>{{ $pendaftaran->periode->jenisKegiatan->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Periode</td>
                                    <td>{{ $pendaftaran->periode->nama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Mitra Pilihan 1</td>
                                    <td>{{ $pendaftaran->mitraPilihan1->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mitra Pilihan 2</td>
                                    <td>{{ $pendaftaran->mitraPilihan2->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mitra Pilihan 3</td>
                                    <td>{{ $pendaftaran->mitraPilihan3->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mitra Diterima</td>
                                    <td><strong class="text-success">{{ $pendaftaran->mitraDiterima->nama ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dosen Pembimbing</td>
                                    <td>{{ $pendaftaran->dosenPembimbing->nama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pendaftaran->rencana_kegiatan)
                    <hr>
                    <h6>Rencana Kegiatan</h6>
                    <p>{{ $pendaftaran->rencana_kegiatan }}</p>
                    @endif
                </div>
            </div>

            <!-- Log Kegiatan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Log Kegiatan Harian</h5>
                </div>
                <div class="card-body">
                    @forelse($pendaftaran->logKegiatan as $log)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $log->tanggal->format('d F Y') }}</strong>
                                <span class="text-muted">({{ $log->jam_mulai }} - {{ $log->jam_selesai }})</span>
                            </div>
                            <span class="badge bg-{{ $log->status_badge }}">{{ ucfirst($log->status) }}</span>
                        </div>
                        <p class="mb-1"><strong>Kegiatan:</strong> {{ $log->kegiatan }}</p>
                        @if($log->hasil)
                        <p class="mb-1"><strong>Hasil:</strong> {{ $log->hasil }}</p>
                        @endif
                        @if($log->kendala)
                        <p class="mb-1 text-danger"><strong>Kendala:</strong> {{ $log->kendala }}</p>
                        @endif
                        
                        @if($log->status == 'diajukan')
                        <div class="mt-3 d-flex gap-2">
                            <form action="{{ route('admin.kegiatan-lapangan.log.approve', $log->hashid) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $log->hashid }}">
                                <i class="bi bi-pencil"></i> Revisi
                            </button>
                        </div>
                        
                        <!-- Modal Revisi -->
                        <div class="modal fade" id="revisiModal{{ $log->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.kegiatan-lapangan.log.revisi', $log->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Kirim Revisi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Komentar/Catatan Revisi</label>
                                                <textarea name="komentar_pembimbing" class="form-control" rows="4" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Kirim Revisi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($log->komentar_pembimbing)
                        <div class="alert alert-info mt-2 mb-0">
                            <strong>Komentar Pembimbing:</strong> {{ $log->komentar_pembimbing }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-journal-x display-4 d-block mb-2"></i>
                        Belum ada log kegiatan
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Penilaian -->
            @if($pendaftaran->status == 'selesai' || $pendaftaran->penilaian->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Penilaian</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($pendaftaran->penilaian as $nilai)
                        <div class="col-md-6">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">
                                    {{ $nilai->jenis_penilai == 'dosen' ? 'Penilaian Dosen Pembimbing' : 'Penilaian Pembimbing Lapangan' }}
                                </h6>
                                <table class="table table-sm">
                                    <tr><td>Kedisiplinan</td><td class="text-end">{{ $nilai->nilai_kedisiplinan ?? '-' }}</td></tr>
                                    <tr><td>Kerjasama</td><td class="text-end">{{ $nilai->nilai_kerjasama ?? '-' }}</td></tr>
                                    <tr><td>Inisiatif</td><td class="text-end">{{ $nilai->nilai_inisiatif ?? '-' }}</td></tr>
                                    <tr><td>Keterampilan</td><td class="text-end">{{ $nilai->nilai_keterampilan ?? '-' }}</td></tr>
                                    <tr><td>Hasil Kerja</td><td class="text-end">{{ $nilai->nilai_hasil_kerja ?? '-' }}</td></tr>
                                    @if($nilai->nilai_laporan)
                                    <tr><td>Laporan</td><td class="text-end">{{ $nilai->nilai_laporan }}</td></tr>
                                    @endif
                                    @if($nilai->nilai_presentasi)
                                    <tr><td>Presentasi</td><td class="text-end">{{ $nilai->nilai_presentasi }}</td></tr>
                                    @endif
                                    <tr class="fw-bold"><td>Nilai Akhir</td><td class="text-end">{{ number_format($nilai->nilai_akhir, 2) ?? '-' }}</td></tr>
                                    @if($nilai->grade)
                                    <tr class="fw-bold text-primary"><td>Grade</td><td class="text-end">{{ $nilai->grade }}</td></tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Aksi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    @if($pendaftaran->status == 'diajukan')
                    <form action="{{ route('admin.kegiatan-lapangan.pendaftaran.proses', $pendaftaran->hashid) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Mitra</label>
                            <select name="mitra_diterima" class="form-select" required>
                                <option value="">-- Pilih Mitra --</option>
                                @if($pendaftaran->mitraPilihan1)
                                <option value="{{ $pendaftaran->mitra_pilihan_1 }}">{{ $pendaftaran->mitraPilihan1->nama }}</option>
                                @endif
                                @if($pendaftaran->mitraPilihan2)
                                <option value="{{ $pendaftaran->mitra_pilihan_2 }}">{{ $pendaftaran->mitraPilihan2->nama }}</option>
                                @endif
                                @if($pendaftaran->mitraPilihan3)
                                <option value="{{ $pendaftaran->mitra_pilihan_3 }}">{{ $pendaftaran->mitraPilihan3->nama }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dosen Pembimbing</label>
                            <select name="dosen_pembimbing_id" class="form-select" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens ?? [] as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>Setujui
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">
                                <i class="bi bi-x-lg me-1"></i>Tolak
                            </button>
                        </div>
                    </form>
                    @elseif($pendaftaran->status == 'disetujui')
                    <form action="{{ route('admin.kegiatan-lapangan.pendaftaran.mulai', $pendaftaran->hashid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-play-fill me-1"></i>Mulai Kegiatan
                        </button>
                    </form>
                    @elseif($pendaftaran->status == 'berlangsung')
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#selesaiModal">
                        <i class="bi bi-check-circle me-1"></i>Selesaikan Kegiatan
                    </button>
                    @endif
                </div>
            </div>

            <!-- Info Pembimbing Lapangan -->
            @if($pendaftaran->pembimbing_lapangan)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pembimbing Lapangan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $pendaftaran->pembimbing_lapangan }}</strong></p>
                    <p class="text-muted mb-0">{{ $pendaftaran->jabatan_pembimbing_lapangan }}</p>
                </div>
            </div>
            @endif

            <!-- Dokumen -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($pendaftaran->surat_pengantar)
                    <a href="{{ Storage::url($pendaftaran->surat_pengantar) }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Surat Pengantar
                    </a>
                    @endif
                    @if($pendaftaran->dokumen_pendukung)
                    <a href="{{ Storage::url($pendaftaran->dokumen_pendukung) }}" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="bi bi-file-earmark me-1"></i>Dokumen Pendukung
                    </a>
                    @endif
                    @if(!$pendaftaran->surat_pengantar && !$pendaftaran->dokumen_pendukung)
                    <p class="text-muted text-center mb-0">Tidak ada dokumen</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesai -->
<div class="modal fade" id="selesaiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.kegiatan-lapangan.pendaftaran.selesai', $pendaftaran->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Kegiatan & Input Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Penilaian Dosen Pembimbing</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Kedisiplinan</label>
                            <input type="number" name="nilai_kedisiplinan" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kerjasama</label>
                            <input type="number" name="nilai_kerjasama" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Inisiatif</label>
                            <input type="number" name="nilai_inisiatif" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Keterampilan</label>
                            <input type="number" name="nilai_keterampilan" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hasil Kerja</label>
                            <input type="number" name="nilai_hasil_kerja" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Laporan</label>
                            <input type="number" name="nilai_laporan" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Presentasi</label>
                            <input type="number" name="nilai_presentasi" class="form-control" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan & Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
