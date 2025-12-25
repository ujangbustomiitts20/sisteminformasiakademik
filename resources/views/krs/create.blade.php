@extends('layouts.app')

@section('title', 'Ambil KRS')

@section('content')
<div class="page-title">
    <h4>Pengambilan Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('krs.index') }}">KRS</a></li>
            <li class="breadcrumb-item active">Ambil Mata Kuliah</li>
        </ol>
    </nav>
</div>

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show">
    {!! session('warning') !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {!! session('error') !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check me-2"></i>Pilih Mata Kuliah - {{ $tahunAkademikAktif->nama_lengkap }}
    </div>
    <div class="card-body">
        @if($jadwalTersedia->count() > 0)
        <form method="POST" action="{{ route('krs.store') }}">
            @csrf
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Kelas</th>
                            <th>Jadwal</th>
                            <th>Dosen</th>
                            <th>Ruangan</th>
                            <th>Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalTersedia as $jadwal)
                        @php
                            $prasyaratInfo = $jadwal->mataKuliah->cekPrasyarat($mahasiswa->id);
                        @endphp
                        <tr class="{{ !$prasyaratInfo['terpenuhi'] ? 'table-warning' : '' }}">
                            <td>
                                <input type="checkbox" name="jadwal_kuliah_id[]" value="{{ $jadwal->id }}" 
                                    class="form-check-input check-item"
                                    {{ !$prasyaratInfo['terpenuhi'] ? 'disabled' : '' }}
                                    title="{{ !$prasyaratInfo['terpenuhi'] ? 'Prasyarat belum terpenuhi' : '' }}">
                            </td>
                            <td><code>{{ $jadwal->mataKuliah->kode }}</code></td>
                            <td>
                                <strong>{{ $jadwal->mataKuliah->nama }}</strong>
                                <br><small class="text-muted">Semester {{ $jadwal->mataKuliah->semester }} - {{ $jadwal->mataKuliah->jenis }}</small>
                                @if($jadwal->mataKuliah->prasyarat->count() > 0)
                                <br>
                                <small class="text-{{ $prasyaratInfo['terpenuhi'] ? 'success' : 'danger' }}">
                                    <i class="bi bi-{{ $prasyaratInfo['terpenuhi'] ? 'check-circle' : 'exclamation-triangle' }} me-1"></i>
                                    Prasyarat: 
                                    @foreach($jadwal->mataKuliah->prasyarat as $p)
                                    <span class="badge bg-{{ $p->pivot->jenis_prasyarat == 'wajib' ? 'secondary' : 'light text-dark' }} badge-sm">{{ $p->kode }}</span>
                                    @endforeach
                                    @if(!$prasyaratInfo['terpenuhi'])
                                    <br><span class="text-danger small">{{ $prasyaratInfo['pesan'] }}</span>
                                    @endif
                                </small>
                                @endif
                            </td>
                            <td>{{ $jadwal->mataKuliah->sks }}</td>
                            <td><span class="badge bg-secondary">{{ $jadwal->kelas }}</span></td>
                            <td>
                                {{ $jadwal->hari }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</small>
                            </td>
                            <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                            <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $jadwal->sisaKuota() > 10 ? 'success' : ($jadwal->sisaKuota() > 0 ? 'warning' : 'danger') }}">
                                    {{ $jadwal->sisaKuota() }}/{{ $jadwal->kuota }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Pilih mata kuliah yang ingin diambil, kemudian klik "Ajukan KRS"
                    <br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>MK dengan background kuning memiliki prasyarat yang belum terpenuhi</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('krs.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Ajukan KRS
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="text-center py-4">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0 mt-2">Tidak ada mata kuliah yang tersedia untuk diambil</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('checkAll').addEventListener('change', function() {
    // Hanya pilih checkbox yang tidak disabled (prasyarat terpenuhi)
    document.querySelectorAll('.check-item:not(:disabled)').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
