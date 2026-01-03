@extends('layouts.app')

@section('title', 'Konversi Nilai Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Konversi Nilai / Transfer Kredit</h1>
        @if(!isset($hasDraft) || !$hasDraft)
        <a href="{{ route('mahasiswa.konversi-nilai.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Ajukan Konversi Nilai
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Catatan:</strong> Konversi nilai diperuntukkan bagi mahasiswa pindahan dari kampus lain. 
        Anda dapat mengajukan konversi nilai dan melampirkan transkrip dari perguruan tinggi asal.
    </div>

    @if($pengajuans->isEmpty())
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="bi bi-arrow-left-right display-1 text-muted"></i>
            <h4 class="mt-4">Belum Ada Pengajuan Konversi Nilai</h4>
            <p class="text-muted">Jika Anda mahasiswa pindahan dari kampus lain, Anda dapat mengajukan konversi nilai.</p>
            <a href="{{ route('mahasiswa.konversi-nilai.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg me-1"></i>Ajukan Konversi Nilai
            </a>
        </div>
    </div>
    @else
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. Pengajuan</th>
                            <th>Universitas Asal</th>
                            <th>Prodi Asal</th>
                            <th>Jumlah MK</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengajuans as $index => $pengajuan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $pengajuan->nomor_pengajuan }}</code></td>
                            <td>{{ $pengajuan->universitas_asal }}</td>
                            <td>{{ $pengajuan->program_studi_asal }}</td>
                            <td><span class="badge bg-info">{{ $pengajuan->detailKonversi->count() }} MK</span></td>
                            <td>
                                @php
                                    $badgeClass = match($pengajuan->status) {
                                        'draft' => 'secondary',
                                        'menunggu_kaprodi' => 'info',
                                        'diproses_kaprodi' => 'warning',
                                        'disetujui_kaprodi' => 'primary',
                                        'ditolak_kaprodi' => 'danger',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusLabel = match($pengajuan->status) {
                                        'draft' => 'Draft',
                                        'menunggu_kaprodi' => 'Menunggu Verifikasi',
                                        'diproses_kaprodi' => 'Sedang Diproses',
                                        'disetujui_kaprodi' => 'Disetujui Kaprodi',
                                        'ditolak_kaprodi' => 'Ditolak Kaprodi',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        default => ucfirst(str_replace('_', ' ', $pengajuan->status))
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td>{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('mahasiswa.konversi-nilai.show', $pengajuan->hashid) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
