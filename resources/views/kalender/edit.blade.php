@extends('layouts.app')

@section('title', 'Edit Event Kalender')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Event Kalender</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kalender.index') }}">Kalender Akademik</a></li>
                    <li class="breadcrumb-item active">Edit Event</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar-event me-2"></i>Form Edit Event
                </div>
                <div class="card-body">
                    <form action="{{ route('kalender.update', Hashids::encode($event->id)) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Event <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" 
                                value="{{ old('judul', $event->judul) }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                    value="{{ old('tanggal_mulai', $event->tanggal_mulai->format('Y-m-d')) }}" required>
                                @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                    value="{{ old('tanggal_selesai', $event->tanggal_selesai?->format('Y-m-d')) }}">
                                @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jenis" class="form-label">Jenis Event <span class="text-danger">*</span></label>
                                <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                    <option value="akademik" {{ old('jenis', $event->jenis) == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                    <option value="libur" {{ old('jenis', $event->jenis) == 'libur' ? 'selected' : '' }}>Libur</option>
                                    <option value="ujian" {{ old('jenis', $event->jenis) == 'ujian' ? 'selected' : '' }}>Ujian</option>
                                    <option value="pendaftaran" {{ old('jenis', $event->jenis) == 'pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                                    <option value="lainnya" {{ old('jenis', $event->jenis) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="color" name="warna" id="warna" class="form-control form-control-color w-100" 
                                    value="{{ old('warna', $event->warna) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tahun_akademik_id" class="form-label">Tahun Akademik</label>
                                <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}" {{ old('tahun_akademik_id', $event->tahun_akademik_id) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} - Semester {{ $ta->semester }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Simpan
                                </button>
                                <a href="{{ route('kalender.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x me-1"></i>Batal
                                </a>
                            </div>
                            <form action="{{ route('kalender.destroy', Hashids::encode($event->id)) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus event ini?')">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Info Event
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">ID</td>
                            <td>: #{{ $event->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>: {{ $event->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diubah</td>
                            <td>: {{ $event->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
