@extends('layouts.app')

@section('title', 'Saldo Cuti Pegawai')

@section('content')
<div class="page-title">
    <h4>Saldo Cuti Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.cuti.index') }}">Cuti Pegawai</a></li>
            <li class="breadcrumb-item active">Saldo Cuti</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-wallet2 me-2"></i>Saldo Cuti Tahunan {{ $tahun }}</span>
        <div>
            <form method="GET" class="d-inline-flex gap-2">
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.cuti.saldo') }}" class="mb-4">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="tipe" class="form-select">
                        <option value="">-- Semua Tipe --</option>
                        <option value="dosen" {{ request('tipe') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="pegawai" {{ request('tipe') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.cuti.saldo', ['tahun' => $tahun]) }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Pegawai</th>
                        <th width="120">NIP/NIDN</th>
                        <th width="80">Tipe</th>
                        <th width="80" class="text-center">Jatah</th>
                        <th width="100" class="text-center">Digunakan</th>
                        <th width="100" class="text-center">Pending</th>
                        <th width="80" class="text-center">Sisa</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saldoCuti as $index => $saldo)
                    <tr>
                        <td>{{ $saldoCuti->firstItem() + $index }}</td>
                        <td>
                            @if($saldo->dosen)
                            <strong>{{ $saldo->dosen->nama_lengkap }}</strong>
                            @elseif($saldo->pegawai)
                            <strong>{{ $saldo->pegawai->nama }}</strong>
                            @endif
                        </td>
                        <td>
                            @if($saldo->dosen)
                            <code>{{ $saldo->dosen->nidn ?? $saldo->dosen->nip }}</code>
                            @elseif($saldo->pegawai)
                            <code>{{ $saldo->pegawai->nip }}</code>
                            @endif
                        </td>
                        <td>
                            @if($saldo->dosen_id)
                            <span class="badge bg-info">Dosen</span>
                            @else
                            <span class="badge bg-secondary">Tendik</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $saldo->jatah_cuti }} hari</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger">{{ $saldo->cuti_digunakan }} hari</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">{{ $saldo->cuti_pending }} hari</span>
                        </td>
                        <td class="text-center">
                            @php
                                $sisa = $saldo->jatah_cuti - $saldo->cuti_digunakan - $saldo->cuti_pending;
                            @endphp
                            <span class="badge {{ $sisa > 5 ? 'bg-success' : ($sisa > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $sisa }} hari
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $saldo->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $saldo->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('kepegawaian.cuti.saldo.update', $saldo) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Saldo Cuti</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Pegawai</label>
                                            <input type="text" class="form-control" value="{{ $saldo->dosen->nama_lengkap ?? $saldo->pegawai->nama ?? '-' }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jatah Cuti (Hari)</label>
                                            <input type="number" name="jatah_cuti" class="form-control" value="{{ $saldo->jatah_cuti }}" min="0" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cuti Digunakan (Hari)</label>
                                            <input type="number" name="cuti_digunakan" class="form-control" value="{{ $saldo->cuti_digunakan }}" min="0" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="keterangan" class="form-control" rows="2">{{ $saldo->keterangan }}</textarea>
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
                        <td colspan="9" class="text-center py-4">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data saldo cuti</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($saldoCuti->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $saldoCuti->firstItem() }} - {{ $saldoCuti->lastItem() }} dari {{ $saldoCuti->total() }} data
            </div>
            {{ $saldoCuti->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
