@extends('layouts.app')

@section('title', 'Tambah Slip Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tambah Slip Gaji</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.index') }}">Slip Gaji</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
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

    <form action="{{ route('kepegawaian.slip-gaji.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Data Pegawai & Periode</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipePegawai" class="form-select" required>
                                <option value="">Pilih Tipe</option>
                                <option value="dosen" {{ old('tipe_pegawai') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="tendik" {{ old('tipe_pegawai') == 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                        </div>
                        <div class="mb-3" id="dosenSelect" style="display: none;">
                            <label class="form-label">Dosen <span class="text-danger">*</span></label>
                            <select name="pegawai_id" class="form-select pegawai-select">
                                <option value="">Pilih Dosen</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }} ({{ $dosen->nidn ?? 'No NIDN' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="tendikSelect" style="display: none;">
                            <label class="form-label">Tendik <span class="text-danger">*</span></label>
                            <select name="pegawai_id" class="form-select pegawai-select">
                                <option value="">Pilih Tendik</option>
                                @foreach($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }} ({{ $pegawai->nip ?? 'No NIP' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                    <select name="tahun" class="form-select" required>
                                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                    <select name="bulan" class="form-select" required>
                                        @php
                                            $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        @endphp
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ $bulanNames[$i-1] }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', 0) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komponens->where('jenis', 'pendapatan') as $komponen)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" 
                                                class="komponen-check pendapatan-check" {{ $komponen->wajib ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {{ $komponen->nama }}
                                            @if($komponen->wajib)
                                                <span class="badge bg-primary">Wajib</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                                class="form-control form-control-sm" value="{{ $komponen->nilai_default }}">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komponens->where('jenis', 'potongan') as $komponen)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" 
                                                class="komponen-check potongan-check" {{ $komponen->wajib ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            {{ $komponen->nama }}
                                            @if($komponen->wajib)
                                                <span class="badge bg-primary">Wajib</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                                class="form-control form-control-sm" value="{{ $komponen->nilai_default }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('kepegawaian.slip-gaji.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipePegawai = document.getElementById('tipePegawai');
    const dosenSelect = document.getElementById('dosenSelect');
    const tendikSelect = document.getElementById('tendikSelect');

    tipePegawai.addEventListener('change', function() {
        const val = this.value;
        dosenSelect.style.display = val === 'dosen' ? 'block' : 'none';
        tendikSelect.style.display = val === 'tendik' ? 'block' : 'none';
        
        // Enable/disable the select
        dosenSelect.querySelector('select').disabled = val !== 'dosen';
        tendikSelect.querySelector('select').disabled = val !== 'tendik';
    });

    // Trigger initial state
    tipePegawai.dispatchEvent(new Event('change'));

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
