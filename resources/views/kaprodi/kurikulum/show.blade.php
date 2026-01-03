@extends('layouts.app')

@section('title', 'Detail Kurikulum')

@section('content')
<div class="page-title">
    <h4>Detail Kurikulum</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.kurikulum.index') }}">Kurikulum</a></li>
            <li class="breadcrumb-item active">{{ $kurikulum->kode }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Kurikulum</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="40%">Kode</td>
                        <td><strong>{{ $kurikulum->kode }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $kurikulum->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun</td>
                        <td>{{ $kurikulum->tahun_mulai }} - {{ $kurikulum->tahun_selesai ?? 'Sekarang' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS Wajib</td>
                        <td>{{ $kurikulum->total_sks_wajib ?? 0 }} SKS</td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS Pilihan</td>
                        <td>{{ $kurikulum->total_sks_pilihan ?? 0 }} SKS</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total SKS Lulus</td>
                        <td><strong>{{ $kurikulum->total_sks_lulus ?? 0 }} SKS</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($kurikulum->is_aktif)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if($kurikulum->deskripsi)
                <hr>
                <p class="small text-muted mb-0">{{ $kurikulum->deskripsi }}</p>
                @endif
            </div>
        </div>

        <a href="{{ route('kaprodi.kurikulum.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Mata Kuliah ({{ $kurikulum->mataKuliah->count() }} MK)</h6>
            </div>
            <div class="card-body">
                @for($semester = 1; $semester <= 8; $semester++)
                @php
                    $mkSemester = $kurikulum->mataKuliah->filter(fn($mk) => $mk->pivot->semester_rekomendasi == $semester);
                @endphp
                @if($mkSemester->count() > 0)
                <h6 class="mt-3 mb-2">Semester {{ $semester }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mata Kuliah</th>
                                <th class="text-center">SKS</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mkSemester as $mk)
                            <tr>
                                <td>{{ $mk->kode }}</td>
                                <td>{{ $mk->nama }}</td>
                                <td class="text-center">{{ $mk->sks }}</td>
                                <td>
                                    <span class="badge bg-{{ $mk->pivot->kategori == 'Wajib' ? 'primary' : 'secondary' }}">
                                        {{ $mk->pivot->kategori ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="2" class="text-end">Total SKS Semester {{ $semester }}</th>
                                <th class="text-center">{{ $mkSemester->sum('sks') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
                @endfor

                @if($kurikulum->mataKuliah->count() == 0)
                <div class="text-center text-muted py-4">
                    <i class="bi bi-journal-x fs-1"></i>
                    <p>Belum ada mata kuliah dalam kurikulum ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
