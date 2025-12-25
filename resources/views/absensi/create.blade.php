@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div class="page-title">
    <h4>Input Absensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li class="breadcrumb-item active">Input</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Kelas
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="120">Mata Kuliah</td>
                        <td>: <strong>{{ $jadwalKuliah->mataKuliah->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td>Kode</td>
                        <td>: {{ $jadwalKuliah->mataKuliah->kode }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $jadwalKuliah->kelas }}</td>
                    </tr>
                    <tr>
                        <td>SKS</td>
                        <td>: {{ $jadwalKuliah->mataKuliah->sks }}</td>
                    </tr>
                    <tr>
                        <td>Jadwal</td>
                        <td>: {{ $jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Ruangan</td>
                        <td>: {{ $jadwalKuliah->ruangan->nama }}</td>
                    </tr>
                    <tr>
                        <td>Total Pertemuan</td>
                        <td>: <span class="badge bg-info">{{ $jumlahPertemuan }}x</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-check me-2"></i>Form Absensi Pertemuan ke-{{ $pertemuan }} dari {{ $jumlahPertemuan }}
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($mahasiswa->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Belum ada mahasiswa yang mengambil mata kuliah ini.
                </div>
                @else
                <form action="{{ route('absensi.store', $jadwalKuliah) }}" method="POST">
                    @csrf
                    <input type="hidden" name="pertemuan" value="{{ $pertemuan }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Materi</label>
                            <input type="text" name="materi" class="form-control" placeholder="Topik/materi pertemuan">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Daftar Mahasiswa</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success" onclick="setAllStatus('Hadir')">Semua Hadir</button>
                                <button type="button" class="btn btn-outline-danger" onclick="setAllStatus('Alpha')">Semua Alpha</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th width="150">Status</th>
                                    <th width="200">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mahasiswa as $index => $krs)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $krs->mahasiswa->nim }}</td>
                                    <td>{{ $krs->mahasiswa->nama }}</td>
                                    <td>
                                        <input type="hidden" name="absensi[{{ $index }}][krs_id]" value="{{ $krs->id }}">
                                        <select name="absensi[{{ $index }}][status]" class="form-select form-select-sm status-select" required>
                                            <option value="Hadir" selected>Hadir</option>
                                            <option value="Izin">Izin</option>
                                            <option value="Sakit">Sakit</option>
                                            <option value="Alpha">Alpha</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="absensi[{{ $index }}][keterangan]" class="form-control form-control-sm" placeholder="Opsional">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Absensi
                        </button>
                        <a href="{{ route('absensi.show', $jadwalKuliah) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setAllStatus(status) {
    document.querySelectorAll('.status-select').forEach(select => {
        select.value = status;
    });
}
</script>
@endpush
@endsection
