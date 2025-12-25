@extends('layouts.app')

@section('title', 'Bimbingan Akademik Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Bimbingan Akademik Saya</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bimbingan</li>
            </ol>
        </nav>
    </div>
    @if($dosenWali)
    <a href="{{ route('bimbingan.mahasiswa.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Ajukan Bimbingan
    </a>
    @endif
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

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list me-2"></i>Riwayat Bimbingan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Topik</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bimbingans as $bimbingan)
                            <tr>
                                <td>{{ $bimbingan->tanggal_bimbingan->format('d/m/Y') }}</td>
                                <td><span class="badge bg-{{ $bimbingan->jenis_badge }}">{{ $bimbingan->jenis }}</span></td>
                                <td>{{ Str::limit($bimbingan->topik, 40) }}</td>
                                <td><span class="badge bg-{{ $bimbingan->status_badge }}">{{ $bimbingan->status }}</span></td>
                                <td>
                                    <a href="{{ route('bimbingan.show', $bimbingan) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($bimbingan->status == 'Dijadwalkan')
                                    <form action="{{ route('bimbingan.cancel', $bimbingan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Batalkan bimbingan ini?')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">Belum ada riwayat bimbingan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($bimbingans->hasPages())
            <div class="card-footer">
                {{ $bimbingans->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Dosen Wali
            </div>
            <div class="card-body">
                @if($dosenWali)
                <div class="text-center mb-3">
                    <img src="{{ $dosenWali->foto ? asset('storage/' . $dosenWali->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($dosenWali->nama) }}" 
                        class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover;">
                    <h6 class="mb-0">{{ $dosenWali->nama }}</h6>
                    <small class="text-muted">{{ $dosenWali->nidn }}</small>
                </div>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $dosenWali->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon</td>
                        <td>{{ $dosenWali->telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $dosenWali->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                </table>
                @else
                <div class="text-center text-muted py-3">
                    <i class="bi bi-person-x" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Dosen wali belum ditetapkan</p>
                    <small>Hubungi admin untuk info lebih lanjut</small>
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Ajukan bimbingan untuk konsultasi akademik, KRS, atau masalah lainnya.
                </p>
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Dosen wali akan memberikan arahan dan rekomendasi.
                </p>
                <p class="small text-muted mb-0">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Jadwal bimbingan yang diajukan dapat dibatalkan sebelum pelaksanaan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
