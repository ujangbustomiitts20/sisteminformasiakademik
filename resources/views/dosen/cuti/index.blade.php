@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Pengajuan Cuti</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengajuan Cuti</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('dosen.cuti.saldo') }}" class="btn btn-outline-info btn-sm me-2">
            <i class="bi bi-calendar-check me-1"></i>Saldo Cuti
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjukanCuti">
            <i class="bi bi-plus-lg me-1"></i>Ajukan Cuti
        </button>
    </div>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['sisa_cuti'] }}</div>
                        <div class="small">Sisa Cuti {{ date('Y') }}</div>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['diajukan'] }}</div>
                        <div class="small">Menunggu</div>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['disetujui'] }}</div>
                        <div class="small">Disetujui</div>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['ditolak'] }}</div>
                        <div class="small">Ditolak</div>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\CutiPegawai::STATUS as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('dosen.cuti.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Cuti -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal</th>
                        <th>Jumlah Hari</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cutiList as $cuti)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $cuti->no_pengajuan }}</span>
                        </td>
                        <td>{{ \App\Models\CutiPegawai::JENIS_CUTI[$cuti->jenis_cuti] ?? $cuti->jenis_cuti }}</td>
                        <td>
                            {{ $cuti->tanggal_mulai->format('d/m/Y') }} - {{ $cuti->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td>{{ $cuti->jumlah_hari }} hari</td>
                        <td>
                            @php
                                $badgeClass = match($cuti->status) {
                                    'draft' => 'secondary',
                                    'diajukan' => 'warning',
                                    'disetujui_atasan' => 'info',
                                    'disetujui' => 'success',
                                    'ditolak' => 'danger',
                                    'dibatalkan' => 'dark',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ \App\Models\CutiPegawai::STATUS[$cuti->status] ?? $cuti->status }}
                            </span>
                        </td>
                        <td>{{ $cuti->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('dosen.cuti.show', $cuti) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($cuti->status === 'diajukan')
                            <form action="{{ route('dosen.cuti.destroy', $cuti) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Yakin ingin membatalkan pengajuan cuti ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada pengajuan cuti
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($cutiList->hasPages())
    <div class="card-footer bg-white">
        {{ $cutiList->links() }}
    </div>
    @endif
</div>

<!-- Modal Ajukan Cuti -->
<div class="modal fade" id="modalAjukanCuti" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dosen.cuti.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Ajukan Cuti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Sisa Cuti Tahun {{ date('Y') }}:</strong> {{ $saldoCuti->sisa_cuti ?? 0 }} hari
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select name="jenis_cuti" class="form-select @error('jenis_cuti') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                @foreach(\App\Models\CutiPegawai::JENIS_CUTI as $key => $label)
                                    <option value="{{ $key }}" {{ old('jenis_cuti') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis_cuti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                   value="{{ old('tanggal_mulai') }}" min="{{ date('Y-m-d') }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                   value="{{ old('tanggal_selesai') }}" min="{{ date('Y-m-d') }}" required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alasan Cuti <span class="text-danger">*</span></label>
                            <textarea name="alasan" rows="3" class="form-control @error('alasan') is-invalid @enderror" 
                                      placeholder="Jelaskan alasan pengajuan cuti..." required>{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Alamat Selama Cuti</label>
                            <input type="text" name="alamat_selama_cuti" class="form-control" 
                                   value="{{ old('alamat_selama_cuti') }}" placeholder="Alamat yang dapat dihubungi">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon_selama_cuti" class="form-control" 
                                   value="{{ old('no_telepon_selama_cuti') }}" placeholder="08xx">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dokumen Pendukung</label>
                            <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, JPG, PNG (Max 5MB). Contoh: Surat keterangan dokter untuk cuti sakit.</small>
                            @error('dokumen_pendukung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto open modal if there are validation errors
    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('modalAjukanCuti'));
        modal.show();
    @endif
</script>
@endpush
