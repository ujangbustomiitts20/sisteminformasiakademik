@extends('layouts.app')

@section('title', 'Kelola Pertemuan')

@section('content')
<div class="page-title">
    <h4>Kelola Pertemuan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li class="breadcrumb-item active">Pertemuan</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $jadwalKuliah->mataKuliah->nama }}</h5>
                        <p class="mb-0">
                            <small>{{ $jadwalKuliah->mataKuliah->kode }} | Kelas {{ $jadwalKuliah->kelas }} | {{ $jadwalKuliah->mataKuliah->sks }} SKS</small>
                        </p>
                        <p class="mb-0">
                            <small><i class="bi bi-calendar3 me-1"></i>{{ $jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}</small>
                        </p>
                    </div>
                    <i class="bi bi-journal-text display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Pertemuan</h6>
                        <h3 class="mb-0">{{ $jumlahPertemuan }}</h3>
                        <small>Pertemuan</small>
                    </div>
                    <i class="bi bi-calendar-check display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ol me-2"></i>Daftar Pertemuan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="80">Pertemuan</th>
                        <th>Judul</th>
                        <th width="120">Jenis</th>
                        <th width="110">Tanggal</th>
                        <th width="100" class="text-center">Approval</th>
                        <th width="80" class="text-center">Materi</th>
                        <th width="80" class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= $jumlahPertemuan; $i++)
                    @php $p = $pertemuanList->get($i); @endphp
                    <tr>
                        <td>
                            <span class="badge bg-secondary">Ke-{{ $i }}</span>
                        </td>
                        <td>
                            @if($p)
                                <strong>{{ $p->judul }}</strong>
                                @if($p->deskripsi)
                                <br><small class="text-muted">{{ Str::limit($p->deskripsi, 50) }}</small>
                                @endif
                            @else
                                <span class="text-muted">Belum diatur</span>
                            @endif
                        </td>
                        <td>
                            @if($p)
                                <span class="badge {{ $p->jenis_badge_class }}">{{ $p->jenis }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p && $p->tanggal)
                                {{ $p->tanggal->format('d M Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p && $p->tanggal)
                                {!! $p->approval_badge !!}
                                @if($p->rejection_note)
                                <br><small class="text-danger" title="{{ $p->rejection_note }}">{{ Str::limit($p->rejection_note, 20) }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p)
                                @if($p->file_materi)
                                    <a href="{{ route('pertemuan.download', $p) }}" class="btn btn-sm btn-outline-primary" title="Download File">
                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                    </a>
                                @endif
                                @if($p->link_materi)
                                    <a href="{{ $p->link_materi }}" target="_blank" class="btn btn-sm btn-outline-info" title="Link Materi">
                                        <i class="bi bi-link-45deg"></i>
                                    </a>
                                @endif
                                @if(!$p->file_materi && !$p->link_materi)
                                    <span class="text-muted">-</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p)
                                @if($p->is_published)
                                    <span class="badge bg-success"><i class="bi bi-eye me-1"></i>Publik</span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-eye-slash me-1"></i>Draft</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('pertemuan.create', [$jadwalKuliah, $i]) }}" class="btn btn-outline-primary" title="{{ $p ? 'Edit' : 'Tambah' }}">
                                    <i class="bi bi-{{ $p ? 'pencil' : 'plus-lg' }}"></i>
                                </a>
                                @if($p)
                                <form action="{{ route('pertemuan.toggle', [$jadwalKuliah, $i]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-{{ $p->is_published ? 'warning' : 'success' }}" title="{{ $p->is_published ? 'Sembunyikan' : 'Publikasikan' }}">
                                        <i class="bi bi-{{ $p->is_published ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('pertemuan.destroy', [$jadwalKuliah, $i]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pertemuan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('absensi.show', $jadwalKuliah) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Rekap Absensi
    </a>
</div>
@endsection
