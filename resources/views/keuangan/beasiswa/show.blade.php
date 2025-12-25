@extends('layouts.app')

@section('title', 'Detail Beasiswa')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Detail Beasiswa</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('beasiswa.index') }}">Beasiswa</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-award me-2"></i>Informasi Beasiswa</h5>
                </div>
                <div class="card-body">
                    <h4 class="mb-3">{{ $beasiswa->nama }}</h4>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Jenis</td>
                            <td class="text-end">
                                @php
                                    $jenisColor = match($beasiswa->jenis) {
                                        'Beasiswa' => 'success',
                                        'Potongan' => 'info',
                                        'Keringanan' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $jenisColor }}">{{ $beasiswa->jenis }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Potongan</td>
                            <td class="text-end fw-bold">
                                @if($beasiswa->tipe_potongan === 'Persen')
                                    {{ $beasiswa->nilai_potongan }}%
                                @else
                                    Rp {{ number_format($beasiswa->nilai_potongan, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kuota</td>
                            <td class="text-end">{{ $beasiswa->kuota ?? 'Tidak Terbatas' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sisa Kuota</td>
                            <td class="text-end">
                                @if($beasiswa->kuota)
                                    <span class="badge bg-{{ $beasiswa->sisa_kuota > 0 ? 'success' : 'danger' }}">
                                        {{ $beasiswa->sisa_kuota }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sumber Dana</td>
                            <td class="text-end">{{ $beasiswa->sumber_dana ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="text-end">
                                @if($beasiswa->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($beasiswa->persyaratan)
                    <hr>
                    <h6>Persyaratan:</h6>
                    <p class="text-muted small">{{ $beasiswa->persyaratan }}</p>
                    @endif
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('beasiswa.edit', $beasiswa) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <a href="{{ route('beasiswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Daftar Penerima</h5>
                    <span class="badge bg-primary">{{ $beasiswa->penerima->count() }} Penerima</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Tahun Akademik</th>
                                    <th>Periode</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($beasiswa->penerima as $penerima)
                                <tr>
                                    <td>{{ $penerima->mahasiswa->nim ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $penerima->mahasiswa->nama ?? '-' }}</strong>
                                        <br><small class="text-muted">{{ $penerima->mahasiswa->programStudi->nama ?? '-' }}</small>
                                    </td>
                                    <td>{{ $penerima->tahunAkademik->nama ?? '-' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($penerima->tanggal_mulai)->format('d/m/Y') }}
                                        @if($penerima->tanggal_selesai)
                                            - {{ \Carbon\Carbon::parse($penerima->tanggal_selesai)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="text-center">{!! $penerima->status_badge !!}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada penerima beasiswa
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
