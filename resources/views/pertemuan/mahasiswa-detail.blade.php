@extends('layouts.app')

@section('title', 'Materi - ' . $jadwalKuliah->mataKuliah->nama)

@section('content')
<div class="page-title">
    <h4>Materi Perkuliahan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pertemuan.mahasiswa') }}">Materi Kuliah</a></li>
            <li class="breadcrumb-item active">{{ $jadwalKuliah->mataKuliah->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $jadwalKuliah->mataKuliah->nama }}</h5>
                        <p class="mb-0">
                            <small>{{ $jadwalKuliah->mataKuliah->kode }} | Kelas {{ $jadwalKuliah->kelas }} | {{ $jadwalKuliah->mataKuliah->sks }} SKS</small>
                        </p>
                        <p class="mb-0">
                            <small><i class="bi bi-person me-1"></i>{{ $jadwalKuliah->dosen->nama }}</small>
                        </p>
                    </div>
                    <i class="bi bi-journal-text display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Materi Tersedia</h6>
                        <h3 class="mb-0">{{ $pertemuanList->count() }} / {{ $jumlahPertemuan }}</h3>
                        <small>Pertemuan</small>
                    </div>
                    <i class="bi bi-check-circle display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ol me-2"></i>Daftar Pertemuan
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionPertemuan">
            @for($i = 1; $i <= $jumlahPertemuan; $i++)
            @php $p = $pertemuanList->get($i); @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $i }}">
                    <button class="accordion-button {{ $p ? '' : 'collapsed' }}" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}" 
                            aria-expanded="{{ $p ? 'true' : 'false' }}" aria-controls="collapse{{ $i }}">
                        <div class="d-flex align-items-center w-100 me-3">
                            <span class="badge bg-secondary me-3">{{ $i }}</span>
                            @if($p)
                                <div class="flex-grow-1">
                                    <strong>{{ $p->judul }}</strong>
                                    <span class="badge {{ $p->jenis_badge_class }} ms-2">{{ $p->jenis }}</span>
                                </div>
                                @if($p->tanggal)
                                <small class="text-muted">{{ $p->tanggal->format('d M Y') }}</small>
                                @endif
                            @else
                                <span class="text-muted flex-grow-1">Belum tersedia</span>
                            @endif
                        </div>
                    </button>
                </h2>
                <div id="collapse{{ $i }}" class="accordion-collapse collapse {{ $p && $i == 1 ? 'show' : '' }}" 
                     aria-labelledby="heading{{ $i }}" data-bs-parent="#accordionPertemuan">
                    <div class="accordion-body">
                        @if($p)
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>{{ $p->judul }}</h6>
                                    <p class="mb-2">
                                        <span class="badge {{ $p->jenis_badge_class }}">{{ $p->jenis_label }}</span>
                                        @if($p->tanggal)
                                        <span class="ms-2 text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $p->tanggal->format('d F Y') }}
                                        </span>
                                        @endif
                                    </p>
                                    @if($p->deskripsi)
                                    <div class="bg-light p-3 rounded mb-3">
                                        {!! nl2br(e($p->deskripsi)) !!}
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($p->file_materi || $p->link_materi)
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <i class="bi bi-paperclip me-2"></i>Lampiran
                                        </div>
                                        <div class="card-body">
                                            @if($p->file_materi)
                                            <a href="{{ route('pertemuan.download', $p) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                                <i class="bi bi-download me-1"></i>Download File
                                            </a>
                                            @endif
                                            @if($p->link_materi)
                                            <a href="{{ $p->link_materi }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                                <i class="bi bi-link-45deg me-1"></i>Buka Link
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-muted text-center py-3">
                                        <i class="bi bi-file-earmark-x display-6"></i>
                                        <p class="mb-0 small">Tidak ada lampiran</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-hourglass-split display-4 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Materi pertemuan ini belum tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('pertemuan.mahasiswa') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Mata Kuliah
    </a>
</div>
@endsection
