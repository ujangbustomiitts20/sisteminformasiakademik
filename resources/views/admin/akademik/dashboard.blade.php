@extends('layouts.app')

@section('title', 'Dashboard Akademik')

@push('styles')
<style>
    .stat-card {
        padding: 1.25rem;
        border-radius: 12px;
        color: white;
        transition: all 0.3s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .stat-card .stat-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2.5rem;
        opacity: 0.3;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
    }
    .kpi-card {
        border-left: 4px solid;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        background: white;
        height: 100%;
    }
    .kpi-card.primary { border-color: #0d6efd; }
    .kpi-card.success { border-color: #198754; }
    .kpi-card.warning { border-color: #ffc107; }
    .kpi-card.danger { border-color: #dc3545; }
    .kpi-card.info { border-color: #0dcaf0; }
    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .kpi-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .chart-container {
        position: relative;
        height: 250px;
    }
    .alert-item {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border-left: 4px solid;
    }
    .alert-item.danger { background: #fff5f5; border-color: #dc3545; }
    .alert-item.warning { background: #fffbeb; border-color: #ffc107; }
    .alert-item.info { background: #f0f9ff; border-color: #0dcaf0; }
    .quick-btn {
        padding: 0.6rem;
        border-radius: 8px;
        transition: all 0.2s;
        text-align: center;
        font-size: 0.85rem;
    }
    .quick-btn:hover {
        transform: scale(1.02);
    }
    .section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    /* Clickable items */
    .kpi-card.clickable {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .kpi-card.clickable:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .clickable-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable-item:hover {
        transform: translateX(5px);
        box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
    }
    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="page-title d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Dashboard Akademik</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Akademik</li>
            </ol>
        </nav>
    </div>
    @if($tahunAkademikAktif)
    <span class="badge bg-primary fs-6 px-3 py-2">{{ $tahunAkademikAktif->nama }}</span>
    @endif
</div>

<!-- KPI STRATEGIS -->
<div class="section-title">
    <i class="bi bi-speedometer2 me-2"></i>KPI Strategis
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2">
        <a href="{{ route('mahasiswa.index', ['status' => 'Aktif']) }}" class="text-decoration-none">
            <div class="kpi-card primary clickable">
                <div class="kpi-value text-primary">{{ number_format($mahasiswaStats['aktif']) }}</div>
                <div class="kpi-label">Mahasiswa Aktif</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-2">
        <a href="{{ route('dosen.index', ['status' => 'Aktif']) }}" class="text-decoration-none">
            <div class="kpi-card info clickable">
                <div class="kpi-value text-info">{{ number_format($dosenStats['aktif']) }}</div>
                <div class="kpi-label">Dosen Aktif</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-2">
        <div class="kpi-card {{ $rasioDosenMahasiswa <= 30 ? 'success' : 'warning' }}">
            <div class="kpi-value {{ $rasioDosenMahasiswa <= 30 ? 'text-success' : 'text-warning' }}">1 : {{ $rasioDosenMahasiswa }}</div>
            <div class="kpi-label">Rasio Dosen:Mhs</div>
            <small class="text-muted">Ideal: 1:25-30</small>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <a href="{{ route('nilai.index') }}" class="text-decoration-none">
            <div class="kpi-card clickable {{ $ipkRataRata >= 3.0 ? 'success' : ($ipkRataRata >= 2.5 ? 'warning' : 'danger') }}">
                <div class="kpi-value {{ $ipkRataRata >= 3.0 ? 'text-success' : ($ipkRataRata >= 2.5 ? 'text-warning' : 'text-danger') }}">{{ number_format($ipkRataRata, 2) }}</div>
                <div class="kpi-label">IPK Rata-rata</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-2">
        <a href="{{ route('mahasiswa.index', ['status' => 'Lulus']) }}" class="text-decoration-none">
            <div class="kpi-card success clickable">
                <div class="kpi-value text-success">{{ number_format($mahasiswaStats['lulus']) }}</div>
                <div class="kpi-label">Total Alumni</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-2">
        <a href="{{ route('mahasiswa.index', ['status' => 'Lulus']) }}" class="text-decoration-none">
            <div class="kpi-card info clickable">
                <div class="kpi-value text-info">{{ number_format($lulusTahunIni) }}</div>
                <div class="kpi-label">Lulus {{ now()->year }}</div>
            </div>
        </a>
    </div>
</div>

<!-- EARLY WARNING & PROGRESS -->
<div class="row g-3 mb-4">
    <!-- Early Warning -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-2 bg-danger text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>Early Warning
            </div>
            <div class="card-body">
                <a href="{{ route('mahasiswa.index', ['ipk_filter' => 'rendah']) }}" class="alert-item danger d-flex justify-content-between align-items-center text-decoration-none clickable-item">
                    <div>
                        <i class="bi bi-graph-down text-danger me-2"></i>
                        <span class="text-dark">Mahasiswa IPK &lt; 2.0</span>
                    </div>
                    <span class="badge bg-danger fs-6">{{ number_format($mahasiswaIpkRendah) }}</span>
                </a>
                <a href="{{ route('absensi.index') }}" class="alert-item warning d-flex justify-content-between align-items-center text-decoration-none clickable-item">
                    <div>
                        <i class="bi bi-clock-history text-warning me-2"></i>
                        <span class="text-dark">Kehadiran &lt; 75%</span>
                    </div>
                    <span class="badge bg-warning text-dark fs-6">{{ number_format($mahasiswaKehadiranRendah) }}</span>
                </a>
                <a href="{{ route('mahasiswa.index', ['semester_min' => 8]) }}" class="alert-item warning d-flex justify-content-between align-items-center text-decoration-none clickable-item">
                    <div>
                        <i class="bi bi-hourglass-split text-warning me-2"></i>
                        <span class="text-dark">Semester ≥ 8</span>
                    </div>
                    <span class="badge bg-warning text-dark fs-6">{{ number_format($mahasiswaSemesterAkhir) }}</span>
                </a>
                <a href="{{ route('krs.index', ['belum_krs' => 1]) }}" class="alert-item info d-flex justify-content-between align-items-center text-decoration-none clickable-item">
                    <div>
                        <i class="bi bi-person-x text-info me-2"></i>
                        <span class="text-dark">Belum KRS</span>
                    </div>
                    <span class="badge bg-info fs-6">{{ number_format($mahasiswaBelumKrs) }}</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Progress Tugas Akhir -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="bi bi-mortarboard me-2"></i>Progress Tugas Akhir
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.tugas-akhir.index', ['status' => 'pengajuan']) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span><i class="bi bi-file-earmark-text text-secondary me-2"></i>Pengajuan</span>
                        <span class="badge bg-secondary">{{ $taStats['pengajuan'] }}</span>
                    </a>
                    <a href="{{ route('admin.tugas-akhir.index', ['status' => 'bimbingan']) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span><i class="bi bi-chat-dots text-primary me-2"></i>Bimbingan</span>
                        <span class="badge bg-primary">{{ $taStats['bimbingan'] }}</span>
                    </a>
                    <a href="{{ route('admin.tugas-akhir.index', ['status' => 'revisi']) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span><i class="bi bi-pencil-square text-warning me-2"></i>Revisi</span>
                        <span class="badge bg-warning text-dark">{{ $taStats['revisi'] }}</span>
                    </a>
                    <a href="{{ route('admin.tugas-akhir.index', ['status' => 'sidang']) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span><i class="bi bi-calendar-event text-info me-2"></i>Sidang</span>
                        <span class="badge bg-info">{{ $taStats['sidang'] }}</span>
                    </a>
                    <a href="{{ route('admin.tugas-akhir.index', ['status' => 'selesai']) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                        <span><i class="bi bi-check-circle text-success me-2"></i>Selesai</span>
                        <span class="badge bg-success">{{ $taStats['selesai'] }}</span>
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('wisuda.index') }}" class="d-flex justify-content-between text-decoration-none">
                    <span class="text-dark"><strong>Calon Wisudawan:</strong></span>
                    <span class="badge bg-success fs-6">{{ $calonWisudawan }}</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Status KRS Semester Ini -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="bi bi-journal-check me-2"></i>KRS Semester Ini
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 180px;">
                    <canvas id="chartKrsStatus"></canvas>
                </div>
                <div class="row text-center mt-3">
                    <div class="col-4">
                        <a href="{{ route('krs.index', ['status' => 'disetujui']) }}" class="text-decoration-none">
                            <div class="fw-bold text-success">{{ number_format($krsStats['approved']) }}</div>
                            <small class="text-muted">Disetujui</small>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('krs.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                            <div class="fw-bold text-warning">{{ number_format($krsStats['pending']) }}</div>
                            <small class="text-muted">Pending</small>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('krs.index', ['status' => 'ditolak']) }}" class="text-decoration-none">
                            <div class="fw-bold text-danger">{{ number_format($krsStats['rejected']) }}</div>
                            <small class="text-muted">Ditolak</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS -->
<div class="row g-3 mb-4">
    <!-- Mahasiswa per Prodi -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="bi bi-pie-chart me-2"></i>Mahasiswa per Program Studi
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartMahasiswaProdi"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Distribusi Nilai -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="bi bi-bar-chart me-2"></i>Distribusi Nilai Semester Ini
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartDistribusiNilai"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLES -->
<div class="row g-3 mb-4">
    <!-- Mahasiswa Bermasalah -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center bg-danger text-white">
                <span><i class="bi bi-exclamation-octagon me-2"></i>Mahasiswa IPK Rendah (Top 10)</span>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-sm btn-light">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($listMahasiswaBermasalah->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th class="text-center">IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listMahasiswaBermasalah as $mhs)
                            <tr>
                                <td><small>{{ $mhs->nim }}</small></td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $mhs->nama }}</td>
                                <td><small>{{ $mhs->programStudi->singkatan ?? '-' }}</small></td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ number_format($mhs->ipk, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-success py-4">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Tidak ada mahasiswa dengan IPK < 2.0</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- IPK per Prodi -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="bi bi-trophy me-2"></i>IPK Rata-rata per Program Studi
            </div>
            <div class="card-body p-0">
                @if($ipkPerProdi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Program Studi</th>
                                <th class="text-center">IPK Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ipkPerProdi as $index => $prodi)
                            <tr>
                                <td>
                                    @if($index == 0)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-trophy"></i></span>
                                    @else
                                    {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>{{ $prodi->nama }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $prodi->ipk_rata >= 3.0 ? 'bg-success' : ($prodi->ipk_rata >= 2.5 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ number_format($prodi->ipk_rata, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Belum ada data IPK</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS & INFO -->
<div class="row g-3">
    <!-- KRS Pending -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>KRS Menunggu Persetujuan</span>
                <a href="{{ route('krs.index', ['status' => 'diajukan']) }}" class="btn btn-sm btn-outline-warning">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($recentPendingKrs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Mata Kuliah</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPendingKrs as $krs)
                            <tr>
                                <td>
                                    <strong>{{ $krs->mahasiswa->nama ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ $krs->mahasiswa->nim ?? '-' }}</small>
                                </td>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                                <td><small class="text-muted">{{ $krs->created_at->diffForHumans() }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-success py-4">
                    <i class="bi bi-check-all" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Semua KRS sudah diproses</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="bi bi-lightning me-2"></i>Menu Cepat
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-primary quick-btn w-100">
                            <i class="bi bi-people d-block"></i>Mahasiswa
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('dosen.index') }}" class="btn btn-outline-info quick-btn w-100">
                            <i class="bi bi-person-workspace d-block"></i>Dosen
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('krs.index') }}" class="btn btn-outline-success quick-btn w-100">
                            <i class="bi bi-journal-check d-block"></i>KRS
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('nilai.index') }}" class="btn btn-outline-warning quick-btn w-100">
                            <i class="bi bi-award d-block"></i>Nilai
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.tugas-akhir.index') }}" class="btn btn-outline-secondary quick-btn w-100">
                            <i class="bi bi-file-earmark-text d-block"></i>Tugas Akhir
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('wisuda.index') }}" class="btn btn-outline-dark quick-btn w-100">
                            <i class="bi bi-mortarboard d-block"></i>Wisuda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mahasiswaPerProdi = @json($mahasiswaPerProdi);
    const distribusiNilai = @json($distribusiNilai);
    const krsStats = @json($krsStats);
    
    // Chart: Mahasiswa per Prodi
    if (mahasiswaPerProdi.length > 0) {
        new Chart(document.getElementById('chartMahasiswaProdi'), {
            type: 'doughnut',
            data: {
                labels: mahasiswaPerProdi.map(d => d.nama),
                datasets: [{
                    data: mahasiswaPerProdi.map(d => d.total),
                    backgroundColor: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, padding: 8, font: { size: 10 } } }
                }
            }
        });
    }
    
    // Chart: Distribusi Nilai
    const nilaiOrder = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
    const nilaiColors = {'A': '#198754', 'A-': '#20c997', 'B+': '#0d6efd', 'B': '#0dcaf0', 'B-': '#6610f2', 'C+': '#ffc107', 'C': '#fd7e14', 'D': '#dc3545', 'E': '#6c757d'};
    const sortedNilai = nilaiOrder.map(h => distribusiNilai.find(d => d.huruf === h)).filter(d => d);
    
    if (sortedNilai.length > 0) {
        new Chart(document.getElementById('chartDistribusiNilai'), {
            type: 'bar',
            data: {
                labels: sortedNilai.map(d => d.huruf),
                datasets: [{
                    data: sortedNilai.map(d => d.total),
                    backgroundColor: sortedNilai.map(d => nilaiColors[d.huruf] || '#6c757d'),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } }
            }
        });
    }
    
    // Chart: KRS Status
    if (krsStats.approved > 0 || krsStats.pending > 0 || krsStats.rejected > 0) {
        new Chart(document.getElementById('chartKrsStatus'), {
            type: 'doughnut',
            data: {
                labels: ['Disetujui', 'Pending', 'Ditolak'],
                datasets: [{
                    data: [krsStats.approved, krsStats.pending, krsStats.rejected],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endpush
