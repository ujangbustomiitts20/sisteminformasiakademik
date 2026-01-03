@extends('layouts.app')

@section('title', 'Periode Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Periode Ujian</h1>
            <p class="text-muted mb-0">Kelola periode ujian dan kartu peserta ujian</p>
        </div>
        <a href="{{ route('admin.periode-ujian.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Periode
        </a>
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
                            <th>Jenis</th>
                            <th>Tahun Akademik</th>
                            <th>Tanggal</th>
                            <th>Min. Kehadiran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $i => $periode)
                        <tr>
                            <td>{{ $periodes->firstItem() + $i }}</td>
                            <td>{{ $periode->nama }}</td>
                            <td><span class="badge bg-info">{{ $periode->jenis }}</span></td>
                            <td>{{ $periode->tahunAkademik->nama ?? '-' }}</td>
                            <td>
                                {{ $periode->tanggal_mulai->format('d/m/Y') }} - 
                                {{ $periode->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td>{{ $periode->minimal_kehadiran }}%</td>
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
                                    <a href="{{ route('admin.periode-ujian.show', $periode->hashid) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.periode-ujian.edit', $periode->hashid) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.periode-ujian.generate-kartu', $periode->hashid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary" title="Generate Kartu Ujian">
                                            <i class="bi bi-card-checklist"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.periode-ujian.destroy', $periode->hashid) }}" method="POST" 
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
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada periode ujian
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
