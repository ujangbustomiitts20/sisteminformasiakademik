@extends('layouts.app')

@section('title', 'Komponen Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Komponen Gaji</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.index') }}">Slip Gaji</a></li>
                    <li class="breadcrumb-item active">Komponen Gaji</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Komponen
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Pendapatan -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="mb-0 text-success"><i class="bi bi-plus-circle me-1"></i> Pendapatan/Tunjangan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th class="text-end">Nilai Default</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($komponens->where('jenis', 'pendapatan') as $komponen)
                                <tr class="{{ !$komponen->aktif ? 'table-secondary' : '' }}">
                                    <td>
                                        <span class="badge bg-success">{{ $komponen->kode }}</span>
                                    </td>
                                    <td>
                                        {{ $komponen->nama }}
                                        @if($komponen->wajib)
                                            <span class="badge bg-primary">Wajib</span>
                                        @endif
                                        @if(!$komponen->aktif)
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($komponen->nilai_default, 0, ',', '.') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                            data-bs-toggle="modal" data-bs-target="#editModal{{ $komponen->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('kepegawaian.slip-gaji.komponen.destroy', $komponen->hashid) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus komponen ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $komponen->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('kepegawaian.slip-gaji.komponen.update', $komponen->hashid) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Komponen</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                        <input type="text" name="nama" class="form-control" value="{{ $komponen->nama }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                                                        <select name="jenis" class="form-select" required>
                                                            <option value="pendapatan" {{ $komponen->jenis == 'pendapatan' ? 'selected' : '' }}>Pendapatan/Tunjangan</option>
                                                            <option value="potongan" {{ $komponen->jenis == 'potongan' ? 'selected' : '' }}>Potongan</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                                                        <select name="tipe_nilai" class="form-select" required>
                                                            <option value="tetap" {{ $komponen->tipe_nilai == 'tetap' ? 'selected' : '' }}>Nilai Tetap</option>
                                                            <option value="persentase" {{ $komponen->tipe_nilai == 'persentase' ? 'selected' : '' }}>Persentase</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nilai Default <span class="text-danger">*</span></label>
                                                        <input type="number" name="nilai_default" class="form-control" value="{{ $komponen->nilai_default }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Urutan</label>
                                                        <input type="number" name="urutan" class="form-control" value="{{ $komponen->urutan }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Keterangan</label>
                                                        <textarea name="keterangan" class="form-control" rows="2">{{ $komponen->keterangan }}</textarea>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox" name="wajib" class="form-check-input" {{ $komponen->wajib ? 'checked' : '' }}>
                                                        <label class="form-check-label">Komponen Wajib</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="aktif" class="form-check-input" {{ $komponen->aktif ? 'checked' : '' }}>
                                                        <label class="form-check-label">Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada komponen pendapatan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Potongan -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="mb-0 text-danger"><i class="bi bi-dash-circle me-1"></i> Potongan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th class="text-end">Nilai Default</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($komponens->where('jenis', 'potongan') as $komponen)
                                <tr class="{{ !$komponen->aktif ? 'table-secondary' : '' }}">
                                    <td>
                                        <span class="badge bg-danger">{{ $komponen->kode }}</span>
                                    </td>
                                    <td>
                                        {{ $komponen->nama }}
                                        @if($komponen->wajib)
                                            <span class="badge bg-primary">Wajib</span>
                                        @endif
                                        @if(!$komponen->aktif)
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($komponen->nilai_default, 0, ',', '.') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                            data-bs-toggle="modal" data-bs-target="#editModal{{ $komponen->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('kepegawaian.slip-gaji.komponen.destroy', $komponen->hashid) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus komponen ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $komponen->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('kepegawaian.slip-gaji.komponen.update', $komponen->hashid) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Komponen</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                        <input type="text" name="nama" class="form-control" value="{{ $komponen->nama }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                                                        <select name="jenis" class="form-select" required>
                                                            <option value="pendapatan" {{ $komponen->jenis == 'pendapatan' ? 'selected' : '' }}>Pendapatan/Tunjangan</option>
                                                            <option value="potongan" {{ $komponen->jenis == 'potongan' ? 'selected' : '' }}>Potongan</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                                                        <select name="tipe_nilai" class="form-select" required>
                                                            <option value="tetap" {{ $komponen->tipe_nilai == 'tetap' ? 'selected' : '' }}>Nilai Tetap</option>
                                                            <option value="persentase" {{ $komponen->tipe_nilai == 'persentase' ? 'selected' : '' }}>Persentase</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nilai Default <span class="text-danger">*</span></label>
                                                        <input type="number" name="nilai_default" class="form-control" value="{{ $komponen->nilai_default }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Urutan</label>
                                                        <input type="number" name="urutan" class="form-control" value="{{ $komponen->urutan }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Keterangan</label>
                                                        <textarea name="keterangan" class="form-control" rows="2">{{ $komponen->keterangan }}</textarea>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox" name="wajib" class="form-check-input" {{ $komponen->wajib ? 'checked' : '' }}>
                                                        <label class="form-check-label">Komponen Wajib</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="aktif" class="form-check-input" {{ $komponen->aktif ? 'checked' : '' }}>
                                                        <label class="form-check-label">Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada komponen potongan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.slip-gaji.komponen.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Komponen Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select" required>
                            <option value="pendapatan">Pendapatan/Tunjangan</option>
                            <option value="potongan">Potongan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                        <select name="tipe_nilai" class="form-select" required>
                            <option value="tetap">Nilai Tetap</option>
                            <option value="persentase">Persentase</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai Default <span class="text-danger">*</span></label>
                        <input type="number" name="nilai_default" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="wajib" class="form-check-input">
                        <label class="form-check-label">Komponen Wajib (akan otomatis ditambahkan di setiap slip gaji)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
