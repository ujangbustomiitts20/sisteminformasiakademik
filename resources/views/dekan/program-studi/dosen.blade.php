@extends('layouts.app')

@section('title', 'Dosen ' . $programStudi->nama)

@section('content')
<div class="page-title">
    <h4>Dosen Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.program-studi.index') }}">Program Studi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.program-studi.show', $programStudi->hashid) }}">{{ $programStudi->nama }}</a></li>
            <li class="breadcrumb-item active">Dosen</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $totalDosen }}</h3>
                <small>Total Dosen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $statusCount['aktif'] ?? 0 }}</h3>
                <small>Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $totalMahasiswaBimbingan }}</h3>
                <small>Total Mhs Bimbingan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $totalBimbinganTA }}</h3>
                <small>Bimbingan TA</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-person-badge me-2"></i>Daftar Dosen - {{ $programStudi->nama }}
        </h6>
        <div>
            <form action="" method="GET" class="d-flex gap-2">
                <select name="jabatan" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatanList as $jabatan)
                    <option value="{{ $jabatan }}" {{ request('jabatan') == $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIDN/Nama..." value="{{ request('search') }}" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search') || request('jabatan'))
                <a href="{{ route('dekan.program-studi.dosen', $programStudi->hashid) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>NIDN</th>
                        <th>Nama</th>
                        <th>Jabatan Fungsional</th>
                        <th>Pendidikan</th>
                        <th class="text-center">Mhs Bimbingan</th>
                        <th class="text-center">Bimbingan TA</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $index => $dosen)
                    <tr>
                        <td>{{ $dosens->firstItem() + $index }}</td>
                        <td><strong>{{ $dosen->nidn ?? '-' }}</strong></td>
                        <td>
                            {{ $dosen->nama }}
                            @if($dosen->user && $dosen->user->email)
                            <br><small class="text-muted">{{ $dosen->user->email }}</small>
                            @endif
                        </td>
                        <td>{{ $dosen->jabatan_fungsional ?? '-' }}</td>
                        <td>{{ $dosen->pendidikan_terakhir ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $dosen->mahasiswa_bimbingan_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning">{{ $dosen->tugas_akhir_bimbingan_count }}</span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.dosen.show', $dosen->hashid) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data dosen
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($dosens->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $dosens->firstItem() }} - {{ $dosens->lastItem() }} dari {{ $dosens->total() }} dosen
            </small>
            {{ $dosens->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>

<div class="mt-3">
    <a href="{{ route('dekan.program-studi.show', $programStudi->hashid) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Detail Program Studi
    </a>
</div>
@endsection
