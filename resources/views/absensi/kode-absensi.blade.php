@extends('layouts.app')

@section('title', 'Kode Absensi')

@section('content')
<div class="page-title">
    <h4>Buka Sesi Absensi Mandiri</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.show', $jadwalKuliah) }}">{{ $jadwalKuliah->mataKuliah->nama }}</a></li>
            <li class="breadcrumb-item active">Kode Absensi</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Mata Kuliah</h6>
                        <h5 class="mb-0">{{ $jadwalKuliah->mataKuliah->nama }}</h5>
                        <small>{{ $jadwalKuliah->mataKuliah->kode }} | Kelas {{ $jadwalKuliah->kelas }}</small>
                    </div>
                    <i class="bi bi-book display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Jadwal</h6>
                        <h5 class="mb-0">{{ $jadwalKuliah->hari }}</h5>
                        <small>{{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}</small>
                    </div>
                    <i class="bi bi-calendar3 display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Mahasiswa</h6>
                        <h5 class="mb-0">{{ $totalMahasiswa }} Orang</h5>
                        <small>Mengambil mata kuliah ini</small>
                    </div>
                    <i class="bi bi-people display-4 opacity-50"></i>
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

<div class="row">
    <!-- Form Generate Kode -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Pengaturan Sesi Absensi
            </div>
            <div class="card-body">
                <form id="formGenerateKode">
                    <div class="mb-3">
                        <label for="pertemuan" class="form-label">Pertemuan Ke-</label>
                        <input type="number" 
                               class="form-control" 
                               id="pertemuan" 
                               name="pertemuan" 
                               value="{{ $pertemuan }}"
                               min="1" 
                               max="16" 
                               required>
                        <div class="form-text">Berdasarkan data: sudah ada {{ $pertemuan - 1 }} pertemuan</div>
                    </div>

                    <div class="mb-3">
                        <label for="materi" class="form-label">Materi Perkuliahan</label>
                        <input type="text" 
                               class="form-control" 
                               id="materi" 
                               name="materi" 
                               placeholder="Contoh: Pengenalan Algoritma">
                    </div>

                    <div class="mb-4">
                        <label for="durasi" class="form-label">Durasi Sesi (menit)</label>
                        <select class="form-select" id="durasi" name="durasi" required>
                            <option value="15">15 menit</option>
                            <option value="30" selected>30 menit</option>
                            <option value="45">45 menit</option>
                            <option value="60">60 menit</option>
                            <option value="90">90 menit</option>
                            <option value="120">120 menit</option>
                        </select>
                        <div class="form-text">Mahasiswa dapat absen dalam durasi ini</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnGenerate">
                        <i class="bi bi-qr-code me-2"></i>Buka Sesi Absensi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Display Kode -->
    <div class="col-lg-7">
        <div class="card" id="cardKodeAbsensi">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-broadcast me-2"></i>Kode Absensi Aktif</span>
                    <span id="statusBadge" class="badge bg-light text-success">
                        @if($kodeAktif)
                            <i class="bi bi-circle-fill me-1 blink"></i>Aktif
                        @else
                            <i class="bi bi-circle me-1"></i>Tidak Aktif
                        @endif
                    </span>
                </div>
            </div>
            <div class="card-body text-center py-5">
                @if($kodeAktif)
                <div id="kodeDisplay">
                    <div class="mb-3">
                        <span class="badge bg-info fs-6">Pertemuan ke-{{ $pertemuanAktif }}</span>
                    </div>
                    <div class="display-1 fw-bold text-success mb-3" style="letter-spacing: 15px;">
                        {{ $kodeAktif }}
                    </div>
                    <p class="text-muted mb-2">Berikan kode ini kepada mahasiswa</p>
                    <p class="mb-4">
                        <i class="bi bi-clock me-1"></i>
                        Berlaku sampai: <strong id="expiry">{{ $expiry->format('H:i:s') }}</strong>
                    </p>
                    <div class="row justify-content-center mb-4">
                        <div class="col-auto">
                            <div class="card bg-light">
                                <div class="card-body py-3 px-4">
                                    <h4 class="mb-0">
                                        <span id="sudahAbsen">{{ $sudahAbsen }}</span> / {{ $totalMahasiswa }}
                                    </h4>
                                    <small class="text-muted">Mahasiswa sudah absen</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('absensi.tutup', $jadwalKuliah) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Tutup sesi absensi? Mahasiswa yang belum absen akan ditandai Alpha.')">
                            <i class="bi bi-x-circle me-2"></i>Tutup Sesi & Rekap Absensi
                        </button>
                    </form>
                </div>
                @else
                <div id="kodeEmpty">
                    <i class="bi bi-qr-code display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Belum Ada Sesi Aktif</h5>
                    <p class="text-muted">Klik "Buka Sesi Absensi" untuk memulai</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <h6><i class="bi bi-lightbulb me-2"></i>Cara Kerja Absensi Mandiri:</h6>
                    <ol class="mb-0 ps-3">
                        <li>Buka sesi absensi dengan mengisi form di samping.</li>
                        <li>Bagikan <strong>kode 6 digit</strong> kepada mahasiswa.</li>
                        <li>Mahasiswa memasukkan kode di menu "Absensi Mandiri".</li>
                        <li>Pantau jumlah mahasiswa yang sudah absen secara real-time.</li>
                        <li>Setelah selesai, klik <strong>"Tutup Sesi"</strong> untuk merekap.</li>
                        <li>Mahasiswa yang tidak hadir akan otomatis ditandai <strong>Alpha</strong>.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('absensi.show', $jadwalKuliah) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Rekap Absensi
    </a>
    <a href="{{ route('absensi.create', $jadwalKuliah) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil me-2"></i>Input Absensi Manual
    </a>
</div>

<style>
.blink {
    animation: blink-animation 1s infinite;
}
@keyframes blink-animation {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateKodeUrl = "{{ route('absensi.generate-kode', $jadwalKuliah) }}";
    const statusUrl = "{{ route('absensi.status', $jadwalKuliah) }}";
    const totalMahasiswa = {{ $totalMahasiswa }};
    const csrfToken = "{{ csrf_token() }}";
    
    // Form generate kode
    const form = document.getElementById('formGenerateKode');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btn = document.getElementById('btnGenerate');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
            
            const payload = {
                pertemuan: parseInt(formData.get('pertemuan')),
                materi: formData.get('materi') || '',
                durasi: parseInt(formData.get('durasi'))
            };
            
            fetch(generateKodeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error('Server error: ' + response.status + ' - ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success notification
                    showNotification('success', 'Sesi absensi berhasil dibuka!');
                    // Update tampilan kode
                    updateKodeDisplay(data.kode, data.pertemuan, data.expiry);
                } else {
                    showNotification('danger', 'Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showNotification('danger', 'Terjadi kesalahan: ' + error.message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }

    function showNotification(type, message) {
        // Remove existing notifications
        document.querySelectorAll('.alert-auto').forEach(el => el.remove());
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-auto`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after page-title
        const pageTitle = document.querySelector('.page-title');
        if (pageTitle) {
            pageTitle.insertAdjacentElement('afterend', alertDiv);
        } else {
            document.querySelector('.row.mb-4').insertAdjacentElement('beforebegin', alertDiv);
        }
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }

    function updateKodeDisplay(kode, pertemuan, expiry) {
        const cardBody = document.querySelector('#cardKodeAbsensi .card-body');
        const tutupUrl = "{{ route('absensi.tutup', $jadwalKuliah) }}";
        
        cardBody.innerHTML = `
            <div id="kodeDisplay">
                <div class="mb-3">
                    <span class="badge bg-info fs-6">Pertemuan ke-${pertemuan}</span>
                </div>
                <div class="display-1 fw-bold text-success mb-3" style="letter-spacing: 15px;">
                    ${kode}
                </div>
                <p class="text-muted mb-2">Berikan kode ini kepada mahasiswa</p>
                <p class="mb-4">
                    <i class="bi bi-clock me-1"></i>
                    Berlaku sampai: <strong id="expiry">${expiry}</strong>
                </p>
                <div class="row justify-content-center mb-4">
                    <div class="col-auto">
                        <div class="card bg-light">
                            <div class="card-body py-3 px-4">
                                <h4 class="mb-0">
                                    <span id="sudahAbsen">0</span> / ${totalMahasiswa}
                                </h4>
                                <small class="text-muted">Mahasiswa sudah absen</small>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="${tutupUrl}" method="POST" class="d-inline">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Tutup sesi absensi? Mahasiswa yang belum absen akan ditandai Alpha.')">
                        <i class="bi bi-x-circle me-2"></i>Tutup Sesi & Rekap Absensi
                    </button>
                </form>
            </div>
        `;
        
        // Update status badge
        const statusBadge = document.getElementById('statusBadge');
        if (statusBadge) {
            statusBadge.innerHTML = '<i class="bi bi-circle-fill me-1 blink"></i>Aktif';
            statusBadge.classList.remove('text-secondary');
            statusBadge.classList.add('text-success');
        }
        
        // Start auto refresh
        startAutoRefresh();
    }
    
    function startAutoRefresh() {
        setInterval(function() {
            fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.active) {
                    const sudahAbsenEl = document.getElementById('sudahAbsen');
                    if (sudahAbsenEl) {
                        sudahAbsenEl.textContent = data.sudah_absen;
                    }
                }
            })
            .catch(error => console.error('Status check error:', error));
        }, 5000);
    }
    
    // Auto refresh status setiap 5 detik jika ada kode aktif
    @if($kodeAktif)
    startAutoRefresh();
    @endif
});
</script>
@endpush
