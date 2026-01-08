@extends('layouts.app')

@section('title', 'Kelola Slider')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-images me-2"></i>Kelola Slider</h5>
            <a href="{{ route('pmb.konten-pmb.slider.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Slider
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
                            <th width="60">Urutan</th>
                            <th width="100">Gambar</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th width="80">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sliders as $slider)
                        <tr>
                            <td>{{ $slider->urutan }}</td>
                            <td>
                                <img src="{{ $slider->gambar_url }}" alt="" class="rounded" style="width: 80px; height: 50px; object-fit: cover;">
                            </td>
                            <td>{{ $slider->judul }}</td>
                            <td>{{ Str::limit($slider->deskripsi, 50) }}</td>
                            <td>
                                @if($slider->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pmb.konten-pmb.slider.edit', $slider->hashid) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('pmb.konten-pmb.slider.destroy', $slider->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                            <td colspan="6" class="text-center text-muted py-4">Belum ada slider</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
