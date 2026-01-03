@extends('layouts.app')

@section('title', 'Edit Gelombang PMB')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Edit Gelombang PMB</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.gelombang.index') }}">Gelombang</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('pmb.gelombang.update', $gelombang->hashid) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="periode_pmb_id" class="form-label">Periode PMB <span class="text-danger">*</span></label>
                        <select class="form-select @error('periode_pmb_id') is-invalid @enderror" id="periode_pmb_id" name="periode_pmb_id" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periodes as $periode)
                                <option value="{{ $periode->id }}" {{ old('periode_pmb_id', $gelombang->periode_pmb_id) == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama }} ({{ $periode->tahun_akademik }})
                                </option>
                            @endforeach
                        </select>
                        @error('periode_pmb_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="nama" class="form-label">Nama Gelombang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" name="nama" value="{{ old('nama', $gelombang->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="nomor_gelombang" class="form-label">Nomor <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('nomor_gelombang') is-invalid @enderror" 
                               id="nomor_gelombang" name="nomor_gelombang" value="{{ old('nomor_gelombang', $gelombang->nomor_gelombang) }}" min="1" required>
                        @error('nomor_gelombang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai_daftar" class="form-label">Tanggal Mulai Pendaftaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_mulai_daftar') is-invalid @enderror" 
                               id="tanggal_mulai_daftar" name="tanggal_mulai_daftar" value="{{ old('tanggal_mulai_daftar', $gelombang->tanggal_mulai_daftar->format('Y-m-d')) }}" required>
                        @error('tanggal_mulai_daftar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai_daftar" class="form-label">Tanggal Selesai Pendaftaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_selesai_daftar') is-invalid @enderror" 
                               id="tanggal_selesai_daftar" name="tanggal_selesai_daftar" value="{{ old('tanggal_selesai_daftar', $gelombang->tanggal_selesai_daftar->format('Y-m-d')) }}" required>
                        @error('tanggal_selesai_daftar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_ujian" class="form-label">Tanggal Ujian</label>
                        <input type="date" class="form-control @error('tanggal_ujian') is-invalid @enderror" 
                               id="tanggal_ujian" name="tanggal_ujian" value="{{ old('tanggal_ujian', $gelombang->tanggal_ujian?->format('Y-m-d')) }}">
                        @error('tanggal_ujian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pengumuman" class="form-label">Tanggal Pengumuman</label>
                        <input type="date" class="form-control @error('tanggal_pengumuman') is-invalid @enderror" 
                               id="tanggal_pengumuman" name="tanggal_pengumuman" value="{{ old('tanggal_pengumuman', $gelombang->tanggal_pengumuman?->format('Y-m-d')) }}">
                        @error('tanggal_pengumuman')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_daftar_ulang_mulai" class="form-label">Tanggal Mulai Daftar Ulang</label>
                        <input type="date" class="form-control @error('tanggal_daftar_ulang_mulai') is-invalid @enderror" 
                               id="tanggal_daftar_ulang_mulai" name="tanggal_daftar_ulang_mulai" value="{{ old('tanggal_daftar_ulang_mulai', $gelombang->tanggal_daftar_ulang_mulai?->format('Y-m-d')) }}">
                        @error('tanggal_daftar_ulang_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_daftar_ulang_selesai" class="form-label">Tanggal Selesai Daftar Ulang</label>
                        <input type="date" class="form-control @error('tanggal_daftar_ulang_selesai') is-invalid @enderror" 
                               id="tanggal_daftar_ulang_selesai" name="tanggal_daftar_ulang_selesai" value="{{ old('tanggal_daftar_ulang_selesai', $gelombang->tanggal_daftar_ulang_selesai?->format('Y-m-d')) }}">
                        @error('tanggal_daftar_ulang_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $gelombang->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Set sebagai gelombang aktif
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Update
                    </button>
                    <a href="{{ route('pmb.gelombang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
