@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<div class="page-title">
    <h4>Edit Absensi Pertemuan ke-{{ $pertemuan }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('absensi.show', $jadwalKuliah) }}">{{ $jadwalKuliah->mataKuliah->kode }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
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
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2"></i>Edit Absensi Pertemuan ke-{{ $pertemuan }}
            </div>
            <div class="card-body">
                <form action="{{ route('absensi.update', [$jadwalKuliah, $pertemuan]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ $absensiData?->tanggal?->format('Y-m-d') ?? date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Materi</label>
                            <input type="text" name="materi" class="form-control" value="{{ $absensiData?->materi }}" placeholder="Topik/materi pertemuan">
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
                                @php
                                    $absensi = $krs->absensi->first();
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $krs->mahasiswa->nim }}</td>
                                    <td>{{ $krs->mahasiswa->nama }}</td>
                                    <td>
                                        <select name="absensi[{{ $krs->id }}][status]" class="form-select form-select-sm" required>
                                            <option value="Hadir" {{ $absensi?->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="Izin" {{ $absensi?->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="Sakit" {{ $absensi?->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="Alpha" {{ $absensi?->status == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="absensi[{{ $krs->id }}][keterangan]" class="form-control form-control-sm" value="{{ $absensi?->keterangan }}" placeholder="Opsional">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update Absensi
                        </button>
                        <a href="{{ route('absensi.show', $jadwalKuliah) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
