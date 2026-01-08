@extends('layouts.app')

@section('title', 'Kelola Berita')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-newspaper me-2"></i>Kelola Berita & Pengumuman</h5>
            <a href="{{ route('pmb.konten-pmb.berita.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Berita
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="100">Gambar</th>
                            <th>Judul</th>
                            <th width="100">Kategori</th>
                            <th width="120">Tanggal</th>
                            <th width="80">Views</th>
                            <th width="80">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->gambar_url }}" alt="" class="rounded" style="width: 80px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ Str::limit($item->judul, 50) }}</strong>
                                <br><small class="text-muted">{{ $item->slug }}</small>
                            </td>
                            <td><span class="badge bg-primary">{{ ucfirst($item->kategori) }}</span></td>
                            <td>{{ $item->published_at ? $item->published_at->format('d M Y') : '-' }}</td>
                            <td>{{ number_format($item->views ?? 0) }}</td>
                            <td>
                                @if($item->is_published)
                                <span class="badge bg-success">Publish</span>
                                @else
                                <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pmb.konten-pmb.berita.edit', $item->hashid) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('pmb.konten-pmb.berita.destroy', $item->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada berita</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $berita->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
