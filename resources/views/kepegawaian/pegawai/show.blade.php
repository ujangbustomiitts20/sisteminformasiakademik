@extends('layouts.app')

@section('title', 'Detail Tenaga Kependidikan')

@section('content')
<div class="page-title">
    <h4>Detail Tenaga Kependidikan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.index') }}">Tenaga Kependidikan</a></li>
            <li class="breadcrumb-item active">{{ $pegawai->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Profil -->
        <div class="card">
            <div class="card-body text-center">
                @if($pegawai->foto)
                <img src="{{ Storage::url($pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                    {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                </div>
                @endif
                <h5 class="mb-1">{{ $pegawai->nama }}</h5>
                <p class="text-muted mb-2">
                    @if($pegawai->nip)
                    <code>{{ $pegawai->nip }}</code>
                    @else
                    <small class="text-muted">NIP Belum diisi</small>
                    @endif
                </p>
                <p class="mb-2">
                    <span class="badge bg-secondary">{{ $pegawai->jenis_pegawai }}</span>
                    @if($pegawai->status == 'Aktif')
                    <span class="badge bg-success">Aktif</span>
                    @elseif($pegawai->status == 'Cuti')
                    <span class="badge bg-warning">Cuti</span>
                    @else
                    <span class="badge bg-danger">{{ $pegawai->status }}</span>
                    @endif
                </p>
                @if($pegawai->jabatan)
                <p class="mb-0"><strong>{{ $pegawai->jabatan }}</strong></p>
                @endif
                @if($pegawai->unitKerja)
                <small class="text-muted">{{ $pegawai->unitKerja->nama }}</small>
                @endif
            </div>
        </div>

        <!-- Kontak -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-telephone me-2"></i>Informasi Kontak
            </div>
            <div class="card-body">
                <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $pegawai->email ?? '-' }}</p>
                <p class="mb-2"><i class="bi bi-phone me-2 text-muted"></i>{{ $pegawai->no_hp ?? '-' }}</p>
                <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $pegawai->telepon ?? '-' }}</p>
                <hr>
                <p class="mb-0"><i class="bi bi-geo-alt me-2 text-muted"></i>{{ $pegawai->alamat ?? '-' }}</p>
            </div>
        </div>

        <!-- Info Masa Kerja -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Masa Kerja
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">TMT Pegawai</td>
                        <td>{{ $pegawai->tmt_pegawai?->format('d M Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Masa Kerja</td>
                        <td><strong>{{ $pegawai->masa_kerja ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Golongan</td>
                        <td>{{ $pegawai->golongan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pangkat</td>
                        <td>{{ $pegawai->pangkat ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="pegawaiTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pribadi-tab" data-bs-toggle="tab" data-bs-target="#pribadi" type="button">
                    <i class="bi bi-person me-1"></i>Data Pribadi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button">
                    <i class="bi bi-mortarboard me-1"></i>Pendidikan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pelatihan-tab" data-bs-toggle="tab" data-bs-target="#pelatihan" type="button">
                    <i class="bi bi-journal-check me-1"></i>Pelatihan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" type="button">
                    <i class="bi bi-file-earmark-text me-1"></i>Dokumen
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pegawaiTabContent">
            <!-- Tab Pribadi -->
            <div class="tab-pane fade show active" id="pribadi" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" width="40%">NIP</td><td>{{ $pegawai->nip ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">NIK</td><td>{{ $pegawai->nik ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Nama Lengkap</td><td><strong>{{ $pegawai->nama }}</strong></td></tr>
                                    <tr><td class="text-muted">Tempat, Tgl Lahir</td><td>{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir?->format('d M Y') ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Usia</td><td>{{ $pegawai->usia ?? '-' }} tahun</td></tr>
                                    <tr><td class="text-muted">Jenis Kelamin</td><td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" width="40%">Agama</td><td>{{ $pegawai->agama ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">Status Pernikahan</td><td>{{ $pegawai->status_pernikahan ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">NPWP</td><td>{{ $pegawai->npwp ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">No. Rekening</td><td>{{ $pegawai->no_rekening ?? '-' }} {{ $pegawai->nama_bank ? '('.$pegawai->nama_bank.')' : '' }}</td></tr>
                                    <tr><td class="text-muted">BPJS Kesehatan</td><td>{{ $pegawai->no_bpjs_kesehatan ?? '-' }}</td></tr>
                                    <tr><td class="text-muted">BPJS Ketenagakerjaan</td><td>{{ $pegawai->no_bpjs_ketenagakerjaan ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Pendidikan -->
            <div class="tab-pane fade" id="pendidikan" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        @if($pegawai->riwayatPendidikan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenjang</th>
                                        <th>Institusi</th>
                                        <th>Program Studi</th>
                                        <th>Tahun Lulus</th>
                                        <th>IPK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pegawai->riwayatPendidikan as $pend)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $pend->jenjang }}</span></td>
                                        <td><strong>{{ $pend->nama_institusi }}</strong></td>
                                        <td>{{ $pend->program_studi }}</td>
                                        <td>{{ $pend->tahun_lulus }}</td>
                                        <td>{{ $pend->ipk ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-mortarboard text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada data riwayat pendidikan</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Pelatihan -->
            <div class="tab-pane fade" id="pelatihan" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        @if($pegawai->riwayatPelatihan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Nama Pelatihan</th>
                                        <th>Penyelenggara</th>
                                        <th>Tanggal</th>
                                        <th>JP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pegawai->riwayatPelatihan as $plt)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $plt->jenis }}</span></td>
                                        <td>{{ $plt->nama_pelatihan }}</td>
                                        <td>{{ $plt->penyelenggara }}</td>
                                        <td>{{ $plt->tanggal_mulai->format('d M Y') }}</td>
                                        <td>{{ $plt->jumlah_jam ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-journal-check text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada data riwayat pelatihan</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Dokumen -->
            <div class="tab-pane fade" id="dokumen" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        @if($pegawai->dokumenKepegawaian->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Nama Dokumen</th>
                                        <th>No. Dokumen</th>
                                        <th>Status</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pegawai->dokumenKepegawaian as $dok)
                                    <tr>
                                        <td><span class="badge bg-dark">{{ $dok->jenis_dokumen }}</span></td>
                                        <td>{{ $dok->nama_dokumen }}</td>
                                        <td>{{ $dok->no_dokumen ?? '-' }}</td>
                                        <td>
                                            @if($dok->tanggal_berlaku)
                                                @if($dok->isValid())
                                                <span class="badge bg-success">Valid</span>
                                                @else
                                                <span class="badge bg-danger">Expired</span>
                                                @endif
                                            @else
                                            <span class="badge bg-secondary">Permanen</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ Storage::url($dok->file_dokumen) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada dokumen kepegawaian</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}" class="btn btn-primary">
        <i class="bi bi-folder2-open me-1"></i>Kelola Riwayat
    </a>
    <a href="{{ route('kepegawaian.pegawai.edit', $pegawai) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection
