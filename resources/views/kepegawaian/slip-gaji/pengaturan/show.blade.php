@extends('layouts.app')

@section('title', 'Detail Pengaturan Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pengaturan Gaji</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.index') }}">Slip Gaji</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.pengaturan.index') }}">Pengaturan</a></li>
                    <li class="breadcrumb-item active">{{ $pengaturan->nama_pegawai }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('kepegawaian.slip-gaji.pengaturan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('kepegawaian.slip-gaji.pengaturan.update', $pengaturan->hashid) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Pegawai</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Nama</td>
                                <td><strong>{{ $pengaturan->nama_pegawai }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tipe</td>
                                <td>
                                    <span class="badge bg-{{ $pengaturan->tipe_pegawai == 'Dosen' ? 'success' : 'info' }}">
                                        {{ $pengaturan->tipe_pegawai }}
                                    </span>
                                </td>
                            </tr>
                            @if($pengaturan->dosen)
                            <tr>
                                <td class="text-muted">NIDN</td>
                                <td>{{ $pengaturan->dosen->nidn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Prodi</td>
                                <td>{{ $pengaturan->dosen->programStudi->nama ?? '-' }}</td>
                            </tr>
                            @elseif($pengaturan->pegawai)
                            <tr>
                                <td class="text-muted">NIP</td>
                                <td>{{ $pengaturan->pegawai->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Unit Kerja</td>
                                <td>{{ $pengaturan->pegawai->unitKerja->nama ?? '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Pengaturan Dasar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji_pokok" class="form-control" value="{{ $pengaturan->gaji_pokok }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Berlaku Mulai</label>
                                    <input type="date" name="berlaku_mulai" class="form-control" 
                                        value="{{ $pengaturan->berlaku_mulai?->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Berlaku Sampai</label>
                                    <input type="date" name="berlaku_sampai" class="form-control" 
                                        value="{{ $pengaturan->berlaku_sampai?->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ $pengaturan->catatan }}</textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="aktif" class="form-check-input" {{ $pengaturan->aktif ? 'checked' : '' }}>
                            <label class="form-check-label">Pengaturan Aktif</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Komponen Gaji</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $existingKomponens = $pengaturan->details->keyBy('komponen_gaji_id');
                        @endphp

                        <!-- Pendapatan -->
                        <h6 class="text-success mb-3"><i class="bi bi-plus-circle me-1"></i> Tunjangan/Pendapatan</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%"></th>
                                        <th>Komponen</th>
                                        <th width="35%">Nilai (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komponens->where('jenis', 'pendapatan') as $komponen)
                                    @php
                                        $existing = $existingKomponens->get($komponen->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" 
                                                {{ $existing ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $komponen->nama }}</td>
                                        <td>
                                            <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                                class="form-control form-control-sm" 
                                                value="{{ $existing?->nilai ?? $komponen->nilai_default }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Potongan -->
                        <h6 class="text-danger mb-3"><i class="bi bi-dash-circle me-1"></i> Potongan</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%"></th>
                                        <th>Komponen</th>
                                        <th width="35%">Nilai (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komponens->where('jenis', 'potongan') as $komponen)
                                    @php
                                        $existing = $existingKomponens->get($komponen->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" 
                                                {{ $existing ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $komponen->nama }}</td>
                                        <td>
                                            <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                                class="form-control form-control-sm" 
                                                value="{{ $existing?->nilai ?? $komponen->nilai_default }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('kepegawaian.slip-gaji.pengaturan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
