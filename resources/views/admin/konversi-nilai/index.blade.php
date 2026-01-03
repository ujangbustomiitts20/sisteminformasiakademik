@extends('layouts.app')

@section('title', 'Konversi Nilai Mahasiswa Pindahan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Konversi Nilai Mahasiswa Pindahan</h1>
        <a href="{{ route('admin.konversi-nilai.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Konversi
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Workflow:</strong> Admin input data calon mahasiswa → Admin input mata kuliah → Ajukan ke Kaprodi → Kaprodi pemetaan MK → Admin finalisasi setelah mahasiswa terdaftar.
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="menunggu_kaprodi" {{ request('status') == 'menunggu_kaprodi' ? 'selected' : '' }}>Menunggu Kaprodi</option>
                        <option value="diproses_kaprodi" {{ request('status') == 'diproses_kaprodi' ? 'selected' : '' }}>Diproses Kaprodi</option>
                        <option value="disetujui_kaprodi" {{ request('status') == 'disetujui_kaprodi' ? 'selected' : '' }}>Disetujui Kaprodi</option>
                        <option value="ditolak_kaprodi" {{ request('status') == 'ditolak_kaprodi' ? 'selected' : '' }}>Ditolak Kaprodi</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Final - Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Final - Ditolak</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama calon, Universitas..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.konversi-nilai.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Calon Mahasiswa</th>
                            <th>Universitas Asal</th>
                            <th>Prodi Asal</th>
                            <th>Prodi Tujuan</th>
                            <th>Jumlah MK</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $index => $item)
                        <tr>
                            <td>{{ $pengajuan->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $item->nama_calon_mahasiswa ?? $item->mahasiswa->nama ?? '-' }}</strong>
                                @if($item->mahasiswa)
                                <br><small class="text-success">NIM: {{ $item->mahasiswa->nim }}</small>
                                @elseif($item->email_calon)
                                <br><small class="text-muted">{{ $item->email_calon }}</small>
                                @endif
                            </td>
                            <td>{{ $item->universitas_asal }}</td>
                            <td>{{ $item->program_studi_asal }}</td>
                            <td>{{ $item->programStudiTujuan->nama ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $item->detailKonversi->count() }} MK</span></td>
                            <td>
                                @php
                                    $badgeClass = match($item->status) {
                                        'draft' => 'secondary',
                                        'menunggu_kaprodi' => 'warning',
                                        'diproses_kaprodi' => 'info',
                                        'disetujui_kaprodi' => 'primary',
                                        'ditolak_kaprodi' => 'danger',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'menunggu_kaprodi' => 'Menunggu Kaprodi',
                                        'diproses_kaprodi' => 'Diproses Kaprodi',
                                        'disetujui_kaprodi' => 'Disetujui Kaprodi',
                                        'ditolak_kaprodi' => 'Ditolak Kaprodi',
                                        'disetujui' => 'Final - Disetujui',
                                        'ditolak' => 'Final - Ditolak',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $statusLabels[$item->status] ?? ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.konversi-nilai.show', $item->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data pengajuan konversi nilai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $pengajuan->links() }}
        </div>
    </div>
</div>
@endsection
