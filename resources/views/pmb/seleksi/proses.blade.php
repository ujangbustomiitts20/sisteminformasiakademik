@extends('layouts.app')

@section('title', 'Proses Seleksi')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Proses Seleksi PMB</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.seleksi.index') }}">Seleksi</a></li>
                <li class="breadcrumb-item active">Proses Seleksi</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Pilih Gelombang -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-2"></i>Pilih Gelombang
        </div>
        <div class="card-body">
            <form action="{{ route('pmb.seleksi.proses') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Gelombang PMB</label>
                        <select name="gelombang" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ request('gelombang') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('gelombang') && $selectedGelombang)
    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Peserta</h6>
                            <h3 class="mb-0">{{ $summary['total_peserta'] }}</h3>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Sudah Input Nilai</h6>
                            <h3 class="mb-0">{{ $summary['sudah_nilai'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Belum Input Nilai</h6>
                            <h3 class="mb-0">{{ $summary['belum_nilai'] }}</h3>
                        </div>
                        <i class="bi bi-clock-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Kuota</h6>
                            <h3 class="mb-0">{{ $summary['total_kuota'] }}</h3>
                        </div>
                        <i class="bi bi-mortarboard-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kuota per Prodi -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-pie-chart me-2"></i>Kuota per Program Studi & Jalur
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Program Studi</th>
                            <th>Jalur Seleksi</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Pendaftar</th>
                            <th class="text-center">Sudah Nilai</th>
                            <th class="text-center">Ketersediaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kuotaPerProdi as $index => $item)
                        <tr>
                            <td>{{ $item['prodi'] }}</td>
                            <td><span class="badge bg-secondary">{{ $item['jalur_kode'] }}</span> {{ $item['jalur'] }}</td>
                            <td class="text-center">{{ $item['kuota'] }}</td>
                            <td class="text-center">
                                @if($item['pendaftar'] > 0)
                                    <a href="#" class="text-primary fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $index }}">
                                        {{ $item['pendaftar'] }}
                                    </a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item['sudah_nilai'] > 0)
                                    <span class="text-success fw-bold">{{ $item['sudah_nilai'] }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item['pendaftar'] > $item['kuota'])
                                    <span class="badge bg-danger">Over {{ $item['pendaftar'] - $item['kuota'] }}</span>
                                @else
                                    <span class="badge bg-success">Sisa {{ $item['kuota'] - $item['pendaftar'] }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item['pendaftar'] > 0)
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $index }}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail untuk setiap baris -->
    @foreach($kuotaPerProdi as $index => $item)
    @if($item['pendaftar'] > 0)
    <div class="modal fade" id="modalDetail{{ $index }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="bi bi-people me-2"></i>Detail Pendaftar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Program Studi:</strong> {{ $item['prodi'] }}
                            </div>
                            <div class="col-md-6">
                                <strong>Jalur:</strong> <span class="badge bg-secondary">{{ $item['jalur_kode'] }}</span> {{ $item['jalur'] }}
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <div class="fs-4 fw-bold text-primary">{{ $item['kuota'] }}</div>
                                    <small class="text-muted">Kuota</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <div class="fs-4 fw-bold text-info">{{ $item['pendaftar'] }}</div>
                                    <small class="text-muted">Pendaftar</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <div class="fs-4 fw-bold text-success">{{ $item['sudah_nilai'] }}</div>
                                    <small class="text-muted">Sudah Nilai</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>No Pendaftaran</th>
                                    <th>Nama</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item['detail'] as $no => $camaba)
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    <td><code>{{ $camaba['no_pendaftaran'] }}</code></td>
                                    <td>{{ $camaba['nama'] }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'menunggu_bayar' => 'warning',
                                                'terdaftar' => 'info',
                                                'mengikuti_ujian' => 'primary',
                                                'lulus' => 'success',
                                                'tidak_lulus' => 'danger',
                                                'daftar_ulang' => 'info',
                                                'menjadi_mahasiswa' => 'success',
                                            ];
                                            $color = $statusColors[$camaba['status']] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ str_replace('_', ' ', ucfirst($camaba['status'])) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($camaba['sudah_nilai'])
                                            <span class="fw-bold text-success">{{ $camaba['total_nilai'] }}</span>
                                        @else
                                            <span class="text-muted">Belum input</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('pmb.calon-mahasiswa.index', ['prodi' => $item['prodi_id'], 'jalur' => $item['jalur_id'], 'gelombang' => $selectedGelombang->id]) }}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat di Halaman Camaba
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <!-- Proses Seleksi -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-gear me-2"></i>Proses Seleksi
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Proses seleksi akan menghitung ranking berdasarkan nilai yang sudah diinput, 
                lalu menentukan status kelulusan berdasarkan kuota yang tersedia untuk setiap program studi.
            </div>

            @if($summary['belum_nilai'] > 0)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Perhatian:</strong> Masih ada {{ $summary['belum_nilai'] }} peserta yang belum diinput nilainya. 
                Peserta tersebut tidak akan diikutkan dalam proses seleksi.
            </div>
            @endif

            <form action="{{ route('pmb.seleksi.execute') }}" method="POST" onsubmit="return confirm('Yakin ingin memproses seleksi untuk gelombang ini? Hasil seleksi sebelumnya akan di-reset.')">
                @csrf
                <input type="hidden" name="gelombang_id" value="{{ $selectedGelombang->id }}">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Passing Grade (Nilai Minimum Lulus)</label>
                        <input type="number" name="passing_grade" class="form-control" value="{{ old('passing_grade', 60) }}" min="0" max="100" step="0.01">
                        <small class="text-muted">Peserta dengan nilai di bawah passing grade otomatis tidak lulus</small>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="consider_prodi_2" id="considerProdi2" value="1" checked>
                        <label class="form-check-label" for="considerProdi2">
                            Pertimbangkan pilihan prodi kedua jika tidak lolos di prodi pertama
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" {{ $summary['sudah_nilai'] == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-play me-1"></i> Proses Seleksi
                    </button>
                    <a href="{{ route('pmb.seleksi.hasil', ['gelombang' => $selectedGelombang->id]) }}" class="btn btn-info">
                        <i class="bi bi-list-ul me-1"></i> Lihat Hasil Seleksi
                    </a>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-funnel fs-1 text-muted mb-3"></i>
            <p class="text-muted">Silakan pilih gelombang PMB terlebih dahulu</p>
        </div>
    </div>
    @endif
</div>
@endsection
