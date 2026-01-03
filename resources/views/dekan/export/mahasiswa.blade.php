@extends('layouts.app')

@section('title', 'Export Data Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Export Data Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Export Mahasiswa</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-download me-2"></i>Filter Export Mahasiswa</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dekan.export.mahasiswa.download') }}" method="POST">
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
                                <label class="form-label">Status Mahasiswa</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Semua Status --</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Lulus">Lulus</option>
                                    <option value="DO">Drop Out</option>
                                    <option value="Keluar">Keluar</option>
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
                        <strong>Kolom yang akan di-export:</strong> NIM, Nama, Program Studi, Angkatan, Status, Dosen Wali, Email, No. HP
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
                <p class="mb-0">Total Mahasiswa</p>
            </div>
        </div>
        
        <div class="card bg-success text-white mb-4">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ number_format($totalAktif) }}</h3>
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
                    Data yang di-export adalah data mahasiswa sesuai filter yang dipilih.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
