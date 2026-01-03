@extends('layouts.app')

@section('title', 'Sidang TA - Penguji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Sidang Tugas Akhir (Sebagai Penguji)</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sidang Penguji</li>
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
                        @forelse($sidangs as $key => $sidang)
                        @php
                            $dosenId = $dosen->id ?? 0;
                            if ($sidang->ketua_penguji_id == $dosenId) {
                                $sebagai = 'Ketua Penguji';
                            } elseif ($sidang->penguji_1_id == $dosenId) {
                                $sebagai = 'Penguji 1';
                            } else {
                                $sebagai = 'Penguji 2';
                            }
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $sidang->tugasAkhir->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $sidang->tugasAkhir->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>
                                <span title="{{ $sidang->tugasAkhir->judul }}">{{ Str::limit($sidang->tugasAkhir->judul ?? '-', 50) }}</span>
                            </td>
                            <td>
                                @if($sidang->tanggal)
                                {{ $sidang->tanggal->format('d M Y') }}<br>
                                <small>{{ $sidang->waktu_mulai }} - {{ $sidang->waktu_selesai }}</small>
                                @else
                                <span class="text-muted">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td>{{ $sidang->ruangan ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $sebagai == 'Ketua Penguji' ? 'danger' : 'info' }}">{{ $sebagai }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $sidang->status_badge }}">{{ $sidang->status_label }}</span>
                            </td>
                            <td>
                                <a href="{{ route('dosen.tugas-akhir.sidang.show', $sidang->hashid) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($sidang->status == 'dijadwalkan' || $sidang->status == 'berlangsung')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nilaiModal{{ $sidang->hashid }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Nilai Modal -->
                        @if($sidang->status == 'dijadwalkan' || $sidang->status == 'berlangsung')
                        <div class="modal fade" id="nilaiModal{{ $sidang->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('dosen.tugas-akhir.sidang.nilai', $sidang->hashid) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Input Nilai Sidang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>Mahasiswa:</strong> {{ $sidang->tugasAkhir->mahasiswa->nama }}<br>
                                                <strong>Judul:</strong> {{ Str::limit($sidang->tugasAkhir->judul, 100) }}
                                            </div>
                                            
                                            <h6>Nilai Komponen</h6>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Presentasi</label>
                                                    <input type="number" name="nilai_presentasi" class="form-control" min="0" max="100" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Penguasaan Materi</label>
                                                    <input type="number" name="nilai_penguasaan_materi" class="form-control" min="0" max="100" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Tanya Jawab</label>
                                                    <input type="number" name="nilai_tanya_jawab" class="form-control" min="0" max="100" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Dokumen</label>
                                                    <input type="number" name="nilai_dokumen" class="form-control" min="0" max="100" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Nilai Total ({{ $sebagai }})</label>
                                                <input type="number" name="nilai" class="form-control" min="0" max="100" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Catatan Revisi</label>
                                                <textarea name="catatan_revisi" class="form-control" rows="4" placeholder="Catatan revisi untuk mahasiswa..."></textarea>
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
                                    Tidak ada jadwal sidang sebagai penguji
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
