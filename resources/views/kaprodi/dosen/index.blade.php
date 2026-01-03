@extends('layouts.app')

@section('title', 'Dosen Prodi')

@section('content')
<div class="page-title">
    <h4>Dosen Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dosen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIDN</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan Fungsional</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $dosen)
                    <tr>
                        <td>{{ $dosen->nidn ?? '-' }}</td>
                        <td>{{ $dosen->nama }}</td>
                        <td>{{ $dosen->email ?? ($dosen->user->email ?? '-') }}</td>
                        <td>{{ $dosen->jabatan_fungsional ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $dosen->status == 'Aktif' ? 'success' : 'secondary' }}">
                                {{ $dosen->status ?? 'Aktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kaprodi.dosen.show', $dosen) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada data dosen
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $dosens->links() }}
    </div>
</div>
@endsection
