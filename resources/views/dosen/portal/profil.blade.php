@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="page-title">
    <h4>Profil Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Kolom Kiri - Info Utama -->
    <div class="col-lg-4">
        <!-- Card Foto & Info Dasar -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($dosen->foto)
                <img src="{{ Storage::url($dosen->foto) }}" alt="{{ $dosen->nama }}" class="rounded-circle mb-3 shadow" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 150px; height: 150px; font-size: 4rem;">
                    {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                </div>
                @endif
                
                <h5 class="mb-1">{{ $dosen->gelar_depan }} {{ $dosen->nama }}{{ $dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '' }}</h5>
                <p class="text-muted mb-2">NIDN: <code>{{ $dosen->nidn }}</code></p>
                
                <div class="mb-3">
                    @if($dosen->status == 'Aktif')
                    <span class="badge bg-success">{{ $dosen->status }}</span>
                    @else
                    <span class="badge bg-secondary">{{ $dosen->status }}</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-2">
                        <i class="bi bi-building me-2 text-muted"></i>
                        {{ $dosen->programStudi->fakultas->nama ?? '-' }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-mortarboard me-2 text-muted"></i>
                        {{ $dosen->programStudi->nama ?? '-' }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-briefcase me-2 text-muted"></i>
                        {{ $dosen->jabatan_fungsional ?? '-' }}
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-award me-2 text-muted"></i>
                        {{ $dosen->golongan ?? '-' }}
                    </p>
                </div>
                
                <hr>
                
                <a href="{{ route('dosen.profil.edit') }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit Profil
                </a>
            </div>
        </div>

        <!-- Card Kontak -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-telephone me-2"></i>Kontak
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="40"><i class="bi bi-envelope text-muted"></i></td>
                        <td>{{ $dosen->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-phone text-muted"></i></td>
                        <td>{{ $dosen->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-telephone text-muted"></i></td>
                        <td>{{ $dosen->telepon ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Card ID Peneliti -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-link-45deg me-2"></i>ID Peneliti
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="100">SINTA</td>
                        <td>
                            @if($dosen->sinta_id)
                            <a href="https://sinta.kemdikbud.go.id/authors/profile/{{ $dosen->sinta_id }}" target="_blank">{{ $dosen->sinta_id }}</a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Scopus</td>
                        <td>
                            @if($dosen->scopus_id)
                            <a href="https://www.scopus.com/authid/detail.uri?authorId={{ $dosen->scopus_id }}" target="_blank">{{ $dosen->scopus_id }}</a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Scholar</td>
                        <td>
                            @if($dosen->google_scholar_id)
                            <a href="https://scholar.google.com/citations?user={{ $dosen->google_scholar_id }}" target="_blank">{{ $dosen->google_scholar_id }}</a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>ORCID</td>
                        <td>
                            @if($dosen->orcid)
                            <a href="https://orcid.org/{{ $dosen->orcid }}" target="_blank">{{ $dosen->orcid }}</a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan - Detail -->
    <div class="col-lg-8">
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="profilTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="biodata-tab" data-bs-toggle="tab" data-bs-target="#biodata" type="button">
                    <i class="bi bi-person me-1"></i>Biodata
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button">
                    <i class="bi bi-mortarboard me-1"></i>Pendidikan
                    <span class="badge bg-secondary ms-1">{{ $dosen->riwayatPendidikan->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan" type="button">
                    <i class="bi bi-briefcase me-1"></i>Jabatan
                    <span class="badge bg-secondary ms-1">{{ $dosen->riwayatJabatan->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button">
                    <i class="bi bi-key me-1"></i>Password
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profilTabContent">
            <!-- Tab Biodata -->
            <div class="tab-pane fade show active" id="biodata" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Data Pribadi</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="150" class="text-muted">Tempat Lahir</td>
                                        <td>{{ $dosen->tempat_lahir ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tanggal Lahir</td>
                                        <td>{{ $dosen->tanggal_lahir ? $dosen->tanggal_lahir->format('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jenis Kelamin</td>
                                        <td>{{ $dosen->jenis_kelamin ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pendidikan</td>
                                        <td>{{ $dosen->pendidikan_terakhir ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Bidang Keahlian</td>
                                        <td>{{ $dosen->bidang_keahlian ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Alamat</h6>
                                <p class="mb-2">{{ $dosen->alamat ?? '-' }}</p>
                                @if($dosen->kelurahan || $dosen->kecamatan || $dosen->kabupaten || $dosen->provinsi)
                                <p class="text-muted small">
                                    {{ $dosen->kelurahan->nama ?? '' }}
                                    {{ $dosen->kecamatan ? ', ' . $dosen->kecamatan->nama : '' }}
                                    {{ $dosen->kabupaten ? ', ' . $dosen->kabupaten->nama : '' }}
                                    {{ $dosen->provinsi ? ', ' . $dosen->provinsi->nama : '' }}
                                    {{ $dosen->kode_pos ? ' ' . $dosen->kode_pos : '' }}
                                </p>
                                @endif
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Data Sertifikasi</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="150" class="text-muted">No. Sertifikasi</td>
                                        <td>{{ $dosen->no_sertifikasi_dosen ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tahun Sertifikasi</td>
                                        <td>{{ $dosen->tahun_sertifikasi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Reg. DIKTI</td>
                                        <td>{{ $dosen->no_registrasi_dikti ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Data Bank & BPJS</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="150" class="text-muted">NPWP</td>
                                        <td>{{ $dosen->no_npwp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Rekening</td>
                                        <td>{{ $dosen->no_rekening ?? '-' }} {{ $dosen->nama_bank ? '(' . $dosen->nama_bank . ')' : '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">BPJS Kesehatan</td>
                                        <td>{{ $dosen->no_bpjs_kesehatan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">BPJS TK</td>
                                        <td>{{ $dosen->no_bpjs_ketenagakerjaan ?? '-' }}</td>
                                    </tr>
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
                        @if($dosen->riwayatPendidikan->count() > 0)
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
                                    @foreach($dosen->riwayatPendidikan as $pend)
                                    <tr>
                                        <td><span class="badge bg-info">{{ $pend->jenjang }}</span></td>
                                        <td>{{ $pend->nama_institusi }}</td>
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
                        
                        <div class="mt-3">
                            <a href="{{ route('dosen.kepegawaian') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-folder me-1"></i>Lihat Selengkapnya di Kepegawaian
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Jabatan -->
            <div class="tab-pane fade" id="jabatan" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        @if($dosen->riwayatJabatan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jabatan Fungsional</th>
                                        <th>No. SK</th>
                                        <th>TMT</th>
                                        <th>Angka Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dosen->riwayatJabatan as $jab)
                                    <tr>
                                        <td><strong>{{ $jab->jabatan_fungsional }}</strong></td>
                                        <td>{{ $jab->no_sk }}</td>
                                        <td>{{ $jab->tmt_jabatan ? $jab->tmt_jabatan->format('d M Y') : '-' }}</td>
                                        <td>{{ $jab->angka_kredit ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-briefcase text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada data riwayat jabatan</p>
                        </div>
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ route('dosen.kepegawaian') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-folder me-1"></i>Lihat Selengkapnya di Kepegawaian
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Password -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <form action="{{ route('dosen.profil.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                        @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimal 8 karakter</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-key me-1"></i>Ubah Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
