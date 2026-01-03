@extends('layouts.app')

@section('title', 'Mitra Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Mitra Kegiatan Lapangan</h1>
        <a href="{{ route('admin.kegiatan-lapangan.mitra.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Mitra
        </a>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.jenis.index') }}">Jenis Kegiatan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.kegiatan-lapangan.mitra.index') }}">Mitra Kegiatan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.periode.index') }}">Periode</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}">Pendaftaran</a>
        </li>
    </ul>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Mitra</th>
                            <th>Bidang Usaha</th>
                            <th>Kota</th>
                            <th>PIC</th>
                            <th>Kuota</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mitras as $mitra)
                        <tr>
                            <td>
                                <strong>{{ $mitra->nama }}</strong><br>
                                <small class="text-muted">{{ $mitra->email }}</small>
                            </td>
                            <td>{{ $mitra->bidang_usaha }}</td>
                            <td>{{ $mitra->kota }}, {{ $mitra->provinsi }}</td>
                            <td>
                                {{ $mitra->nama_kontak ?? '-' }}<br>
                                <small class="text-muted">{{ $mitra->jabatan_kontak ?? '' }}</small>
                            </td>
                            <td class="text-center">{{ $mitra->kuota_mahasiswa ?? '-' }}</td>
                            <td>
                                @if($mitra->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.kegiatan-lapangan.mitra.edit', $mitra->hashid) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kegiatan-lapangan.mitra.destroy', $mitra->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus mitra ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada mitra kegiatan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $mitras->links() }}
        </div>
    </div>
</div>
@endsection
