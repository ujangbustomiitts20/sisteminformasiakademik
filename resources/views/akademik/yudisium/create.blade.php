@extends('layouts.app')

@section('title', 'Proses Yudisium')

@section('content')
<div class="page-title">
    <h4>Proses Yudisium</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('yudisium.index') }}">Yudisium</a></li>
            <li class="breadcrumb-item active">Proses</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Info Mahasiswa -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person me-2"></i>Data Mahasiswa
            </div>
            <div class="card-body text-center">
                @if($mahasiswa->foto)
                <img src="{{ asset('storage/' . $mahasiswa->foto) }}" class="rounded-circle mb-3" width="100" height="100" alt="Foto">
                @else
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                </div>
                @endif
                <h5 class="mb-1">{{ $mahasiswa->nama }}</h5>
                <p class="text-muted mb-0">{{ $mahasiswa->nim }}</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Program Studi</span>
                    <strong>{{ $mahasiswa->programStudi->nama ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Fakultas</span>
                    <strong>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Angkatan</span>
                    <strong>{{ $mahasiswa->angkatan }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Periode Wisuda</span>
                    <strong>{{ $pendaftaran->periodeWisuda->nama ?? '-' }}</strong>
                </li>
            </ul>
        </div>

        <!-- Info Akademik -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-bar-chart me-2"></i>Data Akademik
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">IPK</span>
                    <strong class="text-primary fs-5">{{ number_format($academicData['ipk'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Total SKS Lulus</span>
                    <strong>{{ $academicData['total_sks'] }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Total MK Lulus</span>
                    <strong>{{ $academicData['total_mk'] }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Tanggal Masuk</span>
                    <strong>{{ $academicData['tanggal_masuk']->format('d/m/Y') }}</strong>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-journal-plus me-2"></i>Form Yudisium</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('yudisium.store', $pendaftaran) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Yudisium <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_yudisium" class="form-control @error('tanggal_yudisium') is-invalid @enderror" 
                                       value="{{ old('tanggal_yudisium', $pendaftaran->periodeWisuda->tanggal_yudisium?->format('Y-m-d')) }}" required>
                                @error('tanggal_yudisium')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lulus <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lulus" class="form-control @error('tanggal_lulus') is-invalid @enderror" 
                                       value="{{ old('tanggal_lulus', now()->format('Y-m-d')) }}" required>
                                @error('tanggal_lulus')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">IPK Akhir <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="4" name="ipk_akhir" 
                                       class="form-control @error('ipk_akhir') is-invalid @enderror" 
                                       value="{{ old('ipk_akhir', number_format($academicData['ipk'], 2)) }}" required>
                                @error('ipk_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total SKS Lulus <span class="text-danger">*</span></label>
                                <input type="number" min="1" name="total_sks_lulus" 
                                       class="form-control @error('total_sks_lulus') is-invalid @enderror" 
                                       value="{{ old('total_sks_lulus', $academicData['total_sks']) }}" required>
                                @error('total_sks_lulus')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Ijazah</label>
                                <input type="text" name="no_ijazah" class="form-control @error('no_ijazah') is-invalid @enderror" 
                                       value="{{ old('no_ijazah') }}" placeholder="Nomor ijazah (opsional)">
                                @error('no_ijazah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Transkrip</label>
                                <input type="text" name="no_transkrip" class="form-control @error('no_transkrip') is-invalid @enderror" 
                                       value="{{ old('no_transkrip') }}" placeholder="Nomor transkrip (opsional)">
                                @error('no_transkrip')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                                  rows="3" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Preview Predikat -->
                    <div class="alert alert-secondary">
                        <h6 class="alert-heading"><i class="bi bi-calculator me-2"></i>Preview Perhitungan</h6>
                        <p class="mb-2">Berdasarkan data yang diinput:</p>
                        <ul class="mb-0">
                            <li>IPK >= 3.51 + Masa Studi <= 4 tahun = <strong class="text-success">Cum Laude</strong></li>
                            <li>IPK 3.01 - 3.50 = <strong class="text-primary">Sangat Memuaskan</strong></li>
                            <li>IPK 2.76 - 3.00 = <strong class="text-info">Memuaskan</strong></li>
                            <li>IPK < 2.76 = <strong class="text-secondary">Cukup</strong></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Yudisium
                        </button>
                        <a href="{{ route('yudisium.candidates') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
