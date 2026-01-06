@extends('layouts.app')

@section('title', 'Izin Keluar Saya')

@section('content')
<div class="page-title">
    <h4>Izin Keluar Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Izin Keluar</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Terjadi kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6>Total Pengajuan</h6>
                <h3>{{ $stats['total'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h6>Menunggu</h6>
                <h3>{{ $stats['pending'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6>Disetujui</h6>
                <h3>{{ $stats['disetujui'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h6>Ditolak</h6>
                <h3>{{ $stats['ditolak'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Actions & Filters -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Ajukan Izin Keluar
                </button>
            </div>
            <div class="col-md-6">
                <form action="{{ route('dosen.izin-keluar.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
                    <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Menunggu Kaprodi</option>
                        <option value="menunggu_admin" {{ request('status') == 'menunggu_admin' ? 'selected' : '' }}>Menunggu Admin</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    @if(request('status'))
                    <a href="{{ route('dosen.izin-keluar.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Keperluan</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinList as $i => $izin)
                        <tr>
                            <td>{{ $izinList->firstItem() + $i }}</td>
                            <td>{{ $izin->tanggal->format('d/m/Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($izin->jam_keluar)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($izin->jam_kembali)->format('H:i') }}
                            </td>
                            <td>{{ $izin->keperluan_label }}</td>
                            <td>{{ Str::limit($izin->tujuan, 30) ?: '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $izin->status_color }}">{{ $izin->full_status_label }}</span>
                                @if($izin->status_kaprodi === 'disetujui' && $izin->status === 'menunggu_admin')
                                    <br><small class="text-success"><i class="bi bi-check-circle"></i> Kaprodi OK</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dosen.izin-keluar.show', $izin) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($izin->status === 'diajukan')
                                    <form action="{{ route('dosen.izin-keluar.destroy', $izin) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Batalkan" onclick="return confirm('Batalkan pengajuan ini?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada pengajuan izin keluar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $izinList->links() }}
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dosen.izin-keluar.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Izin Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Pengajuan izin keluar akan diteruskan ke Kaprodi untuk disetujui, kemudian ke Admin untuk approval final.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jam Keluar <span class="text-danger">*</span></label>
                            <input type="time" name="jam_keluar" class="form-control" required value="{{ old('jam_keluar') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jam Kembali <span class="text-danger">*</span></label>
                            <input type="time" name="jam_kembali" class="form-control" required value="{{ old('jam_kembali') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                            <select name="keperluan" class="form-select" required>
                                <option value="">Pilih Keperluan</option>
                                @foreach(\App\Models\IzinKeluar::KEPERLUAN as $key => $label)
                                    <option value="{{ $key }}" {{ old('keperluan') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan') }}" placeholder="Lokasi tujuan (opsional)">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" rows="3" required placeholder="Jelaskan keperluan izin keluar (min. 10 karakter)">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
        modal.show();
    });
</script>
@endif
@endsection
