@extends('layouts.app')

@section('title', 'Absensi Mandiri')

@section('content')
<div class="page-title">
    <h4>Absensi Mandiri</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Absensi Mandiri</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Mahasiswa</h6>
                        <h4 class="mb-0">{{ $mahasiswa->nama }}</h4>
                        <small>{{ $mahasiswa->nim }}</small>
                    </div>
                    <i class="bi bi-person-circle display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Hari Ini</h6>
                        <h4 class="mb-0">{{ $hariIni }}</h4>
                        <small>{{ $today->format('d F Y') }}</small>
                    </div>
                    <i class="bi bi-calendar-date display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Jadwal Hari Ini</h6>
                        <h4 class="mb-0">{{ $jadwalHariIni->count() }}</h4>
                        <small>Mata Kuliah</small>
                    </div>
                    <i class="bi bi-journal-bookmark display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar-check me-2"></i>Jadwal Kuliah Hari Ini ({{ $hariIni }})
    </div>
    <div class="card-body">
        @if($jadwalHariIni->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-x display-1 text-muted"></i>
            <h5 class="text-muted mt-3">Tidak Ada Jadwal Hari Ini</h5>
            <p class="text-muted">Anda tidak memiliki jadwal kuliah pada hari {{ $hariIni }}.</p>
        </div>
        @else
        <div class="row">
            @foreach($jadwalHariIni as $krs)
            @php
                $jadwal = $krs->jadwalKuliah;
                $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai);
                $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                $currentTime = \Carbon\Carbon::now()->format('H:i:s');
                $batasAwal = $jamMulai->copy()->subMinutes(30)->format('H:i:s');
                $batasAkhir = $jamSelesai->format('H:i:s');
                $dalamWaktuAbsen = ($currentTime >= $batasAwal && $currentTime <= $batasAkhir);
                
                // Check if already attended today
                $lastPertemuan = $krs->absensi->max('pertemuan') ?? 0;
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border {{ $dalamWaktuAbsen ? 'border-success' : 'border-secondary' }}">
                    <div class="card-header {{ $dalamWaktuAbsen ? 'bg-success text-white' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>{{ $jadwal->mataKuliah->nama }}</strong>
                            @if($dalamWaktuAbsen)
                            <span class="badge bg-white text-success">
                                <i class="bi bi-check-circle me-1"></i>Aktif
                            </span>
                            @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-clock me-1"></i>Belum Aktif
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="bi bi-tag me-2 text-muted"></i>{{ $jadwal->mataKuliah->kode }}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-person me-2 text-muted"></i>{{ $jadwal->dosen->nama }}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-clock me-2 text-muted"></i>{{ $jamMulai->format('H:i') }} - {{ $jamSelesai->format('H:i') }}
                        </p>
                        <p class="mb-3">
                            <i class="bi bi-door-open me-2 text-muted"></i>{{ $jadwal->ruangan->nama }}
                        </p>
                        
                        <p class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Waktu absen: {{ $jamMulai->copy()->subMinutes(30)->format('H:i') }} - {{ $jamSelesai->format('H:i') }}
                            </small>
                        </p>
                        <p class="mb-0">
                            <small class="text-muted">
                                <i class="bi bi-clipboard-check me-1"></i>
                                Pertemuan terakhir: {{ $lastPertemuan > 0 ? 'Ke-' . $lastPertemuan : 'Belum ada' }}
                            </small>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        @if($dalamWaktuAbsen)
                        <button type="button" 
                                class="btn btn-success w-100 btn-absen" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalAbsen"
                                data-krs-id="{{ $krs->id }}"
                                data-matkul="{{ $jadwal->mataKuliah->nama }}"
                                data-dosen="{{ $jadwal->dosen->nama }}">
                            <i class="bi bi-qr-code-scan me-2"></i>Absen Sekarang
                        </button>
                        @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-clock me-2"></i>
                            @if($currentTime < $batasAwal)
                                Buka pukul {{ $jamMulai->copy()->subMinutes(30)->format('H:i') }}
                            @else
                                Waktu absen sudah lewat
                            @endif
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-info-circle me-2"></i>Informasi Absensi Mandiri
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <h6><i class="bi bi-lightbulb me-2"></i>Cara Melakukan Absensi Mandiri:</h6>
            <ol class="mb-0 ps-3">
                <li>Absensi hanya dapat dilakukan <strong>30 menit sebelum</strong> hingga <strong>akhir jam kuliah</strong>.</li>
                <li>Minta <strong>kode absensi</strong> dari dosen pengampu.</li>
                <li>Klik tombol <strong>"Absen Sekarang"</strong> pada jadwal yang tersedia.</li>
                <li>Masukkan kode absensi yang diberikan dosen.</li>
                <li>Jika berhasil, kehadiran Anda akan tercatat otomatis.</li>
            </ol>
        </div>
    </div>
</div>

<!-- Modal Absen -->
<div class="modal fade" id="modalAbsen" tabindex="-1" aria-labelledby="modalAbsenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('absensi.mandiri.proses') }}" method="POST">
                @csrf
                <input type="hidden" name="krs_id" id="inputKrsId">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAbsenLabel">
                        <i class="bi bi-qr-code-scan me-2"></i>Masukkan Kode Absensi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h5 id="modalMatkul" class="mb-1"></h5>
                        <p id="modalDosen" class="text-muted mb-0"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kodeAbsensi" class="form-label">Kode Absensi</label>
                        <input type="text" 
                               class="form-control form-control-lg text-center" 
                               id="kodeAbsensi" 
                               name="kode_absensi" 
                               placeholder="Masukkan kode 6 digit"
                               maxlength="6"
                               style="letter-spacing: 5px; font-size: 1.5rem; text-transform: uppercase;"
                               autocomplete="off"
                               required>
                        <div class="form-text">Minta kode absensi dari dosen Anda</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Konfirmasi Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle click on absen button
    document.querySelectorAll('.btn-absen').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const krsId = this.getAttribute('data-krs-id');
            const matkul = this.getAttribute('data-matkul');
            const dosen = this.getAttribute('data-dosen');
            
            document.getElementById('inputKrsId').value = krsId;
            document.getElementById('modalMatkul').textContent = matkul;
            document.getElementById('modalDosen').textContent = dosen;
            document.getElementById('kodeAbsensi').value = '';
        });
    });

    // Auto uppercase kode absensi
    document.getElementById('kodeAbsensi').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection
