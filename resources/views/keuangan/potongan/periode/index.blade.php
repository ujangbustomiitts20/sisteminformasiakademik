@extends('layouts.app')

@section('title', 'Periode Diskon')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Periode</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Sedang Aktif</h6>
                            <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Akan Datang</h6>
                            <h3 class="mb-0">{{ $stats['akan_datang'] }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Berakhir</h6>
                            <h3 class="mb-0">{{ $stats['berakhir'] }}</h3>
                        </div>
                        <i class="fas fa-history fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Daftar Periode Diskon</h5>
            <a href="{{ route('periode-diskon.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Periode
            </a>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode/nama..." value="{{ request('search') }}">
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
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="akan_datang" {{ request('status') == 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                        <option value="berakhir" {{ request('status') == 'berakhir' ? 'selected' : '' }}>Berakhir</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('periode-diskon.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Periode</th>
                            <th>Jenis Potongan</th>
                            <th>Nilai</th>
                            <th>Periode</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodeDiskon as $item)
                            <tr>
                                <td><code>{{ $item->kode }}</code></td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $item->jenisPotongan->nama ?? '-' }}</span>
                                </td>
                                <td><strong class="text-success">{{ $item->nilai_label }}</strong></td>
                                <td>
                                    <small>
                                        {{ $item->tanggal_mulai->format('d M Y') }}<br>
                                        <span class="text-muted">s/d</span> {{ $item->tanggal_selesai->format('d M Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if($item->kuota)
                                        <span class="badge bg-{{ $item->sisa_kuota > 0 ? 'info' : 'danger' }}">
                                            {{ $item->kuota_terpakai }}/{{ $item->kuota }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Unlimited</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('periode-diskon.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('periode-diskon.edit', $item) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('periode-diskon.toggle-status', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'secondary' : 'success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $item->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('periode-diskon.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Belum ada data periode diskon</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $periodeDiskon->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
