@extends('layouts.app')

@section('title', 'Periode PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Periode PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Periode</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.periode.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Periode
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
                            <th>Nama Periode</th>
                            <th>Tahun Akademik</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aktif</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $periode)
                        <tr>
                            <td>
                                <a href="{{ route('pmb.periode.show', $periode->hashid) }}">
                                    {{ $periode->nama }}
                                </a>
                            </td>
                            <td>{{ $periode->tahun_akademik }}</td>
                            <td>{{ $periode->tanggal_mulai->format('d M Y') }}</td>
                            <td>{{ $periode->tanggal_selesai->format('d M Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $periode->status_badge }}">{{ $periode->status }}</span>
                            </td>
                            <td class="text-center">
                                @if($periode->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Aktif</span>
                                @else
                                    <form action="{{ route('pmb.periode.set-active', $periode->hashid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Set Aktif">
                                            <i class="bi bi-toggle-off"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.periode.show', $periode->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pmb.periode.edit', $periode->hashid) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('pmb.periode.destroy', $periode->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
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
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data periode PMB
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $periodes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
