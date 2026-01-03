@extends('layouts.app')

@section('title', 'KRS Paket')

@section('content')
<div class="page-title">
    <h4>KRS Paket Semester {{ $semesterMahasiswa }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('krs.index') }}">KRS</a></li>
            <li class="breadcrumb-item active">KRS Paket</li>
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

<!-- Info KRS Paket -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-info-circle fs-4 me-3"></i>
        <div>
            <h6 class="mb-1">Mode KRS Paket</h6>
            <p class="mb-0">Mata kuliah sudah ditentukan berdasarkan kurikulum <strong>{{ $kurikulum->nama }}</strong> untuk semester {{ $semesterMahasiswa }}. Anda hanya perlu memilih kelas jika tersedia lebih dari satu kelas.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Semester</h6>
                        <h3 class="mb-0">{{ $semesterMahasiswa }}</h3>
                    </div>
                    <i class="bi bi-calendar3 display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Mata Kuliah</h6>
                        <h3 class="mb-0">{{ $mataKuliahPaket->count() }}</h3>
                    </div>
                    <i class="bi bi-book display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total SKS</h6>
                        <h3 class="mb-0">{{ $mataKuliahPaket->sum(fn($mk) => $mk->mataKuliah->sks ?? 0) }}</h3>
                    </div>
                    <i class="bi bi-mortarboard display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check me-2"></i>Mata Kuliah Paket - {{ $tahunAkademikAktif->nama_lengkap }}
    </div>
    <div class="card-body">
        @if($mataKuliahPaket->count() > 0)
        <form method="POST" action="{{ route('krs.store-paket') }}" id="formKrsPaket">
            @csrf
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Kategori</th>
                            <th>Pilih Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mataKuliahPaket as $index => $kurikulumMk)
                        @php
                            $mk = $kurikulumMk->mataKuliah;
                            $jadwalOptions = $jadwalGrouped->get($mk->id, collect());
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $mk->kode }}</code></td>
                            <td>
                                <strong>{{ $mk->nama }}</strong>
                                <br><small class="text-muted">{{ $mk->jenis }}</small>
                            </td>
                            <td><span class="badge bg-primary">{{ $mk->sks }} SKS</span></td>
                            <td>
                                <span class="badge bg-{{ $kurikulumMk->kategoriBadge }}">{{ $kurikulumMk->kategori }}</span>
                            </td>
                            <td>
                                @if($jadwalOptions->count() > 0)
                                    @if($jadwalOptions->count() == 1)
                                        @php $jadwal = $jadwalOptions->first(); @endphp
                                        <input type="hidden" name="jadwal_kuliah_id[]" value="{{ $jadwal->id }}">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary me-2">{{ $jadwal->kelas }}</span>
                                            <small class="text-muted">
                                                {{ $jadwal->hari }} {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                <br>{{ $jadwal->dosen->nama ?? '-' }} | {{ $jadwal->ruangan->nama ?? '-' }}
                                                <br><span class="badge bg-{{ $jadwal->sisaKuota() > 0 ? 'success' : 'danger' }} badge-sm">Kuota: {{ $jadwal->sisaKuota() }}/{{ $jadwal->kuota }}</span>
                                            </small>
                                        </div>
                                    @else
                                        <select name="jadwal_kuliah_id[]" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach($jadwalOptions as $jadwal)
                                            <option value="{{ $jadwal->id }}" {{ $jadwal->sisaKuota() <= 0 ? 'disabled' : '' }}>
                                                Kelas {{ $jadwal->kelas }} - {{ $jadwal->hari }} {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} 
                                                | {{ $jadwal->dosen->nama ?? '-' }} 
                                                | Kuota: {{ $jadwal->sisaKuota() }}/{{ $jadwal->kuota }}
                                                {{ $jadwal->sisaKuota() <= 0 ? '(PENUH)' : '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    @endif
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Jadwal belum tersedia
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('krs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Ambil KRS Paket
                </button>
            </div>
        </form>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h5 class="mt-3 text-muted">Tidak Ada Mata Kuliah Paket</h5>
            <p class="text-muted">Mata kuliah paket untuk semester {{ $semesterMahasiswa }} belum diatur di kurikulum.</p>
            <a href="{{ route('krs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
