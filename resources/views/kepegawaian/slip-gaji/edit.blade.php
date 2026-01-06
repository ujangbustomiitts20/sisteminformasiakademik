@extends('layouts.app')

@section('title', 'Edit Slip Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Slip Gaji</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.index') }}">Slip Gaji</a></li>
                    <li class="breadcrumb-item active">Edit {{ $slipGaji->no_slip }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('kepegawaian.slip-gaji.update', $slipGaji->hashid) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Data Pegawai & Periode</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">No. Slip</label>
                            <input type="text" class="form-control" value="{{ $slipGaji->no_slip }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pegawai</label>
                            <input type="text" class="form-control" value="{{ $slipGaji->nama_pegawai }} ({{ $slipGaji->tipe_pegawai }})" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <input type="text" class="form-control" value="{{ $slipGaji->periode }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', $slipGaji->gaji_pokok) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $slipGaji->catatan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Komponen Gaji</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $existingKomponens = $slipGaji->details->keyBy('komponen_gaji_id');
                        @endphp

                        <!-- Pendapatan -->
                        <h6 class="text-success mb-3"><i class="bi bi-plus-circle me-1"></i> Tunjangan/Pendapatan</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="checkAllPendapatan">
                                        </th>
                                        <th>Komponen</th>
                                        <th width="30%">Nilai (Rp)</th>
                                        <th width="25%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komponens->where('jenis', 'pendapatan') as $komponen)
                                    @php
                                        $existing = $existingKomponens->get($komponen->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" 
                                                class="komponen-check pendapatan-check" {{ $existing || $komponen->wajib ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {{ $komponen->nama }}
                                            @if($komponen->wajib)
                                                <span class="badge bg-primary">Wajib</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                                class="form-control form-control-sm" 
                                                value="{{ $existing?->nilai ?? $komponen->nilai_default }}">
                                        </td>
                                        <td>
                                            <input type="text" name="komponen[{{ $komponen->id }}][keterangan]" 
                                                class="form-control form-control-sm" 
                                                value="{{ $existing?->keterangan }}" placeholder="Opsional">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Potongan -->
                        <h6 class="text-danger mb-3"><i class="bi bi-dash-circle me-1"></i> Potongan</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="checkAllPotongan">
                                        </th>
                                        <th>Komponen</th>
                                        <th width="30%">Nilai (Rp)</th>
                                        <th width="25%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komponens->where('jenis', 'potongan') as $komponen)
                                    @php
                                        $existing = $existingKomponens->get($komponen->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" 
                                                class="komponen-check potongan-check" {{ $existing || $komponen->wajib ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {{ $komponen->nama }}
                                            @if($komponen->wajib)
                                                <span class="badge bg-primary">Wajib</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                                class="form-control form-control-sm" 
                                                value="{{ $existing?->nilai ?? $komponen->nilai_default }}">
                                        </td>
                                        <td>
                                            <input type="text" name="komponen[{{ $komponen->id }}][keterangan]" 
                                                class="form-control form-control-sm" 
                                                value="{{ $existing?->keterangan }}" placeholder="Opsional">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('kepegawaian.slip-gaji.show', $slipGaji->hashid) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check all pendapatan
    document.getElementById('checkAllPendapatan')?.addEventListener('change', function() {
        document.querySelectorAll('.pendapatan-check').forEach(cb => cb.checked = this.checked);
    });

    // Check all potongan
    document.getElementById('checkAllPotongan')?.addEventListener('change', function() {
        document.querySelectorAll('.potongan-check').forEach(cb => cb.checked = this.checked);
    });
});
</script>
@endpush
@endsection
