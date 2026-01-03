@extends('layouts.app')

@section('title', 'Mahasiswa')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Data Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mahasiswa</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-header">Export Data</li>
                <li>
                    <a class="dropdown-item" href="{{ route('export.mahasiswa', array_merge(request()->query(), ['format' => 'csv'])) }}">
                        <i class="bi bi-filetype-csv me-2"></i>Export CSV
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('export.mahasiswa', array_merge(request()->query(), ['format' => 'excel'])) }}">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li class="dropdown-header">Import Data</li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-upload me-2"></i>Import Data
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('import.template.mahasiswa') }}">
                        <i class="bi bi-filetype-csv me-2"></i>Template CSV
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('import.template.mahasiswa', ['format' => 'excel']) }}">
                        <i class="bi bi-file-earmark-excel me-2"></i>Template Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Mahasiswa
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari NIM atau Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="program_studi_id" class="form-select">
                    <option value="">-- Semua Program Studi --</option>
                    @foreach($programStudi as $ps)
                    <option value="{{ $ps->id }}" {{ request('program_studi_id') == $ps->id ? 'selected' : '' }}>{{ $ps->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="angkatan" class="form-select">
                    <option value="">-- Angkatan --</option>
                    @foreach($angkatanList as $a)
                    <option value="{{ $a }}" {{ request('angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Status --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ request('status') == 'DO' ? 'selected' : '' }}>DO</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswa as $mhs)
                    <tr>
                        <td><code>{{ $mhs->nim }}</code></td>
                        <td>
                            <strong>{{ $mhs->nama }}</strong>
                            <br><small class="text-muted">{{ $mhs->email }}</small>
                        </td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>{{ $mhs->semester_aktif }}</td>
                        <td>
                            <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : ($mhs->status == 'Cuti' ? 'warning' : ($mhs->status == 'Lulus' ? 'info' : 'danger')) }}">
                                {{ $mhs->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('mahasiswa.show', $mhs) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('mahasiswa.edit', $mhs) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('mahasiswa.destroy', $mhs) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada data mahasiswa</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $mahasiswa->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bi bi-upload me-2"></i>Import Data Mahasiswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import.mahasiswa') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Format kolom yang dibutuhkan:</strong>
                                <ol class="mb-0 mt-2">
                                    <li><code>nim</code> - Isi <strong>AUTO</strong> untuk generate otomatis</li>
                                    <li><code>nama</code> - Nama Lengkap (wajib)</li>
                                    <li><code>email</code> - Isi <strong>AUTO</strong> untuk generate dari NIM</li>
                                    <li><code>jenis_kelamin</code> - Laki-laki / Perempuan (wajib)</li>
                                    <li><code>tempat_lahir</code> - Tempat Lahir (opsional)</li>
                                    <li><code>tanggal_lahir</code> - Format YYYY-MM-DD (opsional)</li>
                                    <li><code>kode_prodi</code> - Kode Program Studi (wajib)</li>
                                    <li><code>angkatan</code> - Tahun Angkatan (opsional)</li>
                                    <li><code>alamat</code> - Alamat (opsional)</li>
                                    <li><code>telepon</code> - No. Telepon (opsional)</li>
                                </ol>
                            </div>
                            <div class="alert alert-success small">
                                <i class="bi bi-lightbulb me-1"></i>
                                <strong>Tips:</strong> Isi <code>AUTO</code> pada kolom NIM dan Email untuk generate otomatis berdasarkan kode prodi.
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="alert alert-warning small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Password default untuk mahasiswa adalah <strong>NIM</strong>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File CSV / Excel</label>
                                <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
                                <div class="form-text">Format: CSV, Excel (.xlsx, .xls). Maks: 5MB</div>
                            </div>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ route('import.template.mahasiswa') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-filetype-csv me-1"></i>Template CSV
                                </a>
                                <a href="{{ route('import.template.mahasiswa', ['format' => 'excel']) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Template Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
