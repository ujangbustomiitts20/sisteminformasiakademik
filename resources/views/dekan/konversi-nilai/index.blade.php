@extends('layouts.app')

@section('title', 'Konversi Nilai')

@section('content')
<div class="page-title">
    <h4>Monitoring Konversi Nilai - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Konversi Nilai</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="prodi" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Pengajuan Konversi Nilai</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Asal Perguruan Tinggi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($konversis as $konversi)
                    <tr>
                        <td>
                            <strong>{{ $konversi->mahasiswa->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $konversi->mahasiswa->nim ?? '-' }}</small>
                        </td>
                        <td>{{ $konversi->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ $konversi->asal_perguruan_tinggi ?? '-' }}</td>
                        <td>{{ $konversi->created_at ? $konversi->created_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $konversi->status == 'selesai' ? 'success' : ($konversi->status == 'diproses' ? 'info' : 'warning') }}">
                                {{ ucfirst($konversi->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.konversi-nilai.show', $konversi) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Tidak ada pengajuan konversi nilai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $konversis->withQueryString()->links() }}
    </div>
</div>
@endsection
