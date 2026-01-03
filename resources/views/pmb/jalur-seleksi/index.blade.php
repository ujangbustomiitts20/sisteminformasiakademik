@extends('layouts.app')

@section('title', 'Jalur Seleksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Jalur Seleksi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Jalur Seleksi</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.jalur-seleksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Jalur
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
                            <th>Kode</th>
                            <th>Nama Jalur</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Pendaftar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jalurs as $jalur)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $jalur->kode }}</span></td>
                            <td>
                                <a href="{{ route('pmb.jalur-seleksi.show', $jalur->hashid) }}">
                                    {{ $jalur->nama }}
                                </a>
                            </td>
                            <td>{{ Str::limit($jalur->deskripsi, 50) ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $jalur->calon_mahasiswa_count }}</span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('pmb.jalur-seleksi.toggle-active', $jalur->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $jalur->is_active ? 'success' : 'secondary' }}">
                                        @if($jalur->is_active)
                                            <i class="fas fa-check"></i> Aktif
                                        @else
                                            <i class="fas fa-times"></i> Nonaktif
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.jalur-seleksi.show', $jalur->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pmb.jalur-seleksi.edit', $jalur->hashid) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pmb.jalur-seleksi.destroy', $jalur->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data jalur seleksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $jalurs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
