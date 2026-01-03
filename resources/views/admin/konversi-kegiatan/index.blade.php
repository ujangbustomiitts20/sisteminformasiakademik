@extends('layouts.app')

@section('title', 'Pengajuan Konversi Kegiatan')

@section('content')
<div class="page-title">
    <h4>Pengajuan Konversi Kegiatan (RPL)</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Konversi Kegiatan</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total Pengajuan</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['diajukan'] }}</h3>
                    <small class="text-muted">Menunggu Proses</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-check-circle fs-4 text-success"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['disetujui'] }}</h3>
                    <small class="text-muted">Disetujui</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                    <i class="bi bi-x-circle fs-4 text-danger"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['ditolak'] }}</h3>
                    <small class="text-muted">Ditolak</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Pengajuan Konversi Kegiatan</h6>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Program Studi</label>
                <select name="prodi" class="form-select">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik') == $ta->id ? 'selected' : '' }}>{{ $ta->tahun }} {{ $ta->semester }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="NIM / Nama / No.Pengajuan" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>

        <!-- Table -->
        @if($pengajuans->isEmpty())
        <div class="text-center py-4">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="text-muted mt-2">Tidak ada data pengajuan konversi kegiatan</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Tanggal</th>
                        <th>Jml Kegiatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuans as $pengajuan)
                    <tr>
                        <td><strong>{{ $pengajuan->no_pengajuan }}</strong></td>
                        <td>
                            {{ $pengajuan->mahasiswa->nama }}
                            <br><small class="text-muted">{{ $pengajuan->mahasiswa->nim }}</small>
                        </td>
                        <td>{{ $pengajuan->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-info">{{ $pengajuan->details->count() }} kegiatan</span>
                            @if($pengajuan->total_sks > 0)
                            <br><small class="text-success">{{ $pengajuan->total_sks }} SKS diakui</small>
                            @endif
                        </td>
                        <td>{!! $pengajuan->status_badge !!}</td>
                        <td>
                            <a href="{{ route('admin.konversi-kegiatan.show', $pengajuan) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pengajuans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
