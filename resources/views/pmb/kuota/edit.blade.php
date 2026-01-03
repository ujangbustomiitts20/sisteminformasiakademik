@extends('layouts.app')

@section('title', 'Edit Kuota PMB')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Edit Kuota PMB</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.kuota.index') }}">Kuota</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Edit Kuota</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pmb.kuota.update', $kuota->hashid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Gelombang <span class="text-danger">*</span></label>
                                <select name="gelombang_pmb_id" class="form-select @error('gelombang_pmb_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Gelombang --</option>
                                    @foreach($gelombangs as $g)
                                        <option value="{{ $g->id }}" 
                                            {{ old('gelombang_pmb_id', $kuota->gelombang_pmb_id) == $g->id ? 'selected' : '' }}>
                                            {{ $g->periodePmb->nama ?? '-' }} - Gel. {{ $g->nomor_gelombang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gelombang_pmb_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                                <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach($prodis as $prodi)
                                        <option value="{{ $prodi->id }}" 
                                            {{ old('program_studi_id', $kuota->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                                <select name="jalur_seleksi_id" class="form-select @error('jalur_seleksi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jalur Seleksi --</option>
                                    @foreach($jalurs as $jalur)
                                        <option value="{{ $jalur->id }}" 
                                            {{ old('jalur_seleksi_id', $kuota->jalur_seleksi_id) == $jalur->id ? 'selected' : '' }}>
                                            {{ $jalur->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jalur_seleksi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Kuota <span class="text-danger">*</span></label>
                                <input type="number" name="kuota" 
                                       class="form-control @error('kuota') is-invalid @enderror" 
                                       min="0" value="{{ old('kuota', $kuota->kuota) }}" required>
                                @error('kuota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Terisi</label>
                                <input type="text" class="form-control" value="{{ $kuota->terisi }}" disabled>
                                <small class="text-muted">Diupdate otomatis saat seleksi</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Passing Grade</label>
                                <input type="number" name="passing_grade" 
                                       class="form-control @error('passing_grade') is-invalid @enderror" 
                                       min="0" max="100" step="0.1" 
                                       value="{{ old('passing_grade', $kuota->passing_grade) }}"
                                       placeholder="0 - 100">
                                @error('passing_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pmb.kuota.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik Kuota</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Total Kuota</td>
                            <td class="text-end fw-bold">{{ $kuota->kuota }}</td>
                        </tr>
                        <tr>
                            <td>Terisi</td>
                            <td class="text-end">
                                <span class="badge bg-success">{{ $kuota->terisi }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Sisa Kuota</td>
                            <td class="text-end">
                                @php $sisa = $kuota->kuota - $kuota->terisi; @endphp
                                <span class="badge {{ $sisa > 0 ? 'bg-primary' : 'bg-danger' }}">
                                    {{ $sisa }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Persentase Terisi</td>
                            <td class="text-end">
                                @php 
                                    $persen = $kuota->kuota > 0 ? round(($kuota->terisi / $kuota->kuota) * 100, 1) : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $persen >= 100 ? 'bg-danger' : ($persen >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                         role="progressbar" style="width: {{ min($persen, 100) }}%">
                                        {{ $persen }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($kuota->terisi > 0)
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Perhatian!</strong> Kuota ini sudah memiliki {{ $kuota->terisi }} pendaftar yang diterima.
                    Mengubah kuota tidak akan mempengaruhi pendaftar yang sudah diterima.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
