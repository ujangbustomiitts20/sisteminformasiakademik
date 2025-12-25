@extends('layouts.app')

@section('title', 'Kelola Absensi')

@section('content')
<div class="page-title">
    <h4>Kelola Absensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Absensi</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-check me-2"></i>Daftar Jadwal Kuliah
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('absensi.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="tahun_akademik" class="form-select" onchange="this.form.submit()">
                        @foreach($tahunAkademik as $ta)
                        <option value="{{ $ta->id }}" {{ $tahunAkademikAktif?->id == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_lengkap }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Dosen</th>
                        <th>Hari/Jam</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalKuliah as $index => $jk)
                    <tr>
                        <td>{{ $jadwalKuliah->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $jk->mataKuliah->nama }}</strong>
                            <br><small class="text-muted">{{ $jk->mataKuliah->kode }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $jk->kelas }}</span></td>
                        <td>{{ $jk->dosen->nama }}</td>
                        <td>
                            {{ $jk->hari }}<br>
                            <small>{{ \Carbon\Carbon::parse($jk->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jk->jam_selesai)->format('H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('absensi.show', $jk) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i>Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox display-6 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada jadwal kuliah</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $jadwalKuliah->withQueryString()->links() }}
    </div>
</div>
@endsection
