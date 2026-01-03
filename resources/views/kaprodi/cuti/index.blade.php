@extends('layouts.app')

@section('title', 'Cuti Akademik')

@section('content')
<div class="page-title">
    <h4>Permohonan Cuti Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Cuti Akademik</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui Kaprodi" {{ request('status') == 'Disetujui Kaprodi' ? 'selected' : '' }}>Disetujui Kaprodi</option>
                    <option value="Disetujui Dekan" {{ request('status') == 'Disetujui Dekan' ? 'selected' : '' }}>Disetujui Dekan</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Mahasiswa</th>
                        <th>Tahun Akademik</th>
                        <th>Alasan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cutiList as $cuti)
                    <tr>
                        <td>{{ $cuti->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $cuti->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $cuti->tahunAkademik->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $cuti->alasan_badge }}">
                                {{ $cuti->alasan }}
                            </span>
                        </td>
                        <td>{{ $cuti->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $cuti->status_badge }}">
                                {{ $cuti->status }}
                            </span>
                        </td>
                        <td>
                            @if($cuti->status == 'Pending')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#approvalModal{{ $cuti->id }}">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            
                            <!-- Approval Modal -->
                            <div class="modal fade" id="approvalModal{{ $cuti->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('kaprodi.cuti.approval', $cuti) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Approval Cuti Akademik</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>{{ $cuti->mahasiswa->nama }}</strong> ({{ $cuti->mahasiswa->nim }})</p>
                                                <p><strong>Alasan:</strong> {{ $cuti->alasan }}</p>
                                                <hr>
                                                <div class="mb-3">
                                                    <label class="form-label">Keputusan</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Disetujui Kaprodi">Setujui (Lanjut ke Dekan)</option>
                                                        <option value="Ditolak">Tolak</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="catatan" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada permohonan cuti
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $cutiList->links() }}
    </div>
</div>
@endsection
