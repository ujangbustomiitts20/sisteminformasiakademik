@extends('layouts.app')

@section('title', 'Pertanyaan EDOM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Pertanyaan EDOM</h1>
            <p class="text-muted mb-0">Kelola daftar pertanyaan evaluasi dosen</p>
        </div>
        <div>
            <a href="{{ route('admin.edom.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('admin.edom.pertanyaan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Pertanyaan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @foreach($kategoris as $kode => $namaKategori)
    @php
        $pertanyaanKategori = $pertanyaan->where('kategori', $kode);
    @endphp
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-bookmark me-2"></i>{{ $namaKategori }}
                <span class="badge bg-secondary ms-2">{{ $pertanyaanKategori->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($pertanyaanKategori->isEmpty())
                <p class="text-muted text-center py-3">Belum ada pertanyaan untuk kategori ini</p>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">Kode</th>
                            <th width="60">Urutan</th>
                            <th>Pertanyaan</th>
                            <th width="80">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pertanyaanKategori as $p)
                        <tr>
                            <td><code>{{ $p->kode }}</code></td>
                            <td>{{ $p->urutan }}</td>
                            <td>{{ $p->pertanyaan }}</td>
                            <td>
                                @if($p->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.edom.pertanyaan.edit', $p->hashid) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.edom.pertanyaan.destroy', $p->hashid) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="mt-3">
        {{ $pertanyaan->links() }}
    </div>
</div>
@endsection
