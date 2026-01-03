@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Detail Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mahasiswa.index') }}">Mahasiswa</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <!-- Profil -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($mahasiswa->foto)
                <img src="{{ asset('storage/' . $mahasiswa->foto) }}" alt="Foto {{ $mahasiswa->nama }}" 
                     class="img-thumbnail mb-3" style="width: 150px; height: 180px; object-fit: cover;">
                @else
                <div class="user-avatar bg-primary mx-auto mb-3" style="width: 150px; height: 180px; font-size: 3rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ strtoupper(substr($mahasiswa->nama, 0, 2)) }}
                </div>
                @endif
                <h5 class="mb-1">{{ $mahasiswa->nama }}</h5>
                <p class="text-muted mb-2"><code>{{ $mahasiswa->nim }}</code></p>
                <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : ($mahasiswa->status == 'Cuti' ? 'warning' : ($mahasiswa->status == 'Lulus' ? 'info' : 'danger')) }} fs-6">
                    {{ $mahasiswa->status }}
                </span>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Statistik Akademik</div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary mb-0">{{ $ipk }}</h4>
                        <small class="text-muted">IPK</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success mb-0">{{ $totalSks }}</h4>
                        <small class="text-muted">SKS Lulus</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info mb-0">{{ $mahasiswa->semester_aktif }}</h4>
                        <small class="text-muted">Semester</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Finansial -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-credit-card me-2"></i>Data Finansial</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">No. Rekening</td>
                        <td>{{ $mahasiswa->no_rekening ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bank</td>
                        <td>{{ $mahasiswa->nama_bank ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Atas Nama</td>
                        <td>{{ $mahasiswa->atas_nama_rekening ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Penerima KIP</td>
                        <td>
                            @if($mahasiswa->penerima_kip)
                            <span class="badge bg-success">Ya</span>
                            <small class="text-muted">({{ $mahasiswa->no_kip }})</small>
                            @else
                            <span class="badge bg-secondary">Tidak</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Info Detail -->
    <div class="col-lg-8">
        <!-- Data Pribadi -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person me-2"></i>Data Pribadi</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="130" class="text-muted">Email</td>
                                <td>{{ $mahasiswa->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td>{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">TTL</td>
                                <td>{{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d M Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Agama</td>
                                <td>{{ $mahasiswa->agama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Gol. Darah</td>
                                <td>{{ $mahasiswa->golongan_darah ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="130" class="text-muted">NIK</td>
                                <td>{{ $mahasiswa->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. KK</td>
                                <td>{{ $mahasiswa->no_kk ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kewarganegaraan</td>
                                <td>{{ $mahasiswa->kewarganegaraan ?? 'WNI' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. Telepon</td>
                                <td>{{ $mahasiswa->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. HP</td>
                                <td>{{ $mahasiswa->no_hp ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr class="my-2">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="130" class="text-muted">Alamat</td>
                        <td>{{ $mahasiswa->alamat ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Data Akademik -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-mortarboard me-2"></i>Data Akademik</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="130" class="text-muted">Program Studi</td>
                                <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Fakultas</td>
                                <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Angkatan</td>
                                <td>{{ $mahasiswa->angkatan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jalur Masuk</td>
                                <td>{{ $mahasiswa->jalur_masuk ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dosen Wali</td>
                                <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Asal Sekolah</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="130" class="text-muted">Sekolah</td>
                                <td>{{ $mahasiswa->asal_sekolah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jurusan</td>
                                <td>{{ $mahasiswa->jurusan_asal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tahun Lulus</td>
                                <td>{{ $mahasiswa->tahun_lulus_sekolah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nilai UN</td>
                                <td>{{ $mahasiswa->nilai_un ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. Ijazah</td>
                                <td>{{ $mahasiswa->no_ijazah_sma ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Orang Tua -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-people me-2"></i>Data Orang Tua</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Data Ayah</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="100" class="text-muted">Nama</td>
                                <td>{{ $mahasiswa->nama_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK</td>
                                <td>{{ $mahasiswa->nik_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pekerjaan</td>
                                <td>{{ $mahasiswa->pekerjaan_ayah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pendidikan</td>
                                <td>{{ $mahasiswa->pendidikan_ayah ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Data Ibu</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="100" class="text-muted">Nama</td>
                                <td>{{ $mahasiswa->nama_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK</td>
                                <td>{{ $mahasiswa->nik_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pekerjaan</td>
                                <td>{{ $mahasiswa->pekerjaan_ibu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pendidikan</td>
                                <td>{{ $mahasiswa->pendidikan_ibu ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr class="my-2">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="130" class="text-muted">No. HP Ortu</td>
                        <td>{{ $mahasiswa->no_hp_ortu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email Ortu</td>
                        <td>{{ $mahasiswa->email_ortu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Penghasilan</td>
                        <td>{{ $mahasiswa->penghasilan_ortu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat Ortu</td>
                        <td>{{ $mahasiswa->alamat_ortu ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Data Wali -->
        @if($mahasiswa->nama_wali)
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-badge me-2"></i>Data Wali</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="130" class="text-muted">Nama Wali</td>
                        <td>{{ $mahasiswa->nama_wali }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hubungan</td>
                        <td>{{ $mahasiswa->hubungan_wali ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pekerjaan</td>
                        <td>{{ $mahasiswa->pekerjaan_wali ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $mahasiswa->no_hp_wali ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td>{{ $mahasiswa->alamat_wali ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
