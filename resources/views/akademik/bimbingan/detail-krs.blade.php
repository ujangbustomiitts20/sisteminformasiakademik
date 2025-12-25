@extends('layouts.app')

@section('title', 'Detail KRS Mahasiswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail KRS - {{ $persetujuan->mahasiswa->nama }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bimbingan.persetujuan-krs') }}">Persetujuan KRS</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('bimbingan.persetujuan-krs') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check me-2"></i>Daftar Mata Kuliah yang Diambil</span>
                <span class="badge bg-primary">Total: {{ $totalSks }} SKS</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Mata Kuliah</th>
                                <th class="text-center">SKS</th>
                                <th>Kelas</th>
                                <th>Jadwal</th>
                                <th>Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($krs as $index => $k)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $k->jadwalKuliah->mataKuliah->kode }}</code></td>
                                <td>
                                    {{ $k->jadwalKuliah->mataKuliah->nama }}
                                    <br><small class="text-muted">Semester {{ $k->jadwalKuliah->mataKuliah->semester }}</small>
                                </td>
                                <td class="text-center">{{ $k->jadwalKuliah->mataKuliah->sks }}</td>
                                <td><span class="badge bg-secondary">{{ $k->jadwalKuliah->kelas }}</span></td>
                                <td>
                                    {{ $k->jadwalKuliah->hari }}
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($k->jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($k->jadwalKuliah->jam_selesai)->format('H:i') }}</small>
                                </td>
                                <td>{{ $k->jadwalKuliah->dosen->nama ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada mata kuliah yang diambil
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total SKS:</th>
                                <th class="text-center">{{ $totalSks }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($persetujuan->catatan_mahasiswa)
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-chat-left-text me-2"></i>Catatan dari Mahasiswa
            </div>
            <div class="card-body">
                {{ $persetujuan->catatan_mahasiswa }}
            </div>
        </div>
        @endif

        @if($persetujuan->status == 'Pending')
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-check-square me-2"></i>Proses Persetujuan
            </div>
            <div class="card-body">
                <form id="formPersetujuan">
                    <div class="mb-3">
                        <label class="form-label">Catatan Dosen</label>
                        <textarea name="catatan_dosen" id="catatan_dosen" class="form-control" rows="3" placeholder="Catatan atau feedback untuk mahasiswa..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="prosesKrs('Disetujui')">
                            <i class="bi bi-check-lg me-1"></i>Setujui KRS
                        </button>
                        <button type="button" class="btn btn-warning" onclick="prosesKrs('Revisi')">
                            <i class="bi bi-pencil me-1"></i>Minta Revisi
                        </button>
                        <button type="button" class="btn btn-danger" onclick="prosesKrs('Ditolak')">
                            <i class="bi bi-x-lg me-1"></i>Tolak KRS
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Informasi Mahasiswa
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">NIM</td>
                        <td>{{ $persetujuan->mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $persetujuan->mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $persetujuan->mahasiswa->semester_aktif }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $persetujuan->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td><span class="badge bg-{{ $persetujuan->mahasiswa->status == 'Aktif' ? 'success' : 'secondary' }}">{{ $persetujuan->mahasiswa->status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-calendar me-2"></i>Info Pengajuan
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Tahun Akademik</td>
                        <td>{{ $persetujuan->tahunAkademik->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Pengajuan</td>
                        <td>{{ $persetujuan->tanggal_pengajuan ? $persetujuan->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td><span class="badge bg-{{ $persetujuan->status_badge }}">{{ $persetujuan->status }}</span></td>
                    </tr>
                    @if($persetujuan->tanggal_persetujuan)
                    <tr>
                        <td class="text-muted">Tanggal Proses</td>
                        <td>{{ $persetujuan->tanggal_persetujuan->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        @if($persetujuan->catatan_dosen)
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-chat-right-text me-2"></i>Catatan Dosen
            </div>
            <div class="card-body">
                {{ $persetujuan->catatan_dosen }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function prosesKrs(status) {
    const catatan = document.getElementById('catatan_dosen').value;
    const confirmMsg = {
        'Disetujui': 'Setujui KRS ini?',
        'Revisi': 'Minta revisi untuk KRS ini?',
        'Ditolak': 'Tolak KRS ini?'
    };
    
    if (!confirm(confirmMsg[status])) return;
    
    fetch('{{ route("bimbingan.proses-persetujuan", $persetujuan) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: status,
            catatan_dosen: catatan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Gagal memproses KRS');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan!');
    });
}
</script>
@endpush
