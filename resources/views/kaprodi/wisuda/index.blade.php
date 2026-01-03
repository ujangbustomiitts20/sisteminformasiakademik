@extends('layouts.app')

@section('title', 'Pendaftar Wisuda')

@section('content')
<div class="page-title">
    <h4>Pendaftar Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pendaftar Wisuda</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Pendaftar Wisuda - {{ $prodi->nama }}</h6>
        <form method="GET" class="d-flex gap-2">
            <select name="periode" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Periode</option>
                @foreach($periodes ?? [] as $periode)
                <option value="{{ $periode }}" {{ request('periode') == $periode ? 'selected' : '' }}>
                    {{ $periode }}
                </option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </form>
    </div>
    <div class="card-body">
        @if($wisudawans->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Belum ada data pendaftar wisuda atau tabel pendaftaran wisuda belum tersedia.
        </div>
        @else
        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['total'] ?? 0 }}</h4>
                        <small>Total Pendaftar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['pending'] ?? 0 }}</h4>
                        <small>Menunggu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['disetujui'] ?? 0 }}</h4>
                        <small>Disetujui</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['ditolak'] ?? 0 }}</h4>
                        <small>Ditolak</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Periode</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Status</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wisudawans as $wisuda)
                    <tr>
                        <td>{{ $wisuda->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $wisuda->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $wisuda->periode ?? '-' }}</td>
                        <td class="text-center">{{ number_format($wisuda->mahasiswa->ipk ?? 0, 2) }}</td>
                        <td class="text-center">{{ $wisuda->mahasiswa->total_sks ?? 0 }}</td>
                        <td class="text-center">
                            @php
                                $status = $wisuda->status ?? 'pending';
                                $statusClass = match(strtolower($status)) {
                                    'disetujui', 'approved' => 'success',
                                    'ditolak', 'rejected' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>{{ $wisuda->created_at?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $wisudawans->links() }}
        @endif
    </div>
</div>
@endsection
