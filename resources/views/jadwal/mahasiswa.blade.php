@extends('layouts.app')

@section('title', 'Jadwal Kuliah Saya')

@section('content')
<div class="page-title">
    <h4>Jadwal Kuliah Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Kuliah</li>
        </ol>
    </nav>
</div>

<!-- Filter Tahun Akademik -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label">Pilih Semester</label>
                <select class="form-select" id="tahunAkademikSelect" onchange="window.location.href='{{ route('jadwal.mahasiswa') }}?tahun_akademik_id='+this.value">
                    @foreach($tahunAkademikList as $ta)
                        <option value="{{ $ta->id }}" {{ $tahunAkademik?->id == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <h5 class="mb-1">{{ $tahunAkademik->nama_lengkap ?? '-' }}</h5>
                <p class="text-muted mb-0">Semester Terpilih</p>
            </div>
            <div class="col-md-4 text-md-end">
                <h5 class="mb-1">{{ $jadwal->count() }} Mata Kuliah</h5>
                <p class="text-muted mb-0">Total Diambil</p>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal dalam Bentuk Tabel -->
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
                        <th width="100">Jam</th>
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
                                $mulai = \Carbon\Carbon::parse($j->jadwalKuliah->jam_mulai)->format('H:i');
                                $selesai = \Carbon\Carbon::parse($j->jadwalKuliah->jam_selesai)->format('H:i');
                                return $j->jadwalKuliah->hari == $h && $mulai <= $jam && $selesai > $jam;
                            });
                            $isStart = $mk && \Carbon\Carbon::parse($mk->jadwalKuliah->jam_mulai)->format('H:i') == $jam;
                        @endphp
                        <td class="{{ $mk ? 'bg-primary bg-opacity-10' : '' }}">
                            @if($isStart)
                            <div class="small">
                                <strong class="text-primary">{{ $mk->jadwalKuliah->mataKuliah->nama }}</strong><br>
                                <span class="text-muted">{{ $mk->jadwalKuliah->ruangan->nama ?? '-' }}</span><br>
                                <small>{{ \Carbon\Carbon::parse($mk->jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($mk->jadwalKuliah->jam_selesai)->format('H:i') }}</small>
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
            <p class="text-muted mt-2">Belum ada jadwal kuliah</p>
        </div>
        @endif
    </div>
</div>

<!-- Daftar Detail Jadwal -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Detail Jadwal Kuliah
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
                        <th>SKS</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Dosen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $index => $krs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $krs->jadwalKuliah->mataKuliah->kode }}</code></td>
                        <td>
                            <strong>{{ $krs->jadwalKuliah->mataKuliah->nama }}</strong>
                            <br><small class="text-muted">Kelas {{ $krs->jadwalKuliah->kelas }}</small>
                        </td>
                        <td>{{ $krs->jadwalKuliah->mataKuliah->sks }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $krs->jadwalKuliah->hari }}</span>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_selesai)->format('H:i') }}
                        </td>
                        <td>{{ $krs->jadwalKuliah->ruangan->nama ?? '-' }}</td>
                        <td>{{ $krs->jadwalKuliah->dosen->nama ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="3" class="text-end">Total SKS:</th>
                        <th>{{ $jadwal->sum(fn($k) => $k->jadwalKuliah->mataKuliah->sks) }}</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <p class="text-muted text-center">Belum ada jadwal kuliah</p>
        @endif
    </div>
</div>
@endsection
