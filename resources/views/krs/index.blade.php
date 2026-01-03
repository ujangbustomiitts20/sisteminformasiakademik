@extends('layouts.app')

@section('title', 'KRS')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Kartu Rencana Studi (KRS)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">KRS</li>
            </ol>
        </nav>
    </div>
    <div>
        @if(isset($krsSemesterIni) && $krsSemesterIni->count() > 0)
        <a href="{{ route('cetak.krs') }}" target="_blank" class="btn btn-danger me-2">
            <i class="bi bi-file-pdf me-1"></i>Cetak KRS
        </a>
        @endif
        @if(isset($isPeriodeKrs) && $isPeriodeKrs)
        <a href="{{ route('krs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Ambil Mata Kuliah
        </a>
        @endif
    </div>
</div>

@if(isset($error))
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
</div>
@else

<!-- Info Semester -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h5 class="mb-1">{{ $tahunAkademikAktif->nama_lengkap ?? '-' }}</h5>
                <p class="text-muted mb-0">Tahun Akademik Aktif</p>
            </div>
            <div class="col-md-4 text-md-center">
                <h5 class="mb-1">{{ $totalSks ?? 0 }} SKS</h5>
                <p class="text-muted mb-0">Total SKS Diambil</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-{{ ($modeKrs ?? 'pilihan') === 'paket' ? 'primary' : 'info' }} fs-6">
                    <i class="bi bi-{{ ($modeKrs ?? 'pilihan') === 'paket' ? 'box-seam' : 'hand-index-thumb' }} me-1"></i>
                    Mode: {{ ($modeKrs ?? 'pilihan') === 'paket' ? 'KRS Paket' : 'KRS Pilihan' }}
                </span>
            </div>
        </div>
        
        @if($tahunAkademikAktif && $tahunAkademikAktif->isPeriodeKrs())
        <div class="alert alert-success mt-3 mb-0">
            <i class="bi bi-check-circle me-2"></i>
            Periode pengisian KRS: <strong>{{ $tahunAkademikAktif->mulai_krs->format('d M Y') }}</strong> - <strong>{{ $tahunAkademikAktif->selesai_krs->format('d M Y') }}</strong>
            @if(($modeKrs ?? 'pilihan') === 'paket')
            <br><small>Mode KRS Paket: Mata kuliah sudah ditentukan berdasarkan kurikulum dan semester Anda.</small>
            @endif
        </div>
        @else
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Periode pengisian KRS sudah ditutup
        </div>
        @endif
    </div>
</div>

<!-- Daftar KRS -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-text me-2"></i>Mata Kuliah yang Diambil
    </div>
    <div class="card-body">
        @if(isset($krsSemesterIni) && $krsSemesterIni->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Jadwal</th>
                        <th>Dosen</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krsSemesterIni as $index => $krs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $krs->jadwalKuliah->mataKuliah->kode }}</code></td>
                        <td>
                            <strong>{{ $krs->jadwalKuliah->mataKuliah->nama }}</strong>
                            <br><small class="text-muted">Kelas {{ $krs->jadwalKuliah->kelas }}</small>
                        </td>
                        <td>{{ $krs->jadwalKuliah->mataKuliah->sks }}</td>
                        <td>
                            {{ $krs->jadwalKuliah->hari }}<br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_selesai)->format('H:i') }}</small>
                        </td>
                        <td>{{ $krs->jadwalKuliah->dosen->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $krs->status == 'Disetujui' ? 'success' : ($krs->status == 'Pending' ? 'warning' : 'danger') }}">
                                {{ $krs->status }}
                            </span>
                        </td>
                        <td>
                            @if($krs->status == 'Pending' && isset($isPeriodeKrs) && $isPeriodeKrs)
                            <form action="{{ route('krs.destroy', $krs) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan mata kuliah ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="3" class="text-end">Total SKS:</th>
                        <th>{{ $totalSks }}</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-3 mt-2">Belum ada mata kuliah yang diambil</p>
            @if(isset($isPeriodeKrs) && $isPeriodeKrs)
            <a href="{{ route('krs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Ambil Mata Kuliah Sekarang
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endif
@endsection
