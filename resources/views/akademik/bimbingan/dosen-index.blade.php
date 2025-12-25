@extends('layouts.app')

@section('title', 'Bimbingan Akademik - Dosen')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Bimbingan Akademik</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bimbingan Perwalian</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPilihMahasiswa">
        <i class="bi bi-plus-lg me-1"></i>Buat Jadwal Bimbingan
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Mahasiswa Perwalian</h6>
                        <h3 class="mb-0">{{ $stats['mahasiswa_perwalian'] }}</h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Dijadwalkan</h6>
                        <h3 class="mb-0">{{ $stats['dijadwalkan'] }}</h3>
                    </div>
                    <i class="bi bi-calendar-event" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Selesai</h6>
                        <h3 class="mb-0">{{ $stats['selesai'] }}</h3>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Total Bimbingan</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <i class="bi bi-chat-dots" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list me-2"></i>Daftar Bimbingan</span>
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Dijadwalkan" {{ request('status') == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Mahasiswa</th>
                                <th>Jenis</th>
                                <th>Topik</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bimbingans as $bimbingan)
                            <tr>
                                <td>{{ $bimbingan->tanggal_bimbingan->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $bimbingan->mahasiswa->nama }}</strong>
                                    <br><small class="text-muted">{{ $bimbingan->mahasiswa->nim }}</small>
                                </td>
                                <td><span class="badge bg-{{ $bimbingan->jenis_badge }}">{{ $bimbingan->jenis }}</span></td>
                                <td>{{ Str::limit($bimbingan->topik, 30) }}</td>
                                <td><span class="badge bg-{{ $bimbingan->status_badge }}">{{ $bimbingan->status }}</span></td>
                                <td>
                                    <a href="{{ route('bimbingan.show', $bimbingan) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($bimbingan->status == 'Dijadwalkan')
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="selesaikanBimbingan('{{ $bimbingan->hashid }}')">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">Belum ada data bimbingan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($bimbingans->hasPages())
            <div class="card-footer">
                {{ $bimbingans->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Mahasiswa Perwalian
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($mahasiswaPerwalian as $mhs)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $mhs->nama }}</strong>
                            <br><small class="text-muted">{{ $mhs->nim }} - Semester {{ $mhs->semester_aktif }}</small>
                        </div>
                        <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : 'secondary' }}">{{ $mhs->status }}</span>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">
                        Tidak ada mahasiswa perwalian
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-file-check me-2"></i>Persetujuan KRS
            </div>
            <div class="card-body">
                <a href="{{ route('bimbingan.persetujuan-krs') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-card-checklist me-1"></i>Lihat Pengajuan KRS
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesaikan -->
<div class="modal fade" id="modalSelesai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formSelesai" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Bimbingan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status" value="Selesai">
                    <div class="mb-3">
                        <label class="form-label">Catatan Dosen</label>
                        <textarea name="catatan_dosen" class="form-control" rows="3" placeholder="Catatan hasil bimbingan..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rekomendasi</label>
                        <textarea name="rekomendasi" class="form-control" rows="2" placeholder="Rekomendasi untuk mahasiswa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pilih Mahasiswa -->
<div class="modal fade" id="modalPilihMahasiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Pilih mahasiswa untuk membuat jadwal bimbingan:</p>
                <div class="list-group">
                    @foreach($mahasiswaPerwalian as $mhs)
                    <a href="{{ route('bimbingan.buat-jadwal-form', $mhs) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $mhs->nama }}</strong>
                                <br><small class="text-muted">{{ $mhs->nim }} - Semester {{ $mhs->semester_aktif ?? $mhs->semester }}</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selesaikanBimbingan(hashid) {
    document.getElementById('formSelesai').action = '{{ url("/bimbingan-dosen") }}/' + hashid + '/respond';
    new bootstrap.Modal(document.getElementById('modalSelesai')).show();
}
</script>
@endpush
