@extends('layouts.app')

@section('title', 'Beasiswa Tersedia')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Beasiswa Tersedia</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Beasiswa</li>
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

    <div class="row">
        @forelse($beasiswa as $item)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">{{ $item->nama }}</h5>
                            @php
                                $jenisColor = match($item->jenis) {
                                    'Beasiswa' => 'success',
                                    'Potongan' => 'info',
                                    'Keringanan' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $jenisColor }}">{{ $item->jenis }}</span>
                        </div>
                        @if($item->kuota)
                            <span class="badge bg-{{ ($item->kuota - $item->kuota_terpakai) > 0 ? 'primary' : 'danger' }}">
                                Sisa: {{ $item->kuota - $item->kuota_terpakai }}/{{ $item->kuota }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Potongan</label>
                        <h4 class="text-success mb-0">
                            @if($item->tipe_potongan === 'Persen')
                                {{ $item->nilai_potongan }}%
                            @else
                                Rp {{ number_format($item->nilai_potongan, 0, ',', '.') }}
                            @endif
                        </h4>
                    </div>

                    @if($item->sumber_dana)
                    <div class="mb-3">
                        <label class="text-muted small">Sumber Dana</label>
                        <p class="mb-0">{{ $item->sumber_dana }}</p>
                    </div>
                    @endif

                    @if($item->persyaratan)
                    <div class="mb-3">
                        <label class="text-muted small">Persyaratan</label>
                        <p class="mb-0 small">{{ Str::limit($item->persyaratan, 150) }}</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    @if(isset($pengajuanSaya[$item->id]))
                        @php
                            $status = $pengajuanSaya[$item->id];
                            $statusColor = match($status) {
                                'Diajukan' => 'warning',
                                'Disetujui' => 'success',
                                'Ditolak' => 'danger',
                                'Dicabut' => 'secondary',
                                default => 'secondary'
                            };
                        @endphp
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $statusColor }}">{{ $status }}</span>
                            @if($status === 'Diajukan')
                                <small class="text-muted">Menunggu persetujuan</small>
                            @elseif($status === 'Disetujui')
                                <small class="text-success"><i class="bi bi-check-circle me-1"></i>Anda adalah penerima</small>
                            @endif
                        </div>
                    @else
                        @if($item->kuota && ($item->kuota - $item->kuota_terpakai) <= 0)
                            <button class="btn btn-secondary w-100" disabled>Kuota Habis</button>
                        @else
                            <form action="{{ route('beasiswa.ajukan', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Ajukan beasiswa ini?')">
                                    <i class="bi bi-send me-1"></i>Ajukan
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-award display-3 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Tidak ada beasiswa tersedia saat ini</h5>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
