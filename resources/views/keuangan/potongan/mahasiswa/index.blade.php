@extends('layouts.app')

@section('title', 'Potongan Mahasiswa')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Pending</h6>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Disetujui</h6>
                            <h3 class="mb-0">{{ $stats['disetujui'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Ditolak</h6>
                            <h3 class="mb-0">{{ $stats['ditolak'] }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-tag me-2"></i>Daftar Potongan Mahasiswa</h5>
            <div class="btn-group">
                <a href="{{ route('potongan-mahasiswa.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah
                </a>
                <a href="{{ route('potongan-mahasiswa.bulk-create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-users me-1"></i> Bulk
                </a>
                <a href="{{ route('potongan-mahasiswa.export', request()->query()) }}" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIM/nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="jenis_potongan_id" class="form-select">
                        <option value="">Semua Jenis Potongan</option>
                        @foreach($jenisPotonganList as $jenis)
                            <option value="{{ $jenis->id }}" {{ request('jenis_potongan_id') == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\PotonganMahasiswa::STATUS as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('potongan-mahasiswa.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Mahasiswa</th>
                            <th>Jenis Potongan</th>
                            <th>Nilai</th>
                            <th>Periode</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($potonganMahasiswa as $item)
                            <tr>
                                <td><code>{{ $item->kode }}</code></td>
                                <td>
                                    <strong>{{ $item->mahasiswa->nama ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ $item->mahasiswa->nim ?? '' }} - {{ $item->mahasiswa->programStudi->nama ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->jenisPotongan->nama ?? '-' }}</span>
                                </td>
                                <td><strong class="text-success">{{ $item->nilai_label }}</strong></td>
                                <td>
                                    @if($item->tanggal_mulai || $item->tanggal_selesai)
                                        <small>
                                            {{ $item->tanggal_mulai?->format('d M Y') ?? 'Awal' }}<br>
                                            <span class="text-muted">s/d</span> {{ $item->tanggal_selesai?->format('d M Y') ?? 'Selamanya' }}
                                        </small>
                                    @else
                                        <span class="text-muted">Selamanya</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('potongan-mahasiswa.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($item->status === 'pending')
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $item->id }}" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        @if(in_array($item->status, ['pending', 'disetujui']))
                                            <a href="{{ route('potongan-mahasiswa.edit', $item) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(!$item->riwayatPotongan()->exists())
                                            <form action="{{ route('potongan-mahasiswa.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('potongan-mahasiswa.approve', $item) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Setujui Potongan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>Setujui potongan untuk <strong>{{ $item->mahasiswa->nama ?? '-' }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Catatan (opsional)</label>
                                                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Setujui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('potongan-mahasiswa.reject', $item) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Potongan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p>Tolak potongan untuk <strong>{{ $item->mahasiswa->nama ?? '-' }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea name="alasan_penolakan" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada data potongan mahasiswa</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $potonganMahasiswa->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
