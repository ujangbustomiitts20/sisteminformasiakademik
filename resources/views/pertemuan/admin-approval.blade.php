@extends('layouts.app')

@section('title', 'Persetujuan Jadwal Pertemuan')

@section('content')
<div class="page-title">
    <h4>Persetujuan Jadwal Pertemuan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Persetujuan Pertemuan</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-check me-2"></i>Daftar Pertemuan</span>
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th class="text-center">Pertemuan Ke</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Status</th>
                        <th width="200" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pertemuanList as $index => $p)
                    <tr>
                        <td>{{ $pertemuanList->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $p->jadwalKuliah->mataKuliah->nama }}</strong>
                            <br><small class="text-muted">{{ $p->jadwalKuliah->mataKuliah->kode }} - Kelas {{ $p->jadwalKuliah->kelas }}</small>
                        </td>
                        <td>{{ $p->jadwalKuliah->dosen->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $p->pertemuan_ke }}</span>
                        </td>
                        <td class="text-center">
                            {{ $p->tanggal?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="text-center">
                            {!! $p->approval_badge !!}
                        </td>
                        <td class="text-center">
                            @if($p->approval_status == 'pending')
                            <div class="btn-group btn-group-sm">
                                <form action="{{ route('pertemuan.approve', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Setujui" onclick="return confirm('Setujui tanggal pertemuan ini?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            
                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('pertemuan.reject', $p) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pertemuan ke-{{ $p->pertemuan_ke }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Penolakan</label>
                                                    <textarea name="rejection_note" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
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
                            @elseif($p->approval_status == 'rejected')
                            <small class="text-danger d-block">{{ Str::limit($p->rejection_note, 30) }}</small>
                            @elseif($p->approval_status == 'approved')
                            <small class="text-muted">
                                Oleh: {{ $p->approver->name ?? 'Admin' }}<br>
                                {{ $p->approved_at?->format('d/m/Y H:i') }}
                            </small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Tidak ada data pertemuan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pertemuanList->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $pertemuanList->firstItem() }} - {{ $pertemuanList->lastItem() }} dari {{ $pertemuanList->total() }} data
            </div>
            {{ $pertemuanList->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
