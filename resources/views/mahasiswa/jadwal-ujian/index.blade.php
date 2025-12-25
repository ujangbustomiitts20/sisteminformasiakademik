@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Jadwal Ujian</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Jadwal Ujian</li>
            </ol>
        </nav>
    </div>
</div>

@if(!$tahunAkademik)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Tidak ada tahun akademik aktif saat ini.
</div>
@else

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Total Ujian</small>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    </div>
                    <i class="bi bi-journal-text" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>UTS</small>
                        <h4 class="mb-0">{{ $stats['uts'] }}</h4>
                    </div>
                    <i class="bi bi-file-earmark-text" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>UAS</small>
                        <h4 class="mb-0">{{ $stats['uas'] }}</h4>
                    </div>
                    <i class="bi bi-file-earmark-check" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Akan Datang</small>
                        <h4 class="mb-0">{{ $stats['upcoming'] }}</h4>
                    </div>
                    <i class="bi bi-clock" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="jenis" class="form-select">
                    <option value="">-- Semua Jenis --</option>
                    <option value="UTS" {{ request('jenis') == 'UTS' ? 'selected' : '' }}>UTS</option>
                    <option value="UAS" {{ request('jenis') == 'UAS' ? 'selected' : '' }}>UAS</option>
                    <option value="Quiz" {{ request('jenis') == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                    <option value="Remedial" {{ request('jenis') == 'Remedial' ? 'selected' : '' }}>Remedial</option>
                    <option value="Susulan" {{ request('jenis') == 'Susulan' ? 'selected' : '' }}>Susulan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="Terjadwal" {{ request('status') == 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditunda" {{ request('status') == 'Ditunda' ? 'selected' : '' }}>Ditunda</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('mahasiswa.jadwal-ujian') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Jadwal Ujian -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar-event me-2"></i>Jadwal Ujian {{ $tahunAkademik->nama }}
    </div>
    <div class="card-body p-0">
        @if($jadwalUjian->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Jenis</th>
                        <th width="12%">Tanggal</th>
                        <th width="10%">Waktu</th>
                        <th>Mata Kuliah</th>
                        <th width="10%">Ruangan</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalUjian as $index => $ujian)
                    <tr class="{{ $ujian->tanggal->isToday() ? 'table-warning' : ($ujian->tanggal->isPast() ? '' : '') }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="badge bg-{{ $ujian->jenis_badge }}">
                                {{ $ujian->jenis_ujian }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $ujian->tanggal->format('d M Y') }}</strong>
                            <br><small class="text-muted">{{ $ujian->tanggal->translatedFormat('l') }}</small>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($ujian->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($ujian->jam_selesai)->format('H:i') }}
                        </td>
                        <td>
                            <strong>{{ $ujian->mataKuliah->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $ujian->mataKuliah->kode ?? '' }} | {{ $ujian->dosen->nama ?? '-' }}</small>
                        </td>
                        <td>{{ $ujian->ruangan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $ujian->status_badge }}">
                                {{ $ujian->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">Belum ada jadwal ujian untuk semester ini</p>
        </div>
        @endif
    </div>
</div>

@endif
@endsection
