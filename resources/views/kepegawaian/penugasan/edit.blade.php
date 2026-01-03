@extends('layouts.app')

@section('title', 'Edit Penugasan/Mutasi')

@section('content')
<div class="page-title">
    <h4>Edit Penugasan/Mutasi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.penugasan.index') }}">Penugasan/Mutasi</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Form Edit Penugasan/Mutasi
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.penugasan.update', $penugasan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Info Pegawai (readonly) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Pegawai</label>
                    <div class="form-control bg-light">
                        @if($penugasan->dosen)
                        <i class="bi bi-person me-1"></i>{{ $penugasan->dosen->nama_lengkap }} 
                        <span class="badge bg-info">Dosen</span>
                        @elseif($penugasan->pegawai)
                        <i class="bi bi-person me-1"></i>{{ $penugasan->pegawai->nama }} 
                        <span class="badge bg-secondary">Tendik</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach($statusList as $key => $value)
                        <option value="{{ $key }}" {{ old('status', $penugasan->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis <span class="text-danger">*</span></label>
                    <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($jenisList as $key => $value)
                        <option value="{{ $key }}" {{ old('jenis', $penugasan->jenis) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('jenis')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. SK <span class="text-danger">*</span></label>
                    <input type="text" name="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $penugasan->no_sk) }}" required>
                    @error('no_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal SK <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk', $penugasan->tanggal_sk?->format('Y-m-d')) }}" required>
                    @error('tanggal_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">TMT <span class="text-danger">*</span></label>
                    <input type="date" name="tmt" class="form-control @error('tmt') is-invalid @enderror" value="{{ old('tmt', $penugasan->tmt?->format('Y-m-d')) }}" required>
                    @error('tmt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $penugasan->tanggal_selesai?->format('Y-m-d')) }}">
                    @error('tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Section Penugasan -->
            <div id="sectionPenugasan" style="display: none;">
                <hr>
                <h5 class="mb-3"><i class="bi bi-briefcase me-2"></i>Detail Penugasan</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Tugas</label>
                        <input type="text" name="nama_tugas" class="form-control @error('nama_tugas') is-invalid @enderror" value="{{ old('nama_tugas', $penugasan->nama_tugas) }}">
                        @error('nama_tugas')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lokasi Penugasan</label>
                        <input type="text" name="lokasi_penugasan" class="form-control @error('lokasi_penugasan') is-invalid @enderror" value="{{ old('lokasi_penugasan', $penugasan->lokasi_penugasan) }}">
                        @error('lokasi_penugasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Tugas</label>
                    <textarea name="deskripsi_tugas" class="form-control @error('deskripsi_tugas') is-invalid @enderror" rows="3">{{ old('deskripsi_tugas', $penugasan->deskripsi_tugas) }}</textarea>
                    @error('deskripsi_tugas')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Section Mutasi -->
            <div id="sectionMutasi" style="display: none;">
                <hr>
                <h5 class="mb-3"><i class="bi bi-arrow-left-right me-2"></i>Detail Mutasi/Promosi/Demosi</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Unit Kerja Asal</label>
                        <select name="unit_kerja_asal_id" class="form-select @error('unit_kerja_asal_id') is-invalid @enderror">
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerja as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_kerja_asal_id', $penugasan->unit_kerja_asal_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                            @endforeach
                        </select>
                        @error('unit_kerja_asal_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan Asal</label>
                        <input type="text" name="jabatan_asal" class="form-control @error('jabatan_asal') is-invalid @enderror" value="{{ old('jabatan_asal', $penugasan->jabatan_asal) }}">
                        @error('jabatan_asal')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Unit Kerja Tujuan</label>
                        <select name="unit_kerja_tujuan_id" class="form-select @error('unit_kerja_tujuan_id') is-invalid @enderror">
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerja as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_kerja_tujuan_id', $penugasan->unit_kerja_tujuan_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                            @endforeach
                        </select>
                        @error('unit_kerja_tujuan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan Tujuan</label>
                        <input type="text" name="jabatan_tujuan" class="form-control @error('jabatan_tujuan') is-invalid @enderror" value="{{ old('jabatan_tujuan', $penugasan->jabatan_tujuan) }}">
                        @error('jabatan_tujuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <div class="mb-3">
                <label class="form-label">Alasan</label>
                <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="2">{{ old('alasan', $penugasan->alasan) }}</textarea>
                @error('alasan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Dokumen SK (PDF)</label>
                @if($penugasan->dokumen_sk)
                <div class="mb-2">
                    <a href="{{ Storage::url($penugasan->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Dokumen Saat Ini
                    </a>
                </div>
                @endif
                <input type="file" name="dokumen_sk" class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                <div class="form-text">Format: PDF, Maks: 5MB. Kosongkan jika tidak ingin mengubah.</div>
                @error('dokumen_sk')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('kepegawaian.penugasan.show', $penugasan) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis');
    const sectionPenugasan = document.getElementById('sectionPenugasan');
    const sectionMutasi = document.getElementById('sectionMutasi');

    function toggleSections() {
        const jenis = jenisSelect.value;
        
        if (jenis === 'penugasan') {
            sectionPenugasan.style.display = 'block';
            sectionMutasi.style.display = 'none';
        } else if (['mutasi', 'promosi', 'demosi', 'rotasi'].includes(jenis)) {
            sectionPenugasan.style.display = 'none';
            sectionMutasi.style.display = 'block';
        } else {
            sectionPenugasan.style.display = 'none';
            sectionMutasi.style.display = 'none';
        }
    }

    jenisSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initial call
});
</script>
@endpush
@endsection
