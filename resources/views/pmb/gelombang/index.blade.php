@extends('layouts.app')

@section('title', 'Gelombang PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gelombang PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Gelombang</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.gelombang.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Gelombang
        </a>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Periode</th>
                            <th>Gelombang</th>
                            <th>Pendaftaran</th>
                            <th>Ujian</th>
                            <th>Pengumuman</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aktif</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gelombangs as $gelombang)
                        <tr>
                            <td>{{ $gelombang->periodePmb->nama ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('pmb.gelombang.show', $gelombang->hashid) }}">
                                    {{ $gelombang->nama }}
                                </a>
                            </td>
                            <td>
                                <small>
                                    {{ $gelombang->tanggal_mulai_daftar->format('d/m/Y') }} - {{ $gelombang->tanggal_selesai_daftar->format('d/m/Y') }}
                                </small>
                            </td>
                            <td>{{ $gelombang->tanggal_ujian?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $gelombang->tanggal_pengumuman?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $gelombang->status_badge }}">{{ $gelombang->status_pendaftaran }}</span>
                            </td>
                            <td class="text-center">
                                @if($gelombang->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                @else
                                    <form action="{{ route('pmb.gelombang.set-active', $gelombang->hashid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Set Aktif">
                                            <i class="bi bi-toggle-off"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.gelombang.show', $gelombang->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pmb.gelombang.edit', $gelombang->hashid) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('pmb.gelombang.destroy', $gelombang->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data gelombang PMB
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $gelombangs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
