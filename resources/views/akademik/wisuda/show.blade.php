@extends('layouts.app')

@section('title', 'Detail Periode Wisuda')

@section('content')
<div class="page-title">
    <h4>{{ $wisuda->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisuda.index') }}">Wisuda</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="mb-0 text-primary">{{ $stats['total'] }}</h4>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="mb-0 text-warning">{{ $stats['pending'] }}</h4>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="mb-0 text-info">{{ $stats['verifikasi'] }}</h4>
                <small class="text-muted">Verifikasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="mb-0 text-primary">{{ $stats['lolos'] }}</h4>
                <small class="text-muted">Lolos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="mb-0 text-success">{{ $stats['lulus'] }}</h4>
                <small class="text-muted">Lulus</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center {{ $wisuda->is_pendaftaran_open ? 'bg-success text-white' : '' }}">
            <div class="card-body py-3">
                <h6 class="mb-0">{{ $wisuda->is_pendaftaran_open ? 'DIBUKA' : 'DITUTUP' }}</h6>
                <small>Pendaftaran</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Info Periode -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle me-2"></i>Info Periode</span>
                {!! $wisuda->status_badge !!}
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Tahun Akademik</td>
                        <td><strong>{{ $wisuda->tahunAkademik->tahun ?? '-' }} {{ $wisuda->tahunAkademik->semester ?? '' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Wisuda</td>
                        <td><strong>{{ $wisuda->tanggal_wisuda->format('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lokasi</td>
                        <td>{{ $wisuda->lokasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pendaftaran</td>
                        <td>{{ $wisuda->tanggal_buka_pendaftaran->format('d/m/Y') }} - {{ $wisuda->tanggal_tutup_pendaftaran->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Yudisium</td>
                        <td>{{ $wisuda->tanggal_yudisium?->format('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kuota</td>
                        <td>
                            @if($wisuda->kuota)
                            {{ $wisuda->jumlah_pendaftar }} / {{ $wisuda->kuota }}
                            @else
                            Tidak terbatas
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Biaya</td>
                        <td><strong>Rp {{ number_format($wisuda->biaya_wisuda, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('wisuda.edit', $wisuda) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('wisuda.pendaftaran.create', $wisuda) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i>Tambah Pendaftar
                    </a>
                </div>
            </div>
        </div>

        @if($wisuda->persyaratan)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-check me-2"></i>Persyaratan
            </div>
            <div class="card-body">
                <p class="text-muted mb-0" style="white-space: pre-line;">{{ $wisuda->persyaratan }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <!-- Daftar Pendaftar -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>Daftar Pendaftar</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No. Pendaftaran</th>
                                <th>Mahasiswa</th>
                                <th>Program Studi</th>
                                <th>IPK</th>
                                <th>SKS</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaran as $p)
                            <tr>
                                <td>{{ $pendaftaran->firstItem() + $loop->index }}</td>
                                <td><code>{{ $p->no_pendaftaran }}</code></td>
                                <td>
                                    <strong>{{ $p->mahasiswa->nama ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ $p->mahasiswa->nim ?? '' }}</small>
                                </td>
                                <td>{{ $p->mahasiswa->programStudi->nama ?? '-' }}</td>
                                <td><strong>{{ number_format($p->ipk, 2) }}</strong></td>
                                <td>{{ $p->total_sks }}</td>
                                <td>
                                    @php $berkas = $p->kelengkapan_berkas; @endphp
                                    <div class="progress" style="width: 60px; height: 8px;" title="{{ $berkas['completed'] }}/{{ $berkas['total'] }}">
                                        <div class="progress-bar bg-{{ $berkas['percent'] == 100 ? 'success' : 'warning' }}" 
                                             style="width: {{ $berkas['percent'] }}%"></div>
                                    </div>
                                </td>
                                <td>{!! $p->status_badge !!}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('wisuda.pendaftaran.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(in_array($p->status, ['Pending', 'Verifikasi Berkas']))
                                        <button type="button" class="btn btn-outline-success" title="Verifikasi"
                                                data-bs-toggle="modal" data-bs-target="#verifyModal" 
                                                data-id="{{ $p->hashid }}" data-nama="{{ $p->mahasiswa->nama }}">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">Belum ada pendaftar</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pendaftaran->hasPages())
            <div class="card-footer">
                {{ $pendaftaran->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="verifyForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Verifikasi pendaftaran wisuda: <strong id="verifyNama"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Verifikasi Berkas">Verifikasi Berkas (Lanjut cek dokumen)</option>
                            <option value="Lolos Yudisium">Lolos Yudisium (Siap yudisium)</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" 
                                  placeholder="Catatan hasil verifikasi..."></textarea>
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

@push('scripts')
<script>
document.getElementById('verifyModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const nama = button.getAttribute('data-nama');
    document.getElementById('verifyForm').action = `/wisuda/pendaftaran/${id}/verify`;
    document.getElementById('verifyNama').textContent = nama;
});
</script>
@endpush
@endsection
