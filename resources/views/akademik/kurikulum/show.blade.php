@extends('layouts.app')

@section('title', 'Detail Kurikulum')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $kurikulum->nama }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kurikulum.index') }}">Kurikulum</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('kurikulum.mata-kuliah', $kurikulum) }}" class="btn btn-primary">
            <i class="bi bi-book me-1"></i>Kelola Mata Kuliah
        </a>
        <a href="{{ route('kurikulum.edit', $kurikulum) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Kurikulum
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">Kode</td>
                        <td><code>{{ $kurikulum->kode }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $kurikulum->programStudi->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun Berlaku</td>
                        <td>{{ $kurikulum->tahun_mulai }} - {{ $kurikulum->tahun_selesai ?? 'Sekarang' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($kurikulum->is_aktif)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $kurikulum->minimal_semester }} - {{ $kurikulum->maksimal_semester }} Semester</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-calculator me-2"></i>Distribusi SKS
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SKS Wajib</span>
                    <strong>{{ $totalSks['wajib'] }} / {{ $kurikulum->total_sks_wajib }}</strong>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-danger" style="width: {{ $kurikulum->total_sks_wajib > 0 ? ($totalSks['wajib'] / $kurikulum->total_sks_wajib * 100) : 0 }}%"></div>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SKS Pilihan</span>
                    <strong>{{ $totalSks['pilihan'] }} / {{ $kurikulum->total_sks_pilihan }}</strong>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-info" style="width: {{ $kurikulum->total_sks_pilihan > 0 ? ($totalSks['pilihan'] / $kurikulum->total_sks_pilihan * 100) : 0 }}%"></div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total SKS</span>
                    <strong class="text-primary">{{ $totalSks['total'] }} / {{ $kurikulum->total_sks_lulus }}</strong>
                </div>
            </div>
        </div>

        @if($kurikulum->deskripsi)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-text me-2"></i>Deskripsi
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $kurikulum->deskripsi }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-grid me-2"></i>Struktur Kurikulum per Semester
            </div>
            <div class="card-body">
                <div class="row">
                    @for($semester = 1; $semester <= 8; $semester++)
                    <div class="col-md-6 mb-4">
                        <div class="border rounded p-3">
                            <h6 class="mb-3">
                                <i class="bi bi-calendar3 me-1"></i>Semester {{ $semester }}
                                <span class="badge bg-secondary float-end">
                                    {{ $mkBySemester[$semester]->sum('sks') }} SKS
                                </span>
                            </h6>
                            @if($mkBySemester[$semester]->count() > 0)
                            <ul class="list-unstyled mb-0 small">
                                @foreach($mkBySemester[$semester] as $mk)
                                <li class="mb-1">
                                    <span class="badge bg-{{ $mk->pivot->kategori == 'Wajib' ? 'danger' : ($mk->pivot->kategori == 'Pilihan' ? 'info' : 'secondary') }} badge-sm me-1">
                                        {{ $mk->sks }}
                                    </span>
                                    <code class="me-1">{{ $mk->kode }}</code>
                                    {{ Str::limit($mk->nama, 25) }}
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-muted small mb-0 text-center">
                                <i class="bi bi-inbox"></i> Belum ada MK
                            </p>
                            @endif
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
