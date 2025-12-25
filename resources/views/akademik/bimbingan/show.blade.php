@extends('layouts.app')

@section('title', 'Detail Bimbingan')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Detail Bimbingan Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Bimbingan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-chat-dots me-2"></i>Informasi Bimbingan</span>
                <span class="badge bg-{{ $bimbingan->status_badge }}">{{ $bimbingan->status }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tanggal Bimbingan</h6>
                        <p class="mb-0"><strong>{{ $bimbingan->tanggal_bimbingan->format('l, d F Y') }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Jenis Bimbingan</h6>
                        <p class="mb-0"><span class="badge bg-{{ $bimbingan->jenis_badge }}">{{ $bimbingan->jenis }}</span></p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-1">Topik</h6>
                    <p class="mb-0">{{ $bimbingan->topik }}</p>
                </div>

                @if($bimbingan->catatan_mahasiswa)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Catatan dari Mahasiswa</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $bimbingan->catatan_mahasiswa }}
                    </div>
                </div>
                @endif

                @if($bimbingan->catatan_dosen)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Catatan dari Dosen</h6>
                    <div class="p-3 bg-info bg-opacity-10 rounded">
                        {{ $bimbingan->catatan_dosen }}
                    </div>
                </div>
                @endif

                @if($bimbingan->rekomendasi)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Rekomendasi</h6>
                    <div class="p-3 bg-success bg-opacity-10 rounded">
                        {{ $bimbingan->rekomendasi }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if(Auth::user()->role == 'dosen' && $bimbingan->status == 'Dijadwalkan')
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Update Bimbingan
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('bimbingan.update', $bimbingan) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Dijadwalkan" {{ $bimbingan->status == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                            <option value="Selesai" {{ $bimbingan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ $bimbingan->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Dosen</label>
                        <textarea name="catatan_dosen" class="form-control" rows="3">{{ $bimbingan->catatan_dosen }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rekomendasi</label>
                        <textarea name="rekomendasi" class="form-control" rows="2">{{ $bimbingan->rekomendasi }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Mahasiswa
            </div>
            <div class="card-body text-center">
                <img src="{{ $bimbingan->mahasiswa->foto ? asset('storage/' . $bimbingan->mahasiswa->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($bimbingan->mahasiswa->nama) }}" 
                    class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover;">
                <h6 class="mb-0">{{ $bimbingan->mahasiswa->nama }}</h6>
                <small class="text-muted">{{ $bimbingan->mahasiswa->nim }}</small>
                <hr>
                <table class="table table-sm table-borderless text-start mb-0">
                    <tr>
                        <td class="text-muted">Prodi</td>
                        <td>{{ $bimbingan->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $bimbingan->mahasiswa->semester_aktif }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td><span class="badge bg-{{ $bimbingan->mahasiswa->status == 'Aktif' ? 'success' : 'secondary' }}">{{ $bimbingan->mahasiswa->status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Dosen Wali
            </div>
            <div class="card-body text-center">
                <img src="{{ $bimbingan->dosen->foto ? asset('storage/' . $bimbingan->dosen->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($bimbingan->dosen->nama) }}" 
                    class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover;">
                <h6 class="mb-0">{{ $bimbingan->dosen->nama }}</h6>
                <small class="text-muted">{{ $bimbingan->dosen->nidn }}</small>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Waktu
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $bimbingan->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir Update</td>
                        <td>{{ $bimbingan->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
