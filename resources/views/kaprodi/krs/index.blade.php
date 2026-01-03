@extends('layouts.app')

@section('title', 'Persetujuan KRS')

@section('content')
<div class="page-title">
    <h4>Persetujuan KRS</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Persetujuan KRS</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            {{ $prodi->nama }} - {{ $tahunAktif ? $tahunAktif->tahun . ' (' . $tahunAktif->semester . ')' : '-' }}
        </h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
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
                        <th>Nama Mahasiswa</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($krsList as $krs)
                    <tr>
                        <td>{{ $krs->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $krs->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                        <td>{{ $krs->jadwalKuliah->mataKuliah->sks ?? '-' }}</td>
                        <td>{{ $krs->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $krs->status == 'Disetujui' ? 'success' : ($krs->status == 'Pending' ? 'warning' : 'danger') }}">
                                {{ $krs->status }}
                            </span>
                        </td>
                        <td>
                            @if($krs->status == 'Pending')
                            <form action="{{ route('kaprodi.krs.approve', $krs) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui KRS ini?')">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $krs->id }}">
                                <i class="bi bi-x"></i>
                            </button>
                            
                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $krs->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('kaprodi.krs.reject', $krs) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak KRS</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan Penolakan</label>
                                                    <textarea name="keterangan" class="form-control" rows="3" required></textarea>
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
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada data KRS
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $krsList->links() }}
    </div>
</div>
@endsection
