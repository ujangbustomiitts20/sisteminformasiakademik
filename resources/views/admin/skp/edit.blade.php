@extends('layouts.app')

@section('title', 'Edit SKP')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Edit SKP</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kepegawaian.skp.index') }}">SKP Pegawai</a></li>
                <li class="breadcrumb-item active">Edit {{ $skp->no_skp }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kepegawaian.skp.show', $skp) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-pencil me-2"></i>Form Edit SKP</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.skp.update', $skp) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Info Pegawai (readonly) -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3">Informasi Pegawai</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Nama</label>
                                <p class="fw-semibold mb-0">{{ $skp->nama_pegawai }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">NIDN/NIP</label>
                                <p class="fw-semibold mb-0">{{ $skp->dosen->nidn ?? $skp->dosen->nip ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ old('tahun', $skp->tahun) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Periode</label>
                                <select name="periode" class="form-select @error('periode') is-invalid @enderror">
                                    <option value="">-- Pilih Periode --</option>
                                    <option value="Semester 1" {{ old('periode', $skp->periode) == 'Semester 1' ? 'selected' : '' }}>Semester 1 (Januari - Juni)</option>
                                    <option value="Semester 2" {{ old('periode', $skp->periode) == 'Semester 2' ? 'selected' : '' }}>Semester 2 (Juli - Desember)</option>
                                    <option value="Tahunan" {{ old('periode', $skp->periode) == 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                                @error('periode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal SKP</label>
                                <input type="date" name="tanggal_skp" class="form-control @error('tanggal_skp') is-invalid @enderror"
                                       value="{{ old('tanggal_skp', $skp->tanggal_skp?->format('Y-m-d')) }}">
                                @error('tanggal_skp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                                       value="{{ old('jabatan', $skp->jabatan) }}">
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit Kerja</label>
                                <input type="text" name="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror"
                                       value="{{ old('unit_kerja', $skp->unit_kerja) }}">
                                @error('unit_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atasan Penilai</label>
                        <input type="text" name="atasan_penilai" class="form-control @error('atasan_penilai') is-invalid @enderror"
                               value="{{ old('atasan_penilai', $skp->atasan_penilai) }}">
                        @error('atasan_penilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                                  rows="3">{{ old('catatan', $skp->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('kepegawaian.skp.show', $skp) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Info SKP -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info SKP</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">No. SKP</td>
                        <td class="fw-semibold">{{ $skp->no_skp }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $skp->status_badge }}">
                                {{ \App\Models\SkpPegawai::STATUS[$skp->status] ?? $skp->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Target</td>
                        <td class="fw-semibold">{{ $skp->targetSkp->count() }} item</td>
                    </tr>
                    @if($skp->nilai_akhir)
                    <tr>
                        <td class="text-muted">Nilai Akhir</td>
                        <td class="fw-semibold text-primary">{{ number_format($skp->nilai_akhir, 2) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Shortcut -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Shortcut</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kepegawaian.skp.show', $skp) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>Lihat Detail
                    </a>
                    <a href="{{ route('kepegawaian.skp.cetak', $skp) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                        <i class="bi bi-printer me-1"></i>Cetak SKP
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
