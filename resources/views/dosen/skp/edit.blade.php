@extends('layouts.app')

@section('title', 'Edit SKP')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Edit SKP</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.skp.index') }}">SKP</a></li>
                <li class="breadcrumb-item active">Edit {{ $skp->no_skp }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit SKP</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.skp.update', $skp) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Info (readonly) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">No. SKP</label>
                            <p class="fw-semibold">{{ $skp->no_skp }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tahun</label>
                            <p class="fw-semibold">{{ $skp->tahun }}</p>
                        </div>
                    </div>

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

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dosen.skp.show', $skp) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <div>
                            @if($skp->status === 'draft')
                            <button type="button" class="btn btn-outline-danger me-2" onclick="confirmDelete()">
                                <i class="bi bi-trash me-1"></i>Hapus SKP
                            </button>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark py-3">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Status: {{ \App\Models\SkpPegawai::STATUS[$skp->status] ?? $skp->status }}</h6>
            </div>
            <div class="card-body">
                @if($skp->status === 'draft')
                <p class="mb-0">SKP masih dalam status draft. Anda dapat mengedit periode dan menambahkan target kinerja.</p>
                @elseif($skp->status === 'revisi')
                <p class="mb-2">SKP dikembalikan untuk revisi oleh atasan.</p>
                @if($skp->catatan)
                <div class="alert alert-danger mb-0">
                    <strong>Catatan Revisi:</strong><br>
                    {{ $skp->catatan }}
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
@if($skp->status === 'draft')
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('dosen.skp.destroy', $skp) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Hapus SKP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Yakin ingin menghapus SKP ini? Semua target kinerja juga akan dihapus.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}
</script>
@endpush
@endif
@endsection
