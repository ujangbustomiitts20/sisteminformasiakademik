@extends('layouts.app')

@section('title', 'Kartu Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Kartu Peserta Ujian</h1>
            <p class="text-muted mb-0">Daftar kartu ujian Anda</p>
        </div>
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

    <!-- Info Mahasiswa -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="120">NIM</td>
                            <td><strong>{{ $mahasiswa->nim }}</strong></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td><strong>{{ $mahasiswa->nama }}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="120">Program Studi</td>
                            <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Semester</td>
                            <td>{{ $mahasiswa->semester ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Periode Aktif -->
    @if($periodeAktif->isNotEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Periode ujian aktif:</strong>
        @foreach($periodeAktif as $p)
            <span class="badge bg-primary ms-2">{{ $p->nama }} ({{ $p->jenis }})</span>
        @endforeach
    </div>
    @endif

    <!-- Daftar Kartu Ujian -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>Daftar Kartu Ujian</h5>
        </div>
        <div class="card-body">
            @if($kartuUjian->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-card-checklist fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Belum ada kartu ujian.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Kartu</th>
                            <th>Periode</th>
                            <th>Jenis</th>
                            <th>Kehadiran</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kartuUjian as $i => $kartu)
                        <tr>
                            <td>{{ $kartuUjian->firstItem() + $i }}</td>
                            <td><code>{{ $kartu->nomor_kartu }}</code></td>
                            <td>{{ $kartu->periodeUjian->nama ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $kartu->periodeUjian->jenis ?? '-' }}</span></td>
                            <td>
                                <span class="badge bg-{{ $kartu->persentase_kehadiran >= ($kartu->periodeUjian->minimal_kehadiran ?? 75) ? 'success' : 'danger' }}">
                                    {{ number_format($kartu->persentase_kehadiran, 1) }}%
                                </span>
                            </td>
                            <td>
                                @if($kartu->pembayaran_lunas)
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                @if($kartu->eligible)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Eligible</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Tidak Eligible</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('mahasiswa.kartu-ujian.show', $kartu->hashid) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($kartu->eligible && $kartu->periodeUjian->isBisaCetakKartu())
                                <a href="{{ route('mahasiswa.kartu-ujian.cetak', $kartu->hashid) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-printer me-1"></i>Cetak
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $kartuUjian->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
