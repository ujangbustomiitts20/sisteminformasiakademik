@extends('layouts.app')

@section('title', 'Detail Jadwal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Jadwal Ujian</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mahasiswa.jadwal-ujian') }}">Jadwal Ujian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.jadwal-ujian') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Ujian
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted">Jenis Ujian</label>
                        <div>
                            <span class="badge bg-{{ $jadwalUjian->jenis_badge }} fs-6">
                                {{ $jadwalUjian->jenis_ujian }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Status</label>
                        <div>
                            <span class="badge bg-{{ $jadwalUjian->status_badge }} fs-6">
                                {{ $jadwalUjian->status }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted">Durasi</label>
                        <div><strong>{{ $jadwalUjian->durasi_menit }} menit</strong></div>
                    </div>
                </div>
                
                <hr>

                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="30%">Mata Kuliah</td>
                        <td>
                            <strong>{{ $jadwalUjian->mataKuliah->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $jadwalUjian->mataKuliah->kode ?? '' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen Pengampu</td>
                        <td>{{ $jadwalUjian->dosen->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>
                            <strong>{{ $jadwalUjian->tanggal->format('d F Y') }}</strong>
                            <br><small class="text-muted">{{ $jadwalUjian->tanggal->translatedFormat('l') }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu</td>
                        <td>
                            {{ \Carbon\Carbon::parse($jadwalUjian->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($jadwalUjian->jam_selesai)->format('H:i') }} WIB
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ruangan</td>
                        <td><strong>{{ $jadwalUjian->ruangan ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $jadwalUjian->tahunAkademik->nama ?? '-' }}</td>
                    </tr>
                    @if($jadwalUjian->keterangan)
                    <tr>
                        <td class="text-muted">Keterangan</td>
                        <td>{{ $jadwalUjian->keterangan }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Countdown / Status -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-clock me-2"></i>Status Ujian
            </div>
            <div class="card-body text-center">
                @php
                    $now = now();
                    $ujianDate = $jadwalUjian->tanggal->setTimeFromTimeString($jadwalUjian->jam_mulai);
                @endphp
                
                @if($jadwalUjian->status == 'Selesai')
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-success">Ujian Selesai</h5>
                @elseif($jadwalUjian->status == 'Dibatalkan')
                    <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-danger">Ujian Dibatalkan</h5>
                @elseif($jadwalUjian->status == 'Ditunda')
                    <i class="bi bi-pause-circle text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-warning">Ujian Ditunda</h5>
                @elseif($ujianDate->isPast())
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-2">Ujian Telah Berlangsung</h5>
                @elseif($ujianDate->isToday())
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-warning">Hari Ini!</h5>
                    <p class="mb-0">Pukul {{ \Carbon\Carbon::parse($jadwalUjian->jam_mulai)->format('H:i') }} WIB</p>
                @else
                    <i class="bi bi-calendar-event text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-2">{{ $ujianDate->diffForHumans() }}</h5>
                    <p class="mb-0 text-muted">Sampai waktu ujian</p>
                @endif
            </div>
        </div>

        <!-- Reminder -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightbulb me-2"></i>Pengingat
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Bawa Kartu Tanda Mahasiswa
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Datang 15 menit sebelum ujian
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Bawa alat tulis sendiri
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Periksa ruangan ujian
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
