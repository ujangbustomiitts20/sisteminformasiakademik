@extends('layouts.app')

@section('title', 'Periode EDOM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Periode EDOM</h1>
            <p class="text-muted mb-0">Kelola periode evaluasi dosen oleh mahasiswa</p>
        </div>
        <div>
            <a href="{{ route('admin.edom.pertanyaan') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-list-check me-1"></i>Pertanyaan
            </a>
            <a href="{{ route('admin.edom.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Periode
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
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
                            <th>Nama Periode</th>
                            <th>Tahun Akademik</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $i => $periode)
                        <tr>
                            <td>{{ $periodes->firstItem() + $i }}</td>
                            <td>{{ $periode->nama }}</td>
                            <td>{{ $periode->tahunAkademik->nama ?? '-' }}</td>
                            <td>{{ $periode->tanggal_mulai->format('d/m/Y') }}</td>
                            <td>{{ $periode->tanggal_selesai->format('d/m/Y') }}</td>
                            <td>
                                @if($periode->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($periode->status == 'selesai')
                                    <span class="badge bg-secondary">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.edom.show', $periode->hashid) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.edom.edit', $periode->hashid) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.edom.hitung-rekap', $periode->hashid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary" title="Hitung Rekap">
                                            <i class="bi bi-calculator"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.edom.destroy', $periode->hashid) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada periode EDOM
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $periodes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
