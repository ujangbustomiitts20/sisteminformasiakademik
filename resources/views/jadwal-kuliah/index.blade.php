@extends('layouts.app')

@section('title', 'Kelola Jadwal Kuliah')

@section('content')
<div class="page-title">
    <h4>Kelola Jadwal Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Kuliah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar3 me-2"></i>Daftar Jadwal Kuliah</span>
        <a href="{{ route('jadwal-kuliah.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('jadwal-kuliah.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="tahun_akademik" class="form-select">
                        <option value="">-- Tahun Akademik --</option>
                        @foreach($tahunAkademik as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_akademik') == $ta->id ? 'selected' : '' }}>{{ $ta->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="hari" class="form-select">
                        <option value="">-- Hari --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                        <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="dosen" class="form-select">
                        <option value="">-- Dosen --</option>
                        @foreach($dosen as $d)
                        <option value="{{ $d->id }}" {{ request('dosen') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Mata Kuliah</th>
                        <th width="80">Kelas</th>
                        <th>Dosen</th>
                        <th>Hari/Jam</th>
                        <th>Ruangan</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalKuliah as $index => $jk)
                    <tr>
                        <td>{{ $jadwalKuliah->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $jk->mataKuliah->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $jk->mataKuliah->kode ?? '' }} | {{ $jk->mataKuliah->sks ?? 0 }} SKS</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $jk->kelas }}</span></td>
                        <td>{{ $jk->dosen->nama ?? '-' }}</td>
                        <td>
                            <strong>{{ $jk->hari }}</strong><br>
                            <small>{{ \Carbon\Carbon::parse($jk->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jk->jam_selesai)->format('H:i') }}</small>
                        </td>
                        <td>{{ $jk->ruangan->nama ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('jadwal-kuliah.show', $jk) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pertemuan.admin.index', $jk) }}" class="btn btn-outline-success" title="Jadwal Pertemuan">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                                <a href="{{ route('jadwal-kuliah.edit', $jk) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('jadwal-kuliah.destroy', $jk) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?')">
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
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-calendar3 text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data jadwal kuliah</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jadwalKuliah->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $jadwalKuliah->firstItem() }} - {{ $jadwalKuliah->lastItem() }} dari {{ $jadwalKuliah->total() }} data
            </div>
            {{ $jadwalKuliah->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
