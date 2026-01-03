@extends('layouts.app')

@section('title', 'Detail Dosen')

@section('content')
<div class="page-title">
    <h4>Detail Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active">{{ $dosen->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($dosen->foto)
                    <img src="{{ Storage::url($dosen->foto) }}" alt="{{ $dosen->nama }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                    </div>
                    @endif
                </div>
                <h5 class="mb-1">
                    {{ $dosen->gelar_depan }} {{ $dosen->nama }}{{ $dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '' }}
                </h5>
                <p class="text-muted mb-2"><code>{{ $dosen->nidn }}</code></p>
                @if($dosen->status == 'Aktif')
                <span class="badge bg-success">Aktif</span>
                @elseif($dosen->status == 'Cuti')
                <span class="badge bg-warning">Cuti</span>
                @else
                <span class="badge bg-danger">Non-Aktif</span>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-telephone me-2"></i>Informasi Kontak
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <i class="bi bi-envelope me-2 text-muted"></i>
                    {{ $dosen->email ?? '-' }}
                </p>
                <p class="mb-2">
                    <i class="bi bi-telephone me-2 text-muted"></i>
                    {{ $dosen->telepon ?? '-' }}
                </p>
                <p class="mb-2">
                    <i class="bi bi-phone me-2 text-muted"></i>
                    {{ $dosen->no_hp ?? '-' }}
                </p>
                <hr>
                <p class="mb-2">
                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                    {{ $dosen->alamat ?? '-' }}
                </p>
                @if($dosen->kelurahan || $dosen->kecamatan || $dosen->kabupaten || $dosen->provinsi)
                <p class="mb-2 small text-muted">
                    @if($dosen->rt || $dosen->rw)RT {{ $dosen->rt ?? '-' }} / RW {{ $dosen->rw ?? '-' }}, @endif
                    {{ $dosen->kelurahan->nama ?? '' }}
                    {{ $dosen->kecamatan ? ', ' . $dosen->kecamatan->nama : '' }}
                    {{ $dosen->kabupaten ? ', ' . $dosen->kabupaten->nama : '' }}
                    {{ $dosen->provinsi ? ', ' . $dosen->provinsi->nama : '' }}
                    {{ $dosen->kode_pos ? ' - ' . $dosen->kode_pos : '' }}
                </p>
                @endif
            </div>
        </div>
        
        <!-- Data Finansial -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-bank me-2"></i>Data Finansial
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="100">NPWP</td>
                        <td>: {{ $dosen->no_npwp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bank</td>
                        <td>: {{ $dosen->nama_bank ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Rek</td>
                        <td>: {{ $dosen->no_rekening ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Atas Nama</td>
                        <td>: {{ $dosen->atas_nama_rekening ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">BPJS Kes</td>
                        <td>: {{ $dosen->no_bpjs_kesehatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">BPJS TK</td>
                        <td>: {{ $dosen->no_bpjs_ketenagakerjaan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Informasi Akademik
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150" class="text-muted">NIDN</td>
                                <td>: <code>{{ $dosen->nidn }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama Lengkap</td>
                                <td>: {{ $dosen->nama }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Gelar</td>
                                <td>: {{ $dosen->gelar_depan ?? '-' }} / {{ $dosen->gelar_belakang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td>: {{ $dosen->jenis_kelamin == 'L' ? 'Laki-laki' : ($dosen->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">TTL</td>
                                <td>: {{ $dosen->tempat_lahir ?? '-' }}, {{ $dosen->tanggal_lahir ? $dosen->tanggal_lahir->format('d M Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pendidikan</td>
                                <td>: {{ $dosen->pendidikan_terakhir ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150" class="text-muted">Program Studi</td>
                                <td>: {{ $dosen->programStudi->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jabatan</td>
                                <td>: {{ $dosen->jabatan_fungsional ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Golongan</td>
                                <td>: {{ $dosen->golongan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Bidang Keahlian</td>
                                <td>: {{ $dosen->bidang_keahlian ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Rumpun Ilmu</td>
                                <td>: {{ $dosen->rumpun_ilmu ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Sertifikasi -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-award me-2"></i>Data Sertifikasi & Publikasi
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150" class="text-muted">No. Serdos</td>
                                <td>: {{ $dosen->no_sertifikasi_dosen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tahun Serdos</td>
                                <td>: {{ $dosen->tahun_sertifikasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Reg. DIKTI</td>
                                <td>: {{ $dosen->no_registrasi_dikti ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150" class="text-muted">SINTA ID</td>
                                <td>: {{ $dosen->sinta_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Scopus ID</td>
                                <td>: {{ $dosen->scopus_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Google Scholar</td>
                                <td>: {{ $dosen->google_scholar_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">ORCID</td>
                                <td>: {{ $dosen->orcid ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mata Kuliah yang Diampu -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-book me-2"></i>Mata Kuliah yang Diampu
            </div>
            <div class="card-body">
                @if($dosen->jadwalKuliah && $dosen->jadwalKuliah->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Kelas</th>
                                <th>Jadwal</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosen->jadwalKuliah as $jk)
                            <tr>
                                <td>
                                    <strong>{{ $jk->mataKuliah->nama }}</strong>
                                    <br><small class="text-muted">{{ $jk->mataKuliah->kode }}</small>
                                </td>
                                <td>{{ $jk->mataKuliah->sks }}</td>
                                <td>{{ $jk->kelas }}</td>
                                <td>{{ $jk->hari }}, {{ \Carbon\Carbon::parse($jk->jam_mulai)->format('H:i') }}</td>
                                <td>{{ $jk->tahunAkademik->tahun ?? '-' }} {{ $jk->tahunAkademik->semester ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-book text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada mata kuliah yang diampu</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Mahasiswa Perwalian -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Mahasiswa Perwalian
            </div>
            <div class="card-body">
                @if($dosen->mahasiswaWali && $dosen->mahasiswaWali->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosen->mahasiswaWali as $mhs)
                            <tr>
                                <td><code>{{ $mhs->nim }}</code></td>
                                <td>{{ $mhs->nama }}</td>
                                <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : 'secondary' }}">{{ $mhs->status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada mahasiswa perwalian</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kepegawaian.index', $dosen) }}" class="btn btn-primary">
        <i class="bi bi-person-badge me-1"></i>Data Kepegawaian
    </a>
    <a href="{{ route('dosen.edit', $dosen) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
@endsection
