@extends('layouts.app')

@section('title', 'Wisuda')

@section('content')
<div class="page-title">
    <h4>Monitoring Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Wisuda</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ $fakultas->nama }}</h6>
        <span class="badge bg-info">Total Pendaftar Fakultas: {{ $totalPendaftar }}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Periode</th>
                        <th>Tanggal Wisuda</th>
                        <th>Lokasi</th>
                        <th>Total Pendaftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodeWisudas as $periode)
                    <tr>
                        <td>{{ $periode->nama }}</td>
                        <td>{{ $periode->tanggal_wisuda ? $periode->tanggal_wisuda->format('d M Y') : '-' }}</td>
                        <td>{{ $periode->lokasi ?? '-' }}</td>
                        <td>{{ $periode->pendaftaran->count() }}</td>
                        <td>
                            <span class="badge bg-{{ $periode->status == 'Dibuka' ? 'success' : 'secondary' }}">
                                {{ $periode->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.wisuda.show', $periode) }}" class="btn btn-sm btn-info" title="Lihat Pendaftar">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada data periode wisuda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $periodeWisudas->links() }}
    </div>
</div>
@endsection
