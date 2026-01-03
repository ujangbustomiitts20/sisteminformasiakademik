@extends('layouts.app')

@section('title', 'Isi Evaluasi Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Evaluasi Dosen</h1>
            <p class="text-muted mb-0">{{ $jadwalKuliah->mataKuliah->nama ?? 'Mata Kuliah' }}</p>
        </div>
        <a href="{{ route('mahasiswa.edom.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Info Dosen -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="150">Mata Kuliah</td>
                            <td><strong>{{ $jadwalKuliah->mataKuliah->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Kode</td>
                            <td>{{ $jadwalKuliah->mataKuliah->kode ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="150">Dosen</td>
                            <td><strong>{{ $jadwalKuliah->dosen->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIDN</td>
                            <td>{{ $jadwalKuliah->dosen->nidn ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Skala Penilaian -->
    <div class="alert alert-light mb-4">
        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Skala Penilaian:</h6>
        <div class="row small">
            @foreach($nilaiOptions as $nilai => $label)
            <div class="col">
                <span class="badge bg-secondary">{{ $nilai }}</span> {{ $label }}
            </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('mahasiswa.edom.store', $jadwalKuliah->hashid) }}" method="POST">
        @csrf

        @foreach($kategoris as $kodeKategori => $namaKategori)
        @if(isset($pertanyaans[$kodeKategori]) && $pertanyaans[$kodeKategori]->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-bookmark me-2"></i>{{ $namaKategori }}</h5>
            </div>
            <div class="card-body">
                @foreach($pertanyaans[$kodeKategori] as $p)
                <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <p class="mb-2"><strong>{{ $p->urutan }}.</strong> {{ $p->pertanyaan }}</p>
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($nilaiOptions as $nilai => $label)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" 
                                   name="jawaban[{{ $p->id }}]" 
                                   id="jawaban_{{ $p->id }}_{{ $nilai }}" 
                                   value="{{ $nilai }}" 
                                   {{ old("jawaban.{$p->id}") == $nilai ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label" for="jawaban_{{ $p->id }}_{{ $nilai }}">
                                <span class="badge bg-{{ $nilai >= 4 ? 'success' : ($nilai >= 3 ? 'warning' : 'danger') }}">{{ $nilai }}</span>
                                <small class="d-none d-md-inline text-muted">{{ $label }}</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error("jawaban.{$p->id}")
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        <!-- Komentar dan Saran -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chat-quote me-2"></i>Komentar & Saran (Opsional)</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Komentar</label>
                    <textarea name="komentar" class="form-control @error('komentar') is-invalid @enderror" 
                              rows="3" placeholder="Berikan komentar tentang dosen ini (opsional)">{{ old('komentar') }}</textarea>
                    @error('komentar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">Saran</label>
                    <textarea name="saran" class="form-control @error('saran') is-invalid @enderror" 
                              rows="3" placeholder="Berikan saran untuk perbaikan (opsional)">{{ old('saran') }}</textarea>
                    @error('saran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="card bg-light">
            <div class="card-body d-flex justify-content-between align-items-center">
                <p class="mb-0 text-muted small">
                    <i class="bi bi-shield-check me-1"></i>Identitas Anda akan dirahasiakan dari dosen yang bersangkutan.
                </p>
                <div>
                    <a href="{{ route('mahasiswa.edom.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Kirim Evaluasi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
