@extends('layouts.app')

@section('title', 'Buat Evaluasi Kinerja')

@section('content')
<div class="page-title">
    <h4>Buat Evaluasi Kinerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.evaluasi.index') }}">Evaluasi Kinerja</a></li>
            <li class="breadcrumb-item active">Buat Baru</li>
        </ol>
    </nav>
</div>

<form action="{{ route('kepegawaian.evaluasi.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Data Pegawai & Periode</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipePegawai" class="form-select" required>
                                <option value="">Pilih Tipe</option>
                                <option value="dosen" {{ old('tipe_pegawai') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ old('tipe_pegawai') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="dosenWrapper" style="display: none;">
                            <label class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                            <select name="dosen_id" id="dosenId" class="form-select">
                                <option value="">Pilih Dosen</option>
                                @foreach($dosenList as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="pegawaiWrapper" style="display: none;">
                            <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_id" id="pegawaiId" class="form-select">
                                <option value="">Pilih Pegawai</option>
                                @foreach($pegawaiList as $pegawai)
                                    <option value="{{ $pegawai->id }}" {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                        {{ $pegawai->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode Evaluasi <span class="text-danger">*</span></label>
                            <select name="periode_id" class="form-select @error('periode_id') is-invalid @enderror" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodeList as $periode)
                                    <option value="{{ $periode->id }}" {{ old('periode_id') == $periode->id ? 'selected' : '' }}>
                                        {{ $periode->nama }} ({{ $periode->tahun }})
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Evaluasi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_evaluasi" class="form-control" value="{{ old('tanggal_evaluasi', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Penilaian Kriteria</h5>
                </div>
                <div class="card-body">
                    @forelse($kriterias as $kriteria)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $kriteria->nama }}</h6>
                                        @if($kriteria->deskripsi)
                                            <small class="text-muted">{{ $kriteria->deskripsi }}</small>
                                        @endif
                                    </div>
                                    <span class="badge bg-info">Bobot: {{ $kriteria->bobot }}%</span>
                                </div>
                                <input type="hidden" name="kriteria[{{ $kriteria->id }}][kriteria_id]" value="{{ $kriteria->id }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Nilai (0-100)</label>
                                        <input type="number" name="kriteria[{{ $kriteria->id }}][nilai]" 
                                               class="form-control nilai-input" 
                                               data-bobot="{{ $kriteria->bobot }}"
                                               data-max="100"
                                               min="0" max="100" 
                                               step="0.01" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Catatan</label>
                                        <input type="text" name="kriteria[{{ $kriteria->id }}][catatan]" class="form-control" placeholder="Opsional">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Belum ada kriteria evaluasi. <a href="{{ route('kepegawaian.evaluasi.kriteria.index') }}">Kelola Kriteria</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan Umum</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card position-sticky" style="top: 100px;">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Penilaian</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <h1 class="display-3 mb-0" id="nilaiAkhir">0.00</h1>
                        <p class="text-muted">Nilai Akhir</p>
                        <span class="badge fs-6" id="predikatBadge">-</span>
                    </div>
                    <hr>
                    <div id="rincianNilai"></div>
                    <hr>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-1"></i> Simpan Evaluasi
                        </button>
                        <a href="{{ route('kepegawaian.evaluasi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('tipePegawai').addEventListener('change', function() {
    const dosenWrapper = document.getElementById('dosenWrapper');
    const pegawaiWrapper = document.getElementById('pegawaiWrapper');
    
    if (this.value === 'dosen') {
        dosenWrapper.style.display = 'block';
        pegawaiWrapper.style.display = 'none';
        document.getElementById('pegawaiId').value = '';
    } else if (this.value === 'pegawai') {
        dosenWrapper.style.display = 'none';
        pegawaiWrapper.style.display = 'block';
        document.getElementById('dosenId').value = '';
    } else {
        dosenWrapper.style.display = 'none';
        pegawaiWrapper.style.display = 'none';
    }
});

document.querySelectorAll('.nilai-input').forEach(function(input) {
    input.addEventListener('input', calculateTotal);
});

function calculateTotal() {
    let totalNilai = 0;
    let rincianHtml = '';
    
    document.querySelectorAll('.nilai-input').forEach(function(input) {
        const nilai = parseFloat(input.value) || 0;
        const bobot = parseFloat(input.dataset.bobot);
        const maxNilai = parseFloat(input.dataset.max);
        const nilaiTerbobot = (nilai / maxNilai) * bobot;
        
        totalNilai += nilaiTerbobot;
        
        if (nilai > 0) {
            rincianHtml += `<div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">${input.closest('.card').querySelector('h6').textContent}</span>
                <span>${nilaiTerbobot.toFixed(2)}</span>
            </div>`;
        }
    });
    
    document.getElementById('nilaiAkhir').textContent = totalNilai.toFixed(2);
    document.getElementById('rincianNilai').innerHTML = rincianHtml;
    
    const badge = document.getElementById('predikatBadge');
    let predikat, color;
    
    if (totalNilai >= 90) {
        predikat = 'Sangat Baik';
        color = 'success';
    } else if (totalNilai >= 75) {
        predikat = 'Baik';
        color = 'info';
    } else if (totalNilai >= 60) {
        predikat = 'Cukup';
        color = 'warning';
    } else {
        predikat = 'Kurang';
        color = 'danger';
    }
    
    badge.textContent = predikat;
    badge.className = 'badge fs-6 bg-' + color;
}

// Trigger on page load
document.getElementById('tipePegawai').dispatchEvent(new Event('change'));
</script>
@endpush
