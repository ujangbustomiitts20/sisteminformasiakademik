@extends('layouts.app')

@section('title', 'Jadwal Bimbingan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Jadwal Bimbingan TA</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dosen.tugas-akhir.index') }}">Bimbingan TA</a></li>
                    <li class="breadcrumb-item active">Jadwal</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Jadwal Bimbingan yang Menunggu</h5>
        </div>
        <div class="card-body">
            @if($jadwals->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-check display-1 text-muted"></i>
                <p class="text-muted mt-3">Tidak ada jadwal bimbingan yang menunggu</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal & Waktu</th>
                            <th>Mahasiswa</th>
                            <th>Judul TA</th>
                            <th>Materi</th>
                            <th>Tempat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwals as $key => $jadwal)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $jadwal->tanggal->format('d M Y') }}</strong><br>
                                @if($jadwal->waktu_mulai)
                                <small class="text-muted">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai ?? '...' }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $jadwal->tugasAkhir->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $jadwal->tugasAkhir->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>
                                <span title="{{ $jadwal->tugasAkhir->judul }}">
                                    {{ Str::limit($jadwal->tugasAkhir->judul, 40) }}
                                </span>
                            </td>
                            <td>{{ $jadwal->materi_bimbingan ?? '-' }}</td>
                            <td>{{ $jadwal->tempat ?? '-' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dosen.tugas-akhir.show', $jadwal->tugasAkhir->hashid) }}" class="btn btn-outline-primary" title="Lihat Detail TA">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#inputModal{{ $jadwal->id }}" title="Input Hasil">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $jadwal->id }}" title="Reschedule">
                                        <i class="bi bi-calendar"></i>
                                    </button>
                                </div>

                                <!-- Modal Input Hasil -->
                                <div class="modal fade" id="inputModal{{ $jadwal->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('dosen.tugas-akhir.bimbingan.input', $jadwal->hashid) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Input Hasil Bimbingan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        <strong>{{ $jadwal->tugasAkhir->mahasiswa->nama }}</strong> - {{ $jadwal->tanggal->format('d M Y') }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Hasil Bimbingan <span class="text-danger">*</span></label>
                                                        <textarea name="hasil_bimbingan" class="form-control" rows="3" required placeholder="Tuliskan hasil dari bimbingan"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan Dosen</label>
                                                        <textarea name="catatan_dosen" class="form-control" rows="2" placeholder="Catatan atau saran untuk mahasiswa"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Rencana Selanjutnya</label>
                                                        <textarea name="rencana_selanjutnya" class="form-control" rows="2" placeholder="Apa yang perlu dikerjakan selanjutnya"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Progress (%) <span class="text-danger">*</span></label>
                                                        <input type="number" name="persentase_progress" class="form-control" min="0" max="100" required placeholder="0-100">
                                                    </div>
                                                    <input type="hidden" name="status" value="selesai">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check me-1"></i>Simpan Hasil
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Reschedule -->
                                <div class="modal fade" id="rescheduleModal{{ $jadwal->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('dosen.tugas-akhir.bimbingan.reschedule', $jadwal->hashid) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Jadwalkan Ulang Bimbingan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        Jadwal saat ini: <strong>{{ $jadwal->tanggal->format('d M Y') }}</strong>
                                                        @if($jadwal->waktu_mulai) {{ $jadwal->waktu_mulai }} @endif
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                                                        <input type="date" name="tanggal" class="form-control" required min="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Waktu Mulai</label>
                                                                <input type="time" name="waktu_mulai" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Waktu Selesai</label>
                                                                <input type="time" name="waktu_selesai" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tempat</label>
                                                        <input type="text" name="tempat" class="form-control" placeholder="Ruangan/Online" value="{{ $jadwal->tempat }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan</label>
                                                        <textarea name="catatan_dosen" class="form-control" rows="2" placeholder="Alasan reschedule (opsional)"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="bi bi-calendar me-1"></i>Reschedule
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
