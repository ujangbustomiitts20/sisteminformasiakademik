@extends('layouts.app')

@section('title', 'Daftar Event Kalender')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Daftar Event Kalender</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kalender.index') }}">Kalender Akademik</a></li>
                    <li class="breadcrumb-item active">Daftar Event</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('kalender.index') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-calendar me-1"></i>Lihat Kalender
            </a>
            <a href="{{ route('kalender.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Event
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Tahun Akademik</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $index => $event)
                        <tr>
                            <td>{{ $events->firstItem() + $index }}</td>
                            <td>
                                <span class="d-inline-block me-2" style="width: 15px; height: 15px; background: {{ $event->warna }}; border-radius: 3px;"></span>
                                {{ $event->judul }}
                            </td>
                            <td>
                                @switch($event->jenis)
                                    @case('akademik')
                                        <span class="badge bg-primary">Akademik</span>
                                        @break
                                    @case('libur')
                                        <span class="badge bg-danger">Libur</span>
                                        @break
                                    @case('ujian')
                                        <span class="badge bg-warning text-dark">Ujian</span>
                                        @break
                                    @case('pendaftaran')
                                        <span class="badge bg-success">Pendaftaran</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">Lainnya</span>
                                @endswitch
                            </td>
                            <td>
                                {{ $event->tanggal_mulai->format('d/m/Y') }}
                                @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                                - {{ $event->tanggal_selesai->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>
                                @if($event->tahunAkademik)
                                {{ $event->tahunAkademik->tahun }} Sem. {{ $event->tahunAkademik->semester }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($event->tanggal_mulai->isFuture())
                                <span class="badge bg-info">Akan Datang</span>
                                @elseif($event->tanggal_selesai && $event->tanggal_selesai->isPast())
                                <span class="badge bg-secondary">Selesai</span>
                                @else
                                <span class="badge bg-success">Berlangsung</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kalender.edit', Hashids::encode($event->id)) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kalender.destroy', Hashids::encode($event->id)) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus event ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-calendar-x display-4 text-muted"></i>
                                <p class="text-muted mt-2">Tidak ada event kalender</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($events->hasPages())
        <div class="card-footer">
            {{ $events->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
