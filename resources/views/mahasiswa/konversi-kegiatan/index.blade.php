@extends('layouts.app')

@section('title', 'Konversi Kegiatan (RPL)')

@section('content')
<div class="page-title">
    <h4>Konversi Kegiatan (RPL)</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Konversi Kegiatan</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Tentang Konversi Kegiatan (RPL)</h6>
            <p class="mb-2">Fitur ini memungkinkan Anda mengajukan konversi kegiatan non-akademik menjadi nilai mata kuliah, seperti:</p>
            <ul class="mb-0">
                <li><strong>Sertifikasi Profesional</strong> - CCNA, AWS, Oracle, dll</li>
                <li><strong>Lomba/Kompetisi</strong> - Juara lomba tingkat nasional/internasional</li>
                <li><strong>Magang/Internship</strong> - Pengalaman magang di industri</li>
                <li><strong>Kursus/Pelatihan</strong> - Course online bersertifikat</li>
                <li><strong>Pengalaman Kerja</strong> - Pengalaman kerja relevan</li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan</h5>
            <a href="{{ route('konversi-kegiatan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Ajukan Konversi Baru
            </a>
        </div>
    </div>
</div>

@if($pengajuans->isEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-earmark-x display-4 text-muted"></i>
                <h5 class="mt-3">Belum Ada Pengajuan</h5>
                <p class="text-muted">Anda belum memiliki pengajuan konversi kegiatan.</p>
                <a href="{{ route('konversi-kegiatan.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-lg me-2"></i>Buat Pengajuan Baru
                </a>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Tanggal</th>
                                <th>Tahun Akademik</th>
                                <th>Jumlah Kegiatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuans as $pengajuan)
                            <tr>
                                <td>
                                    <strong>{{ $pengajuan->no_pengajuan }}</strong>
                                </td>
                                <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                                <td>{{ $pengajuan->tahunAkademik->tahun }} {{ $pengajuan->tahunAkademik->semester }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $pengajuan->details->count() }} kegiatan</span>
                                </td>
                                <td>{!! $pengajuan->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('konversi-kegiatan.show', $pengajuan) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $pengajuans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
