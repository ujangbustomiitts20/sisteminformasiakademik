@extends('layouts.app')

@section('title', 'Persetujuan KRS')

@section('content')
<div class="page-title">
    <h4>Persetujuan KRS Mahasiswa Perwalian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Persetujuan KRS</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-check2-square me-2"></i>Daftar Mahasiswa Perwalian - {{ $tahunAkademikAktif->nama_lengkap ?? '-' }}
    </div>
    <div class="card-body">
        @if($mahasiswaWali->count() > 0)
            @foreach($mahasiswaWali as $mhs)
            @php
                $krsPending = $mhs->krs->where('status', 'Pending');
                $krsDisetujui = $mhs->krs->where('status', 'Disetujui');
                $totalSks = $mhs->krs->sum(fn($k) => $k->jadwalKuliah->mataKuliah->sks ?? 0);
            @endphp
            <div class="card mb-3 {{ $krsPending->count() > 0 ? 'border-warning' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $mhs->nama }}</strong>
                        <span class="text-muted ms-2">{{ $mhs->nim }}</span>
                        @if($krsPending->count() > 0)
                        <span class="badge bg-warning ms-2">{{ $krsPending->count() }} Menunggu Persetujuan</span>
                        @endif
                    </div>
                    <div>
                        <span class="badge bg-secondary">Semester {{ $mhs->semester_aktif }}</span>
                        <span class="badge bg-info">{{ $totalSks }} SKS</span>
                    </div>
                </div>
                
                @if($mhs->krs->count() > 0)
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th class="text-center">SKS</th>
                                    <th>Jadwal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mhs->krs as $krs)
                                <tr>
                                    <td>
                                        <code>{{ $krs->jadwalKuliah->mataKuliah->kode }}</code>
                                        {{ $krs->jadwalKuliah->mataKuliah->nama }}
                                        <span class="badge bg-secondary">Kelas {{ $krs->jadwalKuliah->kelas }}</span>
                                    </td>
                                    <td class="text-center">{{ $krs->jadwalKuliah->mataKuliah->sks }}</td>
                                    <td>{{ $krs->jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $krs->status == 'Disetujui' ? 'success' : ($krs->status == 'Pending' ? 'warning' : 'danger') }}">
                                            {{ $krs->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($krs->status == 'Pending')
                                        <form action="{{ route('krs.approve', $krs) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('krs.reject', $krs) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($krsPending->count() > 0)
                <div class="card-footer">
                    <form action="{{ route('krs.approve-all', $mhs) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-all me-1"></i>Setujui Semua KRS
                        </button>
                    </form>
                </div>
                @endif
                @else
                <div class="card-body text-center text-muted py-3">
                    Belum ada KRS yang diajukan
                </div>
                @endif
            </div>
            @endforeach
        @else
        <div class="text-center py-4">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0 mt-2">Tidak ada mahasiswa perwalian</p>
        </div>
        @endif
    </div>
</div>
@endsection
