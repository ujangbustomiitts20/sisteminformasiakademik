@extends('layouts.app')

@section('title', 'Input Nilai')

@section('content')
<div class="page-title">
    <h4>Input Nilai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('nilai.index') }}">Input Nilai</a></li>
            <li class="breadcrumb-item active">{{ $jadwalKuliah->mataKuliah->nama }}</li>
        </ol>
    </nav>
</div>

<!-- Info Kelas -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="150">Mata Kuliah</td>
                        <td>: <strong>{{ $jadwalKuliah->mataKuliah->nama }}</strong> ({{ $jadwalKuliah->mataKuliah->kode }})</td>
                    </tr>
                    <tr>
                        <td>SKS</td>
                        <td>: {{ $jadwalKuliah->mataKuliah->sks }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $jadwalKuliah->kelas }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="150">Tahun Akademik</td>
                        <td>: {{ $jadwalKuliah->tahunAkademik->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td>Jadwal</td>
                        <td>: {{ $jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Ruangan</td>
                        <td>: {{ $jadwalKuliah->ruangan->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Form Input Nilai -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-2"></i>Daftar Mahasiswa ({{ $krsData->count() }} peserta)</span>
        <div class="text-muted small">
            <strong>Komposisi Nilai:</strong> Tugas 30% | UTS 30% | UAS 40%
        </div>
    </div>
    <div class="card-body">
        @if($krsData->count() > 0)
        <form method="POST" action="{{ route('nilai.store', $jadwalKuliah) }}">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th width="120">NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th class="text-center" width="100">Tugas (30%)</th>
                            <th class="text-center" width="100">UTS (30%)</th>
                            <th class="text-center" width="100">UAS (40%)</th>
                            <th class="text-center" width="100">Nilai Akhir</th>
                            <th class="text-center" width="70">Huruf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($krsData as $index => $krs)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><code>{{ $krs->mahasiswa->nim }}</code></td>
                            <td>{{ $krs->mahasiswa->nama }}</td>
                            <td>
                                <input type="hidden" name="nilai[{{ $index }}][krs_id]" value="{{ $krs->id }}">
                                <input type="number" name="nilai[{{ $index }}][tugas]" class="form-control form-control-sm text-center nilai-input" 
                                    value="{{ $krs->nilai->tugas ?? '' }}" min="0" max="100" step="0.01" data-row="{{ $index }}">
                            </td>
                            <td>
                                <input type="number" name="nilai[{{ $index }}][uts]" class="form-control form-control-sm text-center nilai-input" 
                                    value="{{ $krs->nilai->uts ?? '' }}" min="0" max="100" step="0.01" data-row="{{ $index }}">
                            </td>
                            <td>
                                <input type="number" name="nilai[{{ $index }}][uas]" class="form-control form-control-sm text-center nilai-input" 
                                    value="{{ $krs->nilai->uas ?? '' }}" min="0" max="100" step="0.01" data-row="{{ $index }}">
                            </td>
                            <td class="text-center fw-bold" id="na-{{ $index }}">{{ $krs->nilai?->nilai_akhir ? number_format($krs->nilai->nilai_akhir, 2) : '-' }}</td>
                            <td class="text-center" id="huruf-{{ $index }}">
                                @if($krs->nilai && $krs->nilai->huruf)
                                <span class="badge bg-{{ in_array($krs->nilai->huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($krs->nilai->huruf, ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                    {{ $krs->nilai->huruf }}
                                </span>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Nilai
                </button>
                <a href="{{ route('nilai.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
        @else
        <div class="text-center py-4">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0 mt-2">Belum ada mahasiswa yang terdaftar di kelas ini</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto calculate nilai akhir
document.querySelectorAll('.nilai-input').forEach(input => {
    input.addEventListener('input', function() {
        const row = this.dataset.row;
        const tugas = parseFloat(document.querySelector(`input[name="nilai[${row}][tugas]"]`).value) || 0;
        const uts = parseFloat(document.querySelector(`input[name="nilai[${row}][uts]"]`).value) || 0;
        const uas = parseFloat(document.querySelector(`input[name="nilai[${row}][uas]"]`).value) || 0;
        
        if (tugas > 0 || uts > 0 || uas > 0) {
            const na = (tugas * 0.3) + (uts * 0.3) + (uas * 0.4);
            document.getElementById(`na-${row}`).textContent = na.toFixed(2);
            
            let huruf = 'E';
            let badgeClass = 'danger';
            if (na >= 85) { huruf = 'A'; badgeClass = 'success'; }
            else if (na >= 80) { huruf = 'A-'; badgeClass = 'success'; }
            else if (na >= 75) { huruf = 'B+'; badgeClass = 'success'; }
            else if (na >= 70) { huruf = 'B'; badgeClass = 'success'; }
            else if (na >= 65) { huruf = 'B-'; badgeClass = 'warning'; }
            else if (na >= 60) { huruf = 'C+'; badgeClass = 'warning'; }
            else if (na >= 55) { huruf = 'C'; badgeClass = 'warning'; }
            else if (na >= 50) { huruf = 'D'; badgeClass = 'danger'; }
            
            document.getElementById(`huruf-${row}`).innerHTML = `<span class="badge bg-${badgeClass}">${huruf}</span>`;
        }
    });
});
</script>
@endpush
