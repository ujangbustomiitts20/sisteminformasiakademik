@extends('layouts.app')

@section('title', 'Jenis Potongan')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-tags fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Aktif</h6>
                            <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Diskon</h6>
                            <h3 class="mb-0">{{ $stats['diskon'] }}</h3>
                        </div>
                        <i class="fas fa-percent fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Khusus</h6>
                            <h3 class="mb-0">{{ $stats['potongan_khusus'] }}</h3>
                        </div>
                        <i class="fas fa-star fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Promo</h6>
                            <h3 class="mb-0">{{ $stats['promo'] }}</h3>
                        </div>
                        <i class="fas fa-gift fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Keringanan</h6>
                            <h3 class="mb-0">{{ $stats['keringanan'] }}</h3>
                        </div>
                        <i class="fas fa-hand-holding-heart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Daftar Jenis Potongan</h5>
            <a href="{{ route('jenis-potongan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Jenis Potongan
            </a>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode/nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\JenisPotongan::KATEGORI as $key => $value)
                            <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('jenis-potongan.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Nilai Default</th>
                            <th class="text-center">Stackable</th>
                            <th class="text-center">Prioritas</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisPotongan as $item)
                            <tr>
                                <td><code>{{ $item->kode }}</code></td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    @php
                                        $badgeKategori = match($item->kategori) {
                                            'diskon' => 'info',
                                            'potongan_khusus' => 'warning',
                                            'promo' => 'danger',
                                            'keringanan' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeKategori }}">{{ $item->kategori_label }}</span>
                                </td>
                                <td>{{ $item->tipe_nilai_label }}</td>
                                <td><strong>{{ $item->nilai_default_label }}</strong></td>
                                <td class="text-center">
                                    @if($item->is_stackable)
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-muted"></i>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->prioritas }}</td>
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('jenis-potongan.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('jenis-potongan.edit', $item) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('jenis-potongan.toggle-status', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'secondary' : 'success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $item->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('jenis-potongan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada data jenis potongan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $jenisPotongan->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
