@extends('layouts.app')

@section('title', 'Kelola Tahun Akademik')

@section('content')
<div class="page-title">
    <h4>Kelola Tahun Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tahun Akademik</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-range me-2"></i>Daftar Tahun Akademik
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Tahun</th>
                                <th>Semester</th>
                                <th>Periode KRS</th>
                                <th class="text-center" width="120">Status</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAkademik as $index => $ta)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $ta->tahun }}</strong></td>
                                <td>{{ $ta->semester }}</td>
                                <td>
                                    @if($ta->mulai_krs && $ta->selesai_krs)
                                        <small>
                                            {{ $ta->mulai_krs->format('d/m/Y') }} - {{ $ta->selesai_krs->format('d/m/Y') }}
                                            @php
                                                $now = now();
                                                $isKrsOpen = $now->between($ta->mulai_krs, $ta->selesai_krs);
                                            @endphp
                                            @if($isKrsOpen)
                                                <span class="badge bg-success ms-1">Buka</span>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Belum diatur</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($ta->is_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if(!$ta->is_aktif)
                                        <form action="{{ route('tahun-akademik.activate', $ta) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Aktifkan" onclick="return confirm('Aktifkan tahun akademik ini?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ta->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('tahun-akademik.destroy', $ta) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tahun akademik ini?')">
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
                            <div class="modal fade" id="editModal{{ $ta->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('tahun-akademik.update', $ta) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Tahun Akademik</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Tahun</label>
                                                    <input type="text" name="tahun" class="form-control" value="{{ $ta->tahun }}" placeholder="2024/2025" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Semester</label>
                                                    <select name="semester" class="form-select" required>
                                                        <option value="Ganjil" {{ $ta->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                        <option value="Genap" {{ $ta->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Mulai KRS</label>
                                                            <input type="date" name="mulai_krs" class="form-control" value="{{ $ta->mulai_krs ? $ta->mulai_krs->format('Y-m-d') : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Selesai KRS</label>
                                                            <input type="date" name="selesai_krs" class="form-control" value="{{ $ta->selesai_krs ? $ta->selesai_krs->format('Y-m-d') : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_aktif" id="is_aktif{{ $ta->id }}" value="1" {{ $ta->is_aktif ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_aktif{{ $ta->id }}">
                                                            Aktifkan sebagai tahun akademik berjalan
                                                        </label>
                                                    </div>
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
                                    <i class="bi bi-calendar-range text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">Belum ada data tahun akademik</p>
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
                <i class="bi bi-plus-lg me-2"></i>Tambah Tahun Akademik
            </div>
            <div class="card-body">
                <form action="{{ route('tahun-akademik.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="text" name="tahun" id="tahun" class="form-control @error('tahun') is-invalid @enderror" value="{{ old('tahun') }}" placeholder="2024/2025" required>
                        @error('tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            <option value="Ganjil" {{ old('semester', 'Ganjil') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mulai_krs" class="form-label">Mulai KRS</label>
                                <input type="date" name="mulai_krs" id="mulai_krs" class="form-control @error('mulai_krs') is-invalid @enderror" value="{{ old('mulai_krs') }}">
                                @error('mulai_krs')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selesai_krs" class="form-label">Selesai KRS</label>
                                <input type="date" name="selesai_krs" id="selesai_krs" class="form-control @error('selesai_krs') is-invalid @enderror" value="{{ old('selesai_krs') }}">
                                @error('selesai_krs')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_aktif" id="is_aktif" value="1">
                            <label class="form-check-label" for="is_aktif">
                                Aktifkan sebagai tahun akademik berjalan
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="card mt-3">
            <div class="card-body bg-light">
                <h6><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                <p class="small text-muted mb-0">
                    Tahun akademik yang aktif akan digunakan sebagai default untuk pendaftaran KRS dan kegiatan akademik lainnya.
                    Hanya satu tahun akademik yang dapat aktif pada satu waktu.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
