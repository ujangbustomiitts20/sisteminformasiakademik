@extends('layouts.app')

@section('title', 'Akun Bank')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Akun Bank</h1>
            <p class="text-muted mb-0">Kelola rekening bank untuk rekonsiliasi</p>
        </div>
        <a href="{{ route('akun-bank.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Akun Bank
        </a>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="mb-0">{{ $stats['total'] }}</h5>
                    <small>Total Akun Bank</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="mb-0">{{ $stats['aktif'] }}</h5>
                    <small>Akun Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="mb-0">Rp {{ number_format($stats['total_saldo'], 0, ',', '.') }}</h5>
                    <small>Total Saldo Sistem</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bank</th>
                            <th>No. Rekening</th>
                            <th>Nama Rekening</th>
                            <th>Tipe</th>
                            <th class="text-end">Saldo Sistem</th>
                            <th>Status</th>
                            <th>Mutasi</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($akunBank as $akun)
                        <tr>
                            <td>
                                <strong>{{ $akun->nama_bank }}</strong>
                                @if($akun->cabang)
                                <br><small class="text-muted">{{ $akun->cabang }}</small>
                                @endif
                            </td>
                            <td>{{ $akun->nomor_rekening }}</td>
                            <td>{{ $akun->nama_rekening }}</td>
                            <td>
                                @switch($akun->tipe)
                                    @case('penampungan')
                                        <span class="badge bg-primary">Penampungan</span>
                                        @break
                                    @case('operasional')
                                        <span class="badge bg-info">Operasional</span>
                                        @break
                                    @case('beasiswa')
                                        <span class="badge bg-success">Beasiswa</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($akun->saldo_sistem, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($akun->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $akun->mutasi_bank_count }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('akun-bank.show', $akun) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('akun-bank.edit', $akun) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('akun-bank.toggle-status', $akun) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $akun->is_active ? 'secondary' : 'success' }}" 
                                                title="{{ $akun->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $akun->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-bank fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada akun bank</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($akunBank->hasPages())
        <div class="card-footer">
            {{ $akunBank->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
