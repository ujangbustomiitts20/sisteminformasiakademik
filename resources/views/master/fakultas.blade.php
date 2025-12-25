@extends('layouts.app')

@section('title', 'Kelola Fakultas')

@section('content')
<div class="page-title">
    <h4>Kelola Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Fakultas</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building me-2"></i>Daftar Fakultas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="100">Kode</th>
                                <th>Nama Fakultas</th>
                                <th class="text-center" width="100">Prodi</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fakultas as $index => $f)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $f->kode }}</code></td>
                                <td>{{ $f->nama }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $f->program_studi_count ?? $f->programStudi->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $f->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('fakultas.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus fakultas ini?')">
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
                            <div class="modal fade" id="editModal{{ $f->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('fakultas.update', $f) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Fakultas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="kode{{ $f->id }}" class="form-label">Kode</label>
                                                    <input type="text" name="kode" id="kode{{ $f->id }}" class="form-control" value="{{ $f->kode }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="nama{{ $f->id }}" class="form-label">Nama Fakultas</label>
                                                    <input type="text" name="nama" id="nama{{ $f->id }}" class="form-control" value="{{ $f->nama }}" required>
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
                                <td colspan="5" class="text-center py-4">
                                    <i class="bi bi-building text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">Belum ada data fakultas</p>
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
                <i class="bi bi-plus-lg me-2"></i>Tambah Fakultas
            </div>
            <div class="card-body">
                <form action="{{ route('fakultas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Fakultas <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')
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
