@extends('layouts.app')

@section('title', 'Detail Periode EDOM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $edom->nama }}</h1>
            <p class="text-muted mb-0">{{ $edom->tahunAkademik->nama ?? '-' }}</p>
        </div>
        <div>
            <form action="{{ route('admin.edom.hitung-rekap', $edom->hashid) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-calculator me-1"></i>Hitung Ulang Rekap
                </button>
            </form>
            <a href="{{ route('admin.edom.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Periode -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Responden</h6>
                            <h2 class="mb-0">{{ number_format($statistik['total_responden']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Dosen Dievaluasi</h6>
                            <h2 class="mb-0">{{ number_format($statistik['total_dosen']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-badge fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Rata-rata Total</h6>
                            <h2 class="mb-0">{{ number_format($statistik['rata_rata_total'] ?? 0, 2) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-star fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $edom->status == 'aktif' ? 'bg-success' : ($edom->status == 'selesai' ? 'bg-secondary' : 'bg-warning') }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Status</h6>
                            <h2 class="mb-0 text-capitalize">{{ $edom->status }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Periode -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Periode</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Tanggal Mulai</td>
                            <td><strong>{{ $edom->tanggal_mulai->format('d F Y') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal Selesai</td>
                            <td><strong>{{ $edom->tanggal_selesai->format('d F Y') }}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if($edom->deskripsi)
                    <h6>Deskripsi:</h6>
                    <p class="text-muted">{{ $edom->deskripsi }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Per Dosen -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Rekap Evaluasi per Dosen</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Dosen</th>
                            <th>Mata Kuliah</th>
                            <th class="text-center">Pedagogik</th>
                            <th class="text-center">Profesional</th>
                            <th class="text-center">Kepribadian</th>
                            <th class="text-center">Sosial</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Responden</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $i => $r)
                        <tr>
                            <td>{{ $rekap->firstItem() + $i }}</td>
                            <td>{{ $r->dosen->nama ?? '-' }}</td>
                            <td>{{ $r->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_pedagogik, 2) }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_profesional, 2) }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_kepribadian, 2) }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_sosial, 2) }}</td>
                            <td class="text-center"><strong>{{ number_format($r->rata_rata_total, 2) }}</strong></td>
                            <td class="text-center">{{ $r->jumlah_responden }}</td>
                            <td>
                                <span class="badge bg-{{ $r->kategori_badge }}">{{ $r->kategori }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.edom.rekap-dosen', ['edom' => $edom->hashid, 'dosen' => $r->dosen->hashid]) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data evaluasi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $rekap->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
