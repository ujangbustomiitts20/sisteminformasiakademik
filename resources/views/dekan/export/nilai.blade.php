@extends('layouts.app')

@section('title', 'Export Rekap Nilai')

@section('content')
<div class="page-title">
    <h4>Export Rekap Nilai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Export Nilai</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-download me-2"></i>Filter Export Rekap Nilai</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dekan.export.nilai.download') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Program Studi</label>
                                <select name="prodi" class="form-select">
                                    <option value="">-- Semua Program Studi --</option>
                                    @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Angkatan</label>
                                <select name="angkatan" class="form-select">
                                    <option value="">-- Semua Angkatan --</option>
                                    @foreach($angkatans as $angkatan)
                                    <option value="{{ $angkatan }}">{{ $angkatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Filter IPK</label>
                                <select name="filter_ipk" class="form-select">
                                    <option value="">-- Semua IPK --</option>
                                    <option value="cumlaude">Cum Laude (≥ 3.50)</option>
                                    <option value="sangat_memuaskan">Sangat Memuaskan (3.00 - 3.49)</option>
                                    <option value="memuaskan">Memuaskan (2.50 - 2.99)</option>
                                    <option value="cukup">Cukup (2.00 - 2.49)</option>
                                    <option value="kurang">Kurang (< 2.00)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Format Export</label>
                                <select name="format" class="form-select">
                                    <option value="csv">CSV (Comma Separated Values)</option>
                                    <option value="excel">Excel (.xlsx)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Kolom yang akan di-export:</strong> NIM, Nama, Program Studi, Angkatan, Total SKS, IPK, Predikat
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download me-1"></i> Download Export
                        </button>
                        <a href="{{ route('dekan.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ number_format($totalMahasiswa) }}</h3>
                <p class="mb-0">Mahasiswa Aktif</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Fakultas:</strong> {{ $fakultas->nama }}</p>
                <p class="mb-2"><strong>Jumlah Prodi:</strong> {{ $prodis->count() }} program studi</p>
                <hr>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Export ini berisi rekapitulasi nilai (IPK) mahasiswa aktif sesuai filter yang dipilih.
                </small>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Keterangan Predikat</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><span class="badge bg-success me-2">Cum Laude</span> IPK ≥ 3.50</li>
                    <li class="mt-2"><span class="badge bg-primary me-2">Sangat Memuaskan</span> IPK 3.00 - 3.49</li>
                    <li class="mt-2"><span class="badge bg-info me-2">Memuaskan</span> IPK 2.50 - 2.99</li>
                    <li class="mt-2"><span class="badge bg-warning me-2">Cukup</span> IPK 2.00 - 2.49</li>
                    <li class="mt-2"><span class="badge bg-danger me-2">Kurang</span> IPK < 2.00</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
