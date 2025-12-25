@extends('layouts.app')

@section('title', 'Struktur Kurikulum')

@push('styles')
<style>
.kurikulum-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.kurikulum-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.kurikulum-card.has-prasyarat {
    border-left-color: #dc3545;
}
.kurikulum-card.no-prasyarat {
    border-left-color: #198754;
}
.semester-column {
    min-height: 400px;
}
.prasyarat-arrow {
    position: relative;
}
.mk-code {
    font-family: monospace;
    font-size: 0.85rem;
}
.semester-header {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 10;
    padding: 10px 0;
    border-bottom: 2px solid #dee2e6;
}
</style>
@endpush

@section('content')
<div class="page-title">
    <h4>Struktur Kurikulum</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prasyarat.index') }}">Prasyarat MK</a></li>
            <li class="breadcrumb-item active">Struktur Kurikulum</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Program Studi</label>
                <select name="program_studi_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($programStudi as $prodi)
                    <option value="{{ $prodi->id }}" {{ $programStudiId == $prodi->id ? 'selected' : '' }}>
                        {{ $prodi->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <a href="{{ route('prasyarat.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                </a>
            </div>
        </form>
    </div>
</div>

@if($programStudiId && count($kurikulum) > 0)
<!-- Legenda -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row align-items-center">
            <div class="col-auto">
                <strong>Keterangan:</strong>
            </div>
            <div class="col-auto">
                <span class="badge bg-success me-1">●</span> Tidak ada prasyarat
            </div>
            <div class="col-auto">
                <span class="badge bg-danger me-1">●</span> Memiliki prasyarat
            </div>
            <div class="col-auto">
                <span class="badge bg-danger">Wajib</span> Harus lulus dengan nilai minimal
            </div>
            <div class="col-auto">
                <span class="badge bg-warning">Pilihan</span> Cukup pernah mengambil
            </div>
        </div>
    </div>
</div>

<!-- Kurikulum Grid -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex" style="min-width: {{ count($kurikulum) * 280 }}px;">
                @foreach($kurikulum as $semester => $mataKuliahList)
                <div class="semester-column flex-fill px-2" style="min-width: 270px;">
                    <div class="semester-header text-center mb-3">
                        <h5 class="mb-1">
                            <span class="badge bg-primary fs-6">Semester {{ $semester }}</span>
                        </h5>
                        <small class="text-muted">{{ $mataKuliahList->sum('sks') }} SKS | {{ $mataKuliahList->count() }} MK</small>
                    </div>
                    
                    @foreach($mataKuliahList as $mk)
                    <div class="card kurikulum-card mb-2 {{ $mk->prasyarat->count() > 0 ? 'has-prasyarat' : 'no-prasyarat' }}">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <code class="mk-code">{{ $mk->kode }}</code>
                                <span class="badge bg-{{ $mk->jenis == 'Wajib' ? 'danger' : 'info' }} badge-sm">
                                    {{ $mk->sks }} SKS
                                </span>
                            </div>
                            <h6 class="mb-2 small">{{ $mk->nama }}</h6>
                            
                            @if($mk->prasyarat->count() > 0)
                            <div class="border-top pt-2 mt-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-arrow-return-right me-1"></i>Prasyarat:
                                </small>
                                @foreach($mk->prasyarat as $prasyarat)
                                <span class="badge bg-{{ $prasyarat->pivot->jenis_prasyarat == 'wajib' ? 'danger' : 'warning' }} me-1 mb-1" 
                                      title="{{ $prasyarat->nama }} - {{ $prasyarat->pivot->jenis_prasyarat == 'wajib' ? 'Wajib lulus min. ' . $prasyarat->pivot->nilai_minimal : 'Pernah ambil' }}">
                                    {{ $prasyarat->kode }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                            
                            <div class="mt-2">
                                <a href="{{ route('prasyarat.edit', $mk->hashid) }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-pencil-square me-1"></i>Kelola
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Statistik -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ collect($kurikulum)->flatten(1)->count() }}</h3>
                <small>Total Mata Kuliah</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ collect($kurikulum)->flatten(1)->sum('sks') }}</h3>
                <small>Total SKS</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ collect($kurikulum)->flatten(1)->filter(fn($mk) => $mk->prasyarat->count() > 0)->count() }}</h3>
                <small>MK dengan Prasyarat</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ count($kurikulum) }}</h3>
                <small>Jumlah Semester</small>
            </div>
        </div>
    </div>
</div>

@elseif($programStudiId)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="text-muted mt-2">Belum ada data mata kuliah untuk program studi ini</p>
    </div>
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-hand-index fs-1 text-muted"></i>
        <p class="text-muted mt-2">Silakan pilih Program Studi untuk melihat struktur kurikulum</p>
    </div>
</div>
@endif
@endsection
