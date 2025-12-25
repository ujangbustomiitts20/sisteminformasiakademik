@extends('layouts.app')

@section('title', 'Absensi - Dosen')

@section('content')
<div class="page-title">
    <h4>Kelola Absensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Absensi</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-check me-2"></i>Jadwal Mengajar Saya
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('absensi.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="tahun_akademik" class="form-select" onchange="this.form.submit()">
                        @foreach($tahunAkademik as $ta)
                        <option value="{{ $ta->id }}" {{ $tahunAkademikAktif?->id == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_lengkap }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            @forelse($jadwalKuliah as $jk)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border">
                    <div class="card-body">
                        <h6 class="card-title">{{ $jk->mataKuliah->nama }}</h6>
                        <p class="card-text mb-1">
                            <small class="text-muted">
                                <i class="bi bi-tag me-1"></i>{{ $jk->mataKuliah->kode }} | Kelas {{ $jk->kelas }}
                            </small>
                        </p>
                        <p class="card-text mb-1">
                            <small>
                                <i class="bi bi-calendar3 me-1"></i>{{ $jk->hari }}, {{ \Carbon\Carbon::parse($jk->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jk->jam_selesai)->format('H:i') }}
                            </small>
                        </p>
                        <p class="card-text mb-3">
                            <small>
                                <i class="bi bi-door-open me-1"></i>{{ $jk->ruangan->nama }}
                            </small>
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('absensi.show', $jk) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-list-check me-1"></i>Rekap
                            </a>
                            <a href="{{ route('absensi.create', $jk) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Manual
                            </a>
                            <a href="{{ route('absensi.kode', $jk) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-qr-code me-1"></i>Mandiri
                            </a>
                            <a href="{{ route('pertemuan.index', $jk) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-journal-text me-1"></i>Materi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-4 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada jadwal mengajar pada semester ini</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
