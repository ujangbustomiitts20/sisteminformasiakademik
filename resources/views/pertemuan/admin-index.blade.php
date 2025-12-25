@extends('layouts.app')

@section('title', 'Kelola Jadwal Pertemuan')

@section('content')
<div class="page-title">
    <h4>Kelola Jadwal Pertemuan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jadwal-kuliah.index') }}">Jadwal Kuliah</a></li>
            <li class="breadcrumb-item active">Jadwal Pertemuan</li>
        </ol>
    </nav>
</div>

<!-- Info Jadwal Kuliah -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-1">{{ $jadwalKuliah->mataKuliah->nama }}</h5>
                <p class="text-muted mb-2">
                    <span class="badge bg-secondary">{{ $jadwalKuliah->mataKuliah->kode }}</span>
                    Kelas {{ $jadwalKuliah->kelas }} | {{ $jadwalKuliah->mataKuliah->sks }} SKS
                </p>
                <p class="mb-0">
                    <i class="bi bi-person me-1"></i> <strong>Dosen:</strong> {{ $jadwalKuliah->dosen->nama ?? '-' }}
                </p>
            </div>
            <div class="col-md-6">
                <p class="mb-1">
                    <i class="bi bi-calendar3 me-1"></i> {{ $jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}
                </p>
                <p class="mb-1">
                    <i class="bi bi-door-open me-1"></i> {{ $jadwalKuliah->ruangan->nama ?? '-' }}
                </p>
                <p class="mb-0">
                    <i class="bi bi-book me-1"></i> Jumlah Pertemuan: <strong>{{ $jumlahPertemuan }}x</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Set Tanggal -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-calendar-plus me-2"></i>Atur Tanggal Otomatis
    </div>
    <div class="card-body">
        <form action="{{ route('pertemuan.admin.bulk-set', $jadwalKuliah) }}" method="POST" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Tanggal Mulai Pertemuan 1</label>
                <input type="date" name="tanggal_mulai" class="form-control" required value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Interval (hari)</label>
                <select name="interval_hari" class="form-select">
                    <option value="7" selected>7 hari (Mingguan)</option>
                    <option value="14">14 hari (2 Minggu)</option>
                    <option value="1">1 hari (Harian)</option>
                </select>
            </div>
            <div class="col-md-5">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Ini akan mengatur tanggal untuk semua {{ $jumlahPertemuan }} pertemuan. Lanjutkan?')">
                    <i class="bi bi-calendar-check me-1"></i>Generate {{ $jumlahPertemuan }} Jadwal
                </button>
            </div>
        </form>
        <div class="mt-2">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Fitur ini akan otomatis membuat/memperbarui tanggal untuk semua pertemuan dan langsung menyetujuinya.
            </small>
        </div>
    </div>
</div>

<!-- Daftar Pertemuan -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ol me-2"></i>Daftar Pertemuan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="80" class="text-center">Ke</th>
                        <th>Judul</th>
                        <th width="120" class="text-center">Jenis</th>
                        <th width="130" class="text-center">Tanggal</th>
                        <th width="130" class="text-center">Status</th>
                        <th width="200" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= $jumlahPertemuan; $i++)
                    @php $p = $pertemuanList->get($i); @endphp
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $i }}</span>
                        </td>
                        <td>
                            @if($p)
                                <strong>{{ $p->judul ?? '-' }}</strong>
                                @if($p->deskripsi)
                                <br><small class="text-muted">{{ Str::limit($p->deskripsi, 50) }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p)
                            <span class="badge {{ $p->jenis_badge_class }}">{{ $p->jenis_label }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p && $p->tanggal)
                                {{ $p->tanggal->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Belum diatur</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p)
                                {!! $p->approval_badge !!}
                            @else
                                <span class="badge bg-light text-dark">Kosong</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p)
                                <!-- Edit Tanggal Modal Trigger -->
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTanggalModal{{ $i }}">
                                    <i class="bi bi-calendar-event"></i> Atur Tanggal
                                </button>
                                
                                @if($p->approval_status == 'pending')
                                <form action="{{ route('pertemuan.approve', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <!-- Edit Tanggal Modal -->
                                <div class="modal fade" id="editTanggalModal{{ $i }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('pertemuan.admin.set-tanggal', $p) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Atur Tanggal Pertemuan ke-{{ $i }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control" required value="{{ $p->tanggal?->format('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan & Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Jadwal Kuliah
    </a>
</div>
@endsection
