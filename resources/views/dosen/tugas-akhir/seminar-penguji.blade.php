@extends('layouts.app')

@section('title', 'Seminar Proposal - Penguji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Seminar Proposal (Sebagai Penguji)</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Seminar Penguji</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Judul</th>
                            <th>Jadwal</th>
                            <th>Ruangan</th>
                            <th>Sebagai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seminars as $key => $seminar)
                        @php
                            $dosenId = $dosen->id ?? 0;
                            $sebagai = $seminar->penguji_1_id == $dosenId ? 'Penguji 1' : 'Penguji 2';
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $seminar->tugasAkhir->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $seminar->tugasAkhir->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>
                                <span title="{{ $seminar->tugasAkhir->judul }}">{{ Str::limit($seminar->tugasAkhir->judul ?? '-', 50) }}</span>
                            </td>
                            <td>
                                @if($seminar->tanggal)
                                {{ $seminar->tanggal->format('d M Y') }}<br>
                                <small>{{ $seminar->waktu_mulai }} - {{ $seminar->waktu_selesai }}</small>
                                @else
                                <span class="text-muted">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td>{{ $seminar->ruangan ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $sebagai }}</span></td>
                            <td>
                                <span class="badge bg-{{ $seminar->status_badge }}">{{ $seminar->status_label }}</span>
                            </td>
                            <td>
                                @if($seminar->status == 'dijadwalkan' || $seminar->status == 'berlangsung')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nilaiModal{{ $seminar->hashid }}">
                                    <i class="bi bi-pencil"></i> Nilai
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $seminar->hashid }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $seminar->hashid }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Seminar</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h6>Judul Tugas Akhir</h6>
                                        <p>{{ $seminar->tugasAkhir->judul }}</p>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Mahasiswa</h6>
                                                <p>
                                                    {{ $seminar->tugasAkhir->mahasiswa->nama ?? '-' }}<br>
                                                    <small class="text-muted">{{ $seminar->tugasAkhir->mahasiswa->nim ?? '-' }}</small>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Pembimbing</h6>
                                                <ul class="list-unstyled">
                                                    <li>1. {{ $seminar->tugasAkhir->pembimbing1->nama ?? '-' }}</li>
                                                    <li>2. {{ $seminar->tugasAkhir->pembimbing2->nama ?? '-' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        @if($seminar->nilai_akhir)
                                        <hr>
                                        <h6>Hasil Penilaian</h6>
                                        <table class="table table-sm">
                                            <tr><td>Nilai Penguji 1</td><td>{{ $seminar->nilai_penguji_1 }}</td></tr>
                                            <tr><td>Nilai Penguji 2</td><td>{{ $seminar->nilai_penguji_2 }}</td></tr>
                                            <tr><td>Nilai Pembimbing 1</td><td>{{ $seminar->nilai_pembimbing_1 }}</td></tr>
                                            <tr><td>Nilai Pembimbing 2</td><td>{{ $seminar->nilai_pembimbing_2 }}</td></tr>
                                            <tr class="fw-bold"><td>Nilai Akhir</td><td>{{ number_format($seminar->nilai_akhir, 2) }}</td></tr>
                                            <tr class="fw-bold"><td>Hasil</td><td>{{ ucfirst(str_replace('_', ' ', $seminar->hasil)) }}</td></tr>
                                        </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nilai Modal -->
                        @if($seminar->status == 'dijadwalkan' || $seminar->status == 'berlangsung')
                        <div class="modal fade" id="nilaiModal{{ $seminar->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('dosen.tugas-akhir.seminar.nilai', $seminar->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Input Nilai Seminar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>Mahasiswa:</strong> {{ $seminar->tugasAkhir->mahasiswa->nama }}<br>
                                                <strong>Judul:</strong> {{ Str::limit($seminar->tugasAkhir->judul, 100) }}
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nilai Anda ({{ $sebagai }})</label>
                                                <input type="number" name="nilai" class="form-control" 
                                                    min="0" max="100" 
                                                    value="{{ $sebagai == 'Penguji 1' ? $seminar->nilai_penguji_1 : $seminar->nilai_penguji_2 }}" 
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan/Revisi</label>
                                                <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan revisi jika ada..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Tidak ada jadwal seminar sebagai penguji
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
