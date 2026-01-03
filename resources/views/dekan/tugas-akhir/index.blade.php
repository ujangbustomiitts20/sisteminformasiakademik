@extends('layouts.app')

@section('title', 'Tugas Akhir Fakultas')

@section('content')
<div class="page-title">
    <h4>Monitoring Tugas Akhir Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tugas Akhir</li>
        </ol>
    </nav>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h4 class="text-secondary">{{ $stats['draft'] }}</h4>
                <small>Draft</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h4 class="text-warning">{{ $stats['diajukan'] }}</h4>
                <small>Diajukan</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h4 class="text-info">{{ $stats['bimbingan'] }}</h4>
                <small>Bimbingan</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h4 class="text-primary">{{ $stats['sidang'] }}</h4>
                <small>Sidang</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h4 class="text-success">{{ $stats['selesai'] }}</h4>
                <small>Selesai</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $fakultas->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="prodi" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="bimbingan" {{ request('status') == 'bimbingan' ? 'selected' : '' }}>Bimbingan</option>
                    <option value="sidang" {{ request('status') == 'sidang' ? 'selected' : '' }}>Sidang</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Judul</th>
                        <th>Pembimbing 1</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasAkhirs as $ta)
                    <tr>
                        <td>{{ $ta->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $ta->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $ta->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ Str::limit($ta->judul, 35) }}</td>
                        <td>{{ $ta->pembimbing1->nama ?? '-' }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'diajukan' => 'warning',
                                    'bimbingan' => 'info',
                                    'seminar' => 'primary',
                                    'sidang' => 'primary',
                                    'revisi' => 'warning',
                                    'selesai' => 'success',
                                    'ditolak' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$ta->status] ?? 'secondary' }}">
                                {{ ucfirst($ta->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada data tugas akhir
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $tugasAkhirs->links() }}
    </div>
</div>
@endsection
