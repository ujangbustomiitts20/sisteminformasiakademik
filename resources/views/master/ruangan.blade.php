@extends('layouts.app')

@section('title', 'Kelola Ruangan')

@section('content')
<div class="page-title">
    <h4>Kelola Ruangan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Ruangan</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-door-open me-2"></i>Daftar Ruangan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="100">Kode</th>
                                <th>Nama Ruangan</th>
                                <th width="100">Gedung</th>
                                <th class="text-center" width="100">Kapasitas</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ruangan as $index => $r)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $r->kode }}</code></td>
                                <td>{{ $r->nama }}</td>
                                <td>{{ $r->gedung ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $r->kapasitas }} orang</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $r->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('ruangan.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus ruangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $r->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('ruangan.update', $r) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Ruangan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Kode</label>
                                                    <input type="text" name="kode" class="form-control" value="{{ $r->kode }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Ruangan</label>
                                                    <input type="text" name="nama" class="form-control" value="{{ $r->nama }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Gedung</label>
                                                    <input type="text" name="gedung" class="form-control" value="{{ $r->gedung }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kapasitas</label>
                                                    <input type="number" name="kapasitas" class="form-control" value="{{ $r->kapasitas }}" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jenis</label>
                                                    <select name="jenis" class="form-select" required>
                                                        <option value="Kelas" {{ $r->jenis == 'Kelas' ? 'selected' : '' }}>Kelas</option>
                                                        <option value="Lab" {{ $r->jenis == 'Lab' ? 'selected' : '' }}>Lab</option>
                                                        <option value="Aula" {{ $r->jenis == 'Aula' ? 'selected' : '' }}>Aula</option>
                                                        <option value="Lainnya" {{ $r->jenis == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-door-open text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">Belum ada data ruangan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-lg me-2"></i>Tambah Ruangan
            </div>
            <div class="card-body">
                <form action="{{ route('ruangan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="gedung" class="form-label">Gedung</label>
                        <input type="text" name="gedung" id="gedung" class="form-control @error('gedung') is-invalid @enderror" value="{{ old('gedung') }}">
                        @error('gedung')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas" id="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" value="{{ old('kapasitas', 40) }}" min="1" required>
                        @error('kapasitas')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                            <option value="Kelas" {{ old('jenis', 'Kelas') == 'Kelas' ? 'selected' : '' }}>Kelas</option>
                            <option value="Lab" {{ old('jenis') == 'Lab' ? 'selected' : '' }}>Lab</option>
                            <option value="Aula" {{ old('jenis') == 'Aula' ? 'selected' : '' }}>Aula</option>
                            <option value="Lainnya" {{ old('jenis') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
