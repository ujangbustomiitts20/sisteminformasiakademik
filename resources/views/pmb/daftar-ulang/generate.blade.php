@extends('layouts.app')

@section('title', 'Generate Daftar Ulang')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Generate Daftar Ulang</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.daftar-ulang.index') }}">Daftar Ulang</a></li>
                <li class="breadcrumb-item active">Generate</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="bi bi-gear me-2"></i>Generate Daftar Ulang dari Hasil Seleksi
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Proses ini akan membuat data daftar ulang untuk semua calon mahasiswa yang dinyatakan <strong>LULUS</strong> 
                seleksi pada gelombang yang dipilih dan belum memiliki data daftar ulang.
            </div>

            <form action="{{ route('pmb.daftar-ulang.store-generate') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Gelombang PMB <span class="text-danger">*</span></label>
                        <select name="gelombang_id" class="form-select @error('gelombang_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ old('gelombang_id') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                    ({{ $gelombang->hasilSeleksi()->where('status', 'lulus')->count() }} lulus)
                                </option>
                            @endforeach
                        </select>
                        @error('gelombang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Biaya Daftar Ulang <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya" class="form-control @error('biaya') is-invalid @enderror" value="{{ old('biaya', 500000) }}" required>
                        </div>
                        @error('biaya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Biaya registrasi ulang</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Biaya UKT</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_ukt" class="form-control @error('biaya_ukt') is-invalid @enderror" value="{{ old('biaya_ukt', 0) }}">
                        </div>
                        @error('biaya_ukt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opsional, kosongkan jika tidak ada</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Batas Waktu Pembayaran <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_expired" class="form-control @error('tanggal_expired') is-invalid @enderror" value="{{ old('tanggal_expired', now()->addDays(14)->format('Y-m-d')) }}" required>
                        @error('tanggal_expired')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send_notification" id="sendNotification" value="1" checked>
                        <label class="form-check-label" for="sendNotification">
                            Kirim notifikasi email ke calon mahasiswa yang lulus
                        </label>
                    </div>
                </div>

                <hr>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat me-1"></i> Generate Daftar Ulang
                    </button>
                    <a href="{{ route('pmb.daftar-ulang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="bi bi-bar-chart me-2"></i>Statistik per Gelombang
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Gelombang</th>
                            <th class="text-center">Lulus Seleksi</th>
                            <th class="text-center">Sudah Punya DU</th>
                            <th class="text-center">Belum Punya DU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gelombangs as $gelombang)
                        @php
                            $lulusCount = $gelombang->hasilSeleksi()->where('status', 'lulus')->count();
                            // Hitung yang sudah punya daftar ulang melalui calon_mahasiswa
                            $sudahDUCount = \App\Models\DaftarUlang::whereHas('calonMahasiswa', function($q) use ($gelombang) {
                                $q->where('gelombang_pmb_id', $gelombang->id);
                            })->count();
                            $belumDUCount = max(0, $lulusCount - $sudahDUCount);
                        @endphp
                        <tr>
                            <td>{{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}</td>
                            <td class="text-center"><span class="badge bg-success">{{ $lulusCount }}</span></td>
                            <td class="text-center">{{ $sudahDUCount }}</td>
                            <td class="text-center">
                                @if($belumDUCount > 0)
                                    <span class="badge bg-warning">{{ $belumDUCount }}</span>
                                @else
                                    <span class="badge bg-secondary">0</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
