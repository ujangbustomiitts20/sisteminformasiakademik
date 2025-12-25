@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="page-title">
    <h4>Jadwal Mengajar Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Mengajar</li>
        </ol>
    </nav>
</div>

<!-- Info Semester -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ $tahunAkademik->nama_lengkap ?? '-' }}</h5>
                <p class="text-muted mb-0">Tahun Akademik Aktif</p>
            </div>
            <div class="col-md-4 text-md-end">
                <h5 class="mb-1">{{ $jadwal->count() }} Mata Kuliah</h5>
                <p class="text-muted mb-0">Total Diampu</p>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal dalam Bentuk Tabel Mingguan -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-week me-2"></i>Jadwal Mingguan</span>
        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
    </div>
    <div class="card-body">
        @if($jadwal->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="80">Jam</th>
                        <th>Senin</th>
                        <th>Selasa</th>
                        <th>Rabu</th>
                        <th>Kamis</th>
                        <th>Jumat</th>
                        <th>Sabtu</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $jamSlots = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    @endphp
                    @foreach($jamSlots as $jam)
                    <tr>
                        <td class="text-center align-middle bg-light fw-medium">{{ $jam }}</td>
                        @foreach($hari as $h)
                        @php
                            $mk = $jadwal->first(function($j) use ($h, $jam) {
                                $mulai = \Carbon\Carbon::parse($j->jam_mulai)->format('H:i');
                                $selesai = \Carbon\Carbon::parse($j->jam_selesai)->format('H:i');
                                return $j->hari == $h && $mulai <= $jam && $selesai > $jam;
                            });
                            $isStart = $mk && \Carbon\Carbon::parse($mk->jam_mulai)->format('H:i') == $jam;
                        @endphp
                        <td class="{{ $mk ? 'bg-success bg-opacity-10' : '' }}">
                            @if($isStart)
                            <div class="small">
                                <strong class="text-success">{{ $mk->mataKuliah->nama }}</strong><br>
                                <span class="text-muted">Kelas {{ $mk->kelas }}</span><br>
                                <span class="text-muted">{{ $mk->ruangan->nama ?? '-' }}</span><br>
                                <small>{{ \Carbon\Carbon::parse($mk->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($mk->jam_selesai)->format('H:i') }}</small>
                            </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">Belum ada jadwal mengajar</p>
        </div>
        @endif
    </div>
</div>

<!-- Daftar Detail Jadwal -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Detail Jadwal Mengajar
    </div>
    <div class="card-body">
        @if($jadwal->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>SKS</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Mahasiswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $index => $jdwl)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $jdwl->mataKuliah->kode }}</code></td>
                        <td><strong>{{ $jdwl->mataKuliah->nama }}</strong></td>
                        <td><span class="badge bg-secondary">{{ $jdwl->kelas }}</span></td>
                        <td>{{ $jdwl->mataKuliah->sks }}</td>
                        <td>{{ $jdwl->hari }}</td>
                        <td>{{ \Carbon\Carbon::parse($jdwl->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jdwl->jam_selesai)->format('H:i') }}</td>
                        <td>{{ $jdwl->ruangan->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $jdwl->krs->where('status', 'Disetujui')->count() }} / {{ $jdwl->kuota }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('nilai.input', $jdwl) }}" class="btn btn-outline-primary" title="Input Nilai">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="{{ route('absensi.show', $jdwl) }}" class="btn btn-outline-success" title="Absensi">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="4" class="text-end">Total SKS:</th>
                        <th>{{ $jadwal->sum(fn($j) => $j->mataKuliah->sks) }}</th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <p class="text-muted text-center">Belum ada jadwal mengajar</p>
        @endif
    </div>
</div>
@endsection
