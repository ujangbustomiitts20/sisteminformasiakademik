@extends('layouts.app')

@section('title', 'Konversi Nilai Mahasiswa Pindahan')

@section('content')
<div class="page-title">
    <h4>Konversi Nilai Mahasiswa Pindahan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Konversi Nilai</li>
        </ol>
    </nav>
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

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Program Studi: {{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu_kaprodi" {{ request('status') == 'menunggu_kaprodi' ? 'selected' : '' }}>Menunggu Diproses</option>
                    <option value="diproses_kaprodi" {{ request('status') == 'diproses_kaprodi' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="disetujui_kaprodi" {{ request('status') == 'disetujui_kaprodi' ? 'selected' : '' }}>Disetujui Kaprodi</option>
                    <option value="ditolak_kaprodi" {{ request('status') == 'ditolak_kaprodi' ? 'selected' : '' }}>Ditolak Kaprodi</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Finalisasi Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Finalisasi Ditolak</option>
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
                        <th>No. Pengajuan</th>
                        <th>Calon Mahasiswa</th>
                        <th>Universitas Asal</th>
                        <th>Jumlah MK</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $pengajuan)
                    <tr>
                        <td>{{ $pengajuan->nomor_pengajuan ?? '-' }}</td>
                        <td>
                            <strong>{{ $pengajuan->nama_calon_mahasiswa ?? $pengajuan->mahasiswa->nama ?? '-' }}</strong><br>
                            @if($pengajuan->email_calon)
                            <small class="text-muted">{{ $pengajuan->email_calon }}</small>
                            @elseif($pengajuan->mahasiswa)
                            <small class="text-muted">{{ $pengajuan->mahasiswa->nim }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $pengajuan->universitas_asal }}<br>
                            <small class="text-muted">{{ $pengajuan->program_studi_asal }}</small>
                        </td>
                        <td>{{ $pengajuan->detailKonversi->count() }} MK</td>
                        <td>{{ $pengajuan->created_at->format('d M Y') }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'menunggu_kaprodi' => 'warning',
                                    'diproses_kaprodi' => 'info',
                                    'disetujui_kaprodi' => 'primary',
                                    'ditolak_kaprodi' => 'danger',
                                    'disetujui' => 'success',
                                    'ditolak' => 'danger',
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'menunggu_kaprodi' => 'Menunggu Proses',
                                    'diproses_kaprodi' => 'Sedang Diproses',
                                    'disetujui_kaprodi' => 'Disetujui Kaprodi',
                                    'ditolak_kaprodi' => 'Ditolak Kaprodi',
                                    'disetujui' => 'Final - Disetujui',
                                    'ditolak' => 'Final - Ditolak',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$pengajuan->status] ?? 'secondary' }}">
                                {{ $statusLabels[$pengajuan->status] ?? ucfirst($pengajuan->status) }}
                            </span>
                        </td>
                        <td>
                            @if(in_array($pengajuan->status, ['menunggu_kaprodi', 'diproses_kaprodi']))
                            <a href="{{ route('kaprodi.konversi-nilai.show', $pengajuan) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square"></i> Proses
                            </a>
                            @else
                            <a href="{{ route('kaprodi.konversi-nilai.show', $pengajuan) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada pengajuan konversi nilai untuk program studi ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pengajuans->links() }}
    </div>
</div>
@endsection
