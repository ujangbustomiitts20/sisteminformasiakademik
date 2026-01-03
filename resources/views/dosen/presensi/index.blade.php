@extends('layouts.app')

@section('title', 'Presensi Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Presensi Dosen</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Presensi</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('dosen.presensi.riwayat') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-1"></i> Riwayat Presensi
        </a>
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
        <!-- Card Jam & Tanggal -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar3 text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="text-muted mb-1">{{ $today->locale('id')->isoFormat('dddd') }}</h5>
                    <h3 class="mb-2">{{ $today->locale('id')->isoFormat('D MMMM Y') }}</h3>
                    <div class="display-4 fw-bold text-primary" id="currentTime">
                        {{ now()->format('H:i:s') }}
                    </div>
                    
                    @if($jamKerja)
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted d-block">Jam Kerja</small>
                        <strong>{{ substr($jamKerja->jam_masuk, 0, 5) }} - {{ substr($jamKerja->jam_keluar, 0, 5) }}</strong>
                        <br>
                        <small class="text-muted">Toleransi keterlambatan: {{ $jamKerja->toleransi_terlambat }} menit</small>
                    </div>
                    @else
                    <div class="mt-3 p-3 bg-warning bg-opacity-25 rounded">
                        <small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i> Jam kerja belum diatur admin</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Presensi Hari Ini -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Presensi Hari Ini</h5>
                </div>
                <div class="card-body">
                    @if($presensiHariIni)
                        <div class="row text-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <i class="bi bi-box-arrow-in-right text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">Jam Masuk</h6>
                                    <h4 class="mb-0 text-success">{{ $presensiHariIni->jam_masuk ? substr($presensiHariIni->jam_masuk, 0, 5) : '-' }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <i class="bi bi-box-arrow-right text-danger" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">Jam Keluar</h6>
                                    <h4 class="mb-0 text-danger">{{ $presensiHariIni->jam_keluar ? substr($presensiHariIni->jam_keluar, 0, 5) : '-' }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                    <i class="bi bi-hourglass-split text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">Status</h6>
                                    <h4 class="mb-0">{!! $presensiHariIni->status_badge !!}</h4>
                                </div>
                            </div>
                        </div>

                        @if($presensiHariIni->jam_masuk && !$presensiHariIni->jam_keluar)
                        <hr>
                        <div class="text-center">
                            <p class="text-muted mb-3">Anda sudah absen masuk. Jangan lupa absen keluar!</p>
                            <button type="button" class="btn btn-danger btn-lg px-5" id="btnClockOut">
                                <i class="bi bi-box-arrow-right me-2"></i>Absen Keluar
                            </button>
                        </div>
                        @elseif($presensiHariIni->jam_masuk && $presensiHariIni->jam_keluar)
                        <hr>
                        <div class="text-center">
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                Presensi hari ini sudah lengkap. Durasi kerja: <strong>{{ $presensiHariIni->durasi_kerja }}</strong>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Anda belum absen hari ini</h5>
                            <p class="text-muted mb-4">Silakan lakukan absen masuk untuk memulai presensi</p>
                            <button type="button" class="btn btn-success btn-lg px-5" id="btnClockIn">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Absen Masuk
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Bulan Ini -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Hari Kerja</span>
                        <span class="badge bg-secondary">{{ $statistik['total_hari_kerja'] }} hari</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-check-circle text-success me-1"></i> Hadir</span>
                        <span class="badge bg-success">{{ $statistik['hadir'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-clock text-warning me-1"></i> Terlambat</span>
                        <span class="badge bg-warning text-dark">{{ $statistik['terlambat'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-thermometer-half text-info me-1"></i> Sakit</span>
                        <span class="badge bg-info">{{ $statistik['sakit'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-envelope text-primary me-1"></i> Izin</span>
                        <span class="badge bg-primary">{{ $statistik['izin'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-calendar-x text-secondary me-1"></i> Cuti</span>
                        <span class="badge bg-secondary">{{ $statistik['cuti'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-x-circle text-danger me-1"></i> Alpha</span>
                        <span class="badge bg-danger">{{ $statistik['alpha'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Presensi 7 Hari Terakhir -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Presensi 7 Hari Terakhir</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensiTerakhir as $p)
                                <tr>
                                    <td>{{ $p->tanggal->locale('id')->isoFormat('ddd, D MMM Y') }}</td>
                                    <td>{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '-' }}</td>
                                    <td>{{ $p->jam_keluar ? substr($p->jam_keluar, 0, 5) : '-' }}</td>
                                    <td>{!! $p->status_badge !!}</td>
                                    <td>{{ $p->durasi_kerja }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data presensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Absen Masuk -->
<div class="modal fade" id="modalClockIn" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="bi bi-box-arrow-in-right text-success me-2"></i>Absen Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formClockIn">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="display-5 fw-bold text-primary" id="clockInTime">{{ now()->format('H:i:s') }}</div>
                        <small class="text-muted">{{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }}</small>
                    </div>

                    <!-- Camera Preview -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-camera me-1"></i> Foto Selfie (Opsional)
                        </label>
                        <div class="text-center">
                            <video id="videoPreviewIn" width="100%" autoplay playsinline class="rounded d-none"></video>
                            <canvas id="canvasIn" class="d-none"></canvas>
                            <img id="capturedImageIn" class="img-fluid rounded d-none" style="max-height: 200px;">
                            <input type="hidden" name="foto" id="fotoIn">
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnStartCameraIn">
                                <i class="bi bi-camera"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success d-none" id="btnCaptureIn">
                                <i class="bi bi-camera-fill"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnRetakeIn">
                                <i class="bi bi-arrow-repeat"></i> Ulangi
                            </button>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-geo-alt me-1"></i> Lokasi (Opsional)
                        </label>
                        <input type="text" class="form-control" name="lokasi" id="lokasiIn" readonly placeholder="Mengambil lokasi...">
                        <input type="hidden" name="latitude" id="latitudeIn">
                        <input type="hidden" name="longitude" id="longitudeIn">
                        <small class="text-muted">Lokasi akan diambil otomatis jika diizinkan</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="submitClockIn">
                        <i class="bi bi-check-lg me-1"></i> Konfirmasi Absen Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Absen Keluar -->
<div class="modal fade" id="modalClockOut" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="bi bi-box-arrow-right text-danger me-2"></i>Absen Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formClockOut">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="display-5 fw-bold text-primary" id="clockOutTime">{{ now()->format('H:i:s') }}</div>
                        <small class="text-muted">{{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }}</small>
                    </div>

                    <!-- Camera Preview -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-camera me-1"></i> Foto Selfie (Opsional)
                        </label>
                        <div class="text-center">
                            <video id="videoPreviewOut" width="100%" autoplay playsinline class="rounded d-none"></video>
                            <canvas id="canvasOut" class="d-none"></canvas>
                            <img id="capturedImageOut" class="img-fluid rounded d-none" style="max-height: 200px;">
                            <input type="hidden" name="foto" id="fotoOut">
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnStartCameraOut">
                                <i class="bi bi-camera"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success d-none" id="btnCaptureOut">
                                <i class="bi bi-camera-fill"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnRetakeOut">
                                <i class="bi bi-arrow-repeat"></i> Ulangi
                            </button>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-geo-alt me-1"></i> Lokasi (Opsional)
                        </label>
                        <input type="text" class="form-control" name="lokasi" id="lokasiOut" readonly placeholder="Mengambil lokasi...">
                        <input type="hidden" name="latitude" id="latitudeOut">
                        <input type="hidden" name="longitude" id="longitudeOut">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="submitClockOut">
                        <i class="bi bi-check-lg me-1"></i> Konfirmasi Absen Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update jam realtime
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('currentTime').textContent = timeStr;
        if (document.getElementById('clockInTime')) {
            document.getElementById('clockInTime').textContent = timeStr;
        }
        if (document.getElementById('clockOutTime')) {
            document.getElementById('clockOutTime').textContent = timeStr;
        }
    }
    setInterval(updateClock, 1000);

    // Get Location
    function getLocation(latInput, lngInput, locInput) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById(latInput).value = position.coords.latitude;
                    document.getElementById(lngInput).value = position.coords.longitude;
                    
                    // Reverse geocoding (simple)
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById(locInput).value = data.display_name || 'Lokasi ditemukan';
                        })
                        .catch(() => {
                            document.getElementById(locInput).value = `${position.coords.latitude}, ${position.coords.longitude}`;
                        });
                },
                function(error) {
                    document.getElementById(locInput).value = 'Lokasi tidak tersedia';
                    document.getElementById(locInput).placeholder = 'Gagal mengambil lokasi';
                }
            );
        } else {
            document.getElementById(locInput).value = 'Browser tidak mendukung geolocation';
        }
    }

    // Camera handling
    let streamIn = null;
    let streamOut = null;

    function setupCamera(videoId, canvasId, imageId, fotoInputId, startBtn, captureBtn, retakeBtn, streamVar) {
        const video = document.getElementById(videoId);
        const canvas = document.getElementById(canvasId);
        const image = document.getElementById(imageId);
        const fotoInput = document.getElementById(fotoInputId);
        const btnStart = document.getElementById(startBtn);
        const btnCapture = document.getElementById(captureBtn);
        const btnRetake = document.getElementById(retakeBtn);

        btnStart.addEventListener('click', async function() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                });
                if (videoId === 'videoPreviewIn') {
                    streamIn = stream;
                } else {
                    streamOut = stream;
                }
                video.srcObject = stream;
                video.classList.remove('d-none');
                btnStart.classList.add('d-none');
                btnCapture.classList.remove('d-none');
            } catch (err) {
                alert('Tidak dapat mengakses kamera: ' + err.message);
            }
        });

        btnCapture.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);
            
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            image.src = dataUrl;
            fotoInput.value = dataUrl;
            
            video.classList.add('d-none');
            image.classList.remove('d-none');
            btnCapture.classList.add('d-none');
            btnRetake.classList.remove('d-none');
            
            // Stop camera
            const currentStream = videoId === 'videoPreviewIn' ? streamIn : streamOut;
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
        });

        btnRetake.addEventListener('click', async function() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                });
                if (videoId === 'videoPreviewIn') {
                    streamIn = stream;
                } else {
                    streamOut = stream;
                }
                video.srcObject = stream;
                video.classList.remove('d-none');
                image.classList.add('d-none');
                fotoInput.value = '';
                btnRetake.classList.add('d-none');
                btnCapture.classList.remove('d-none');
            } catch (err) {
                alert('Tidak dapat mengakses kamera: ' + err.message);
            }
        });
    }

    // Setup cameras
    setupCamera('videoPreviewIn', 'canvasIn', 'capturedImageIn', 'fotoIn', 'btnStartCameraIn', 'btnCaptureIn', 'btnRetakeIn');
    setupCamera('videoPreviewOut', 'canvasOut', 'capturedImageOut', 'fotoOut', 'btnStartCameraOut', 'btnCaptureOut', 'btnRetakeOut');

    // Button Clock In
    const btnClockIn = document.getElementById('btnClockIn');
    if (btnClockIn) {
        btnClockIn.addEventListener('click', function() {
            getLocation('latitudeIn', 'longitudeIn', 'lokasiIn');
            new bootstrap.Modal(document.getElementById('modalClockIn')).show();
        });
    }

    // Button Clock Out
    const btnClockOut = document.getElementById('btnClockOut');
    if (btnClockOut) {
        btnClockOut.addEventListener('click', function() {
            getLocation('latitudeOut', 'longitudeOut', 'lokasiOut');
            new bootstrap.Modal(document.getElementById('modalClockOut')).show();
        });
    }

    // Form Clock In
    document.getElementById('formClockIn').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitClockIn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

        const formData = new FormData(this);

        fetch('{{ route("dosen.presensi.masuk") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Konfirmasi Absen Masuk';
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Konfirmasi Absen Masuk';
        });
    });

    // Form Clock Out
    document.getElementById('formClockOut').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitClockOut');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

        const formData = new FormData(this);

        fetch('{{ route("dosen.presensi.keluar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Konfirmasi Absen Keluar';
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Konfirmasi Absen Keluar';
        });
    });

    // Stop camera when modal is closed
    document.getElementById('modalClockIn').addEventListener('hidden.bs.modal', function() {
        if (streamIn) {
            streamIn.getTracks().forEach(track => track.stop());
            streamIn = null;
        }
    });

    document.getElementById('modalClockOut').addEventListener('hidden.bs.modal', function() {
        if (streamOut) {
            streamOut.getTracks().forEach(track => track.stop());
            streamOut = null;
        }
    });
});
</script>
@endpush
