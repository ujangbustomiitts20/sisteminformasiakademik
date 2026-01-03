@extends('layouts.app')

@section('title', 'Calon Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Calon Mahasiswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Calon Mahasiswa</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.calon-mahasiswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pendaftar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pmb.calon-mahasiswa.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Gelombang</label>
                        <select name="gelombang" class="form-select">
                            <option value="">-- Semua Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ request('gelombang') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jalur</label>
                        <select name="jalur" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}" {{ request('jalur') == $jalur->id ? 'selected' : '' }}>
                                    {{ $jalur->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="mendaftar" {{ request('status') == 'mendaftar' ? 'selected' : '' }}>Mendaftar</option>
                            <option value="menunggu_bayar" {{ request('status') == 'menunggu_bayar' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="terdaftar" {{ request('status') == 'terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                            <option value="verifikasi_dokumen" {{ request('status') == 'verifikasi_dokumen' ? 'selected' : '' }}>Verifikasi Dokumen</option>
                            <option value="lulus_administrasi" {{ request('status') == 'lulus_administrasi' ? 'selected' : '' }}>Lulus Administrasi</option>
                            <option value="mengikuti_ujian" {{ request('status') == 'mengikuti_ujian' ? 'selected' : '' }}>Mengikuti Ujian</option>
                            <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus Seleksi</option>
                            <option value="tidak_lulus" {{ request('status') == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                            <option value="daftar_ulang" {{ request('status') == 'daftar_ulang' ? 'selected' : '' }}>Proses Daftar Ulang</option>
                            <option value="menjadi_mahasiswa" {{ request('status') == 'menjadi_mahasiswa' ? 'selected' : '' }}>Menjadi Mahasiswa</option>
                            <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nama / No Pendaftaran / Email" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th>Jalur</th>
                            <th>Program Studi</th>
                            <th class="text-center">Dokumen</th>
                            <th class="text-center">Bayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calonMahasiswas as $camaba)
                        <tr>
                            <td>
                                <a href="{{ route('pmb.calon-mahasiswa.show', $camaba->hashid) }}">
                                    <strong>{{ $camaba->no_pendaftaran }}</strong>
                                </a>
                            </td>
                            <td>
                                {{ $camaba->nama_lengkap }}
                                <br><small class="text-muted">{{ $camaba->email }}</small>
                            </td>
                            <td>{{ $camaba->jalurSeleksi->nama ?? 'N/A' }}</td>
                            <td>{{ $camaba->programStudi->nama ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($camaba->is_dokumen_lengkap)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-clock"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($camaba->is_bayar_pendaftaran)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-clock"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $camaba->status_badge }}">{{ $camaba->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.calon-mahasiswa.show', $camaba->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pmb.calon-mahasiswa.edit', $camaba->hashid) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('pmb.calon-mahasiswa.verifikasi-dokumen', $camaba->hashid) }}" class="btn btn-sm btn-secondary" title="Verifikasi Dokumen">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </a>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Update Status">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><h6 class="dropdown-header">Update Status</h6></li>
                                            @if($camaba->status_pendaftaran == 'draft')
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="menunggu_bayar">
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-credit-card me-2"></i>Set Menunggu Bayar</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['menunggu_bayar', 'draft']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="terdaftar">
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-check-circle me-2"></i>Set Terdaftar</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['terdaftar']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="verifikasi_dokumen">
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-file-earmark-check me-2"></i>Set Verifikasi Dokumen</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['terdaftar', 'verifikasi_dokumen']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="lulus_administrasi">
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-patch-check me-2"></i>Set Lulus Administrasi</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['lulus_administrasi']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="mengikuti_ujian">
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-journal-text me-2"></i>Set Mengikuti Ujian</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['mengikuti_ujian']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="lulus">
                                                    <button type="submit" class="dropdown-item text-success"><i class="bi bi-trophy me-2"></i>Set Lulus Seleksi</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="tidak_lulus">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-2"></i>Set Tidak Lulus</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['lulus']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="daftar_ulang">
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-arrow-clockwise me-2"></i>Set Daftar Ulang</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if(in_array($camaba->status_pendaftaran, ['daftar_ulang']))
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="menjadi_mahasiswa">
                                                    <button type="submit" class="dropdown-item text-primary"><i class="bi bi-mortarboard me-2"></i>Set Menjadi Mahasiswa</button>
                                                </form>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('pmb.calon-mahasiswa.update-status', $camaba->hashid) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="batal">
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin ingin membatalkan pendaftaran ini?')"><i class="bi bi-ban me-2"></i>Batalkan</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    <form action="{{ route('pmb.calon-mahasiswa.destroy', $camaba->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data calon mahasiswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">Menampilkan {{ $calonMahasiswas->firstItem() ?? 0 }} - {{ $calonMahasiswas->lastItem() ?? 0 }} dari {{ $calonMahasiswas->total() }} data</small>
                </div>
                {{ $calonMahasiswas->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
