@extends('layouts.app')

@section('title', 'Kelola Program Studi')

@section('content')
<div class="page-title">
    <h4>Kelola Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Program Studi</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-bookmark me-2"></i>Daftar Program Studi
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="100">Kode</th>
                                <th>Nama Program Studi</th>
                                <th>Fakultas</th>
                                <th width="80">Jenjang</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programStudi as $index => $ps)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $ps->kode }}</code></td>
                                <td>{{ $ps->nama }}</td>
                                <td>{{ $ps->fakultas->nama ?? '-' }}</td>
                                <td><span class="badge bg-secondary">{{ $ps->jenjang ?? 'S1' }}</span></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ps->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('program-studi.destroy', $ps) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus program studi ini?')">
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
                            <div class="modal fade" id="editModal{{ $ps->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('program-studi.update', $ps) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Program Studi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Kode</label>
                                                    <input type="text" name="kode" class="form-control" value="{{ $ps->kode }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Program Studi</label>
                                                    <input type="text" name="nama" class="form-control" value="{{ $ps->nama }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Fakultas</label>
                                                    <select name="fakultas_id" class="form-select" required>
                                                        @foreach($fakultas as $f)
                                                        <option value="{{ $f->id }}" {{ $ps->fakultas_id == $f->id ? 'selected' : '' }}>{{ $f->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jenjang</label>
                                                    <select name="jenjang" class="form-select">
                                                        <option value="D3" {{ $ps->jenjang == 'D3' ? 'selected' : '' }}>D3</option>
                                                        <option value="S1" {{ $ps->jenjang == 'S1' ? 'selected' : '' }}>S1</option>
                                                        <option value="S2" {{ $ps->jenjang == 'S2' ? 'selected' : '' }}>S2</option>
                                                        <option value="S3" {{ $ps->jenjang == 'S3' ? 'selected' : '' }}>S3</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Total SKS</label>
                                                    <input type="number" name="total_sks" class="form-control" value="{{ $ps->total_sks ?? 144 }}" min="100" max="200" required>
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
                                    <i class="bi bi-journal-bookmark text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">Belum ada data program studi</p>
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
                <i class="bi bi-plus-lg me-2"></i>Tambah Program Studi
            </div>
            <div class="card-body">
                <form action="{{ route('program-studi.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Program Studi <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="fakultas_id" class="form-label">Fakultas <span class="text-danger">*</span></label>
                        <select name="fakultas_id" id="fakultas_id" class="form-select @error('fakultas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $f)
                            <option value="{{ $f->id }}" {{ old('fakultas_id') == $f->id ? 'selected' : '' }}>{{ $f->nama }}</option>
                            @endforeach
                        </select>
                        @error('fakultas_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jenjang" class="form-label">Jenjang</label>
                        <select name="jenjang" id="jenjang" class="form-select">
                            <option value="D3" {{ old('jenjang') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ old('jenjang', 'S1') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('jenjang') == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="total_sks" class="form-label">Total SKS <span class="text-danger">*</span></label>
                        <input type="number" name="total_sks" id="total_sks" class="form-control @error('total_sks') is-invalid @enderror" value="{{ old('total_sks', 144) }}" min="100" max="200" required>
                        @error('total_sks')
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
