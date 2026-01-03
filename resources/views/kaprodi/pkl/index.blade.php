@extends('layouts.app')

@section('title', 'PKL/Magang')

@section('content')
<div class="page-title">
    <h4>Monitoring PKL/Magang/KKN</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">PKL/Magang</li>
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
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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
                        <th>Jenis Kegiatan</th>
                        <th>Mitra</th>
                        <th>Periode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftarans as $pendaftaran)
                    <tr>
                        <td>{{ $pendaftaran->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $pendaftaran->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $pendaftaran->periode->jenisKegiatan->nama ?? '-' }}</td>
                        <td>{{ $pendaftaran->mitraDiterima->nama ?? '-' }}</td>
                        <td>{{ $pendaftaran->periode->nama ?? '-' }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'disetujui' => 'info',
                                    'berlangsung' => 'primary',
                                    'selesai' => 'success',
                                    'ditolak' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$pendaftaran->status] ?? 'secondary' }}">
                                {{ ucfirst($pendaftaran->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada data PKL/Magang
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pendaftarans->links() }}
    </div>
</div>
@endsection
