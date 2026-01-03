@extends('layouts.app')

@section('title', 'Kepegawaian - ' . $pegawai->nama)

@section('content')
<div class="page-title">
    <h4>Data Kepegawaian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.index') }}">Pegawai</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}">{{ $pegawai->nama }}</a></li>
            <li class="breadcrumb-item active">Riwayat</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if($pegawai->foto)
                    <img src="{{ asset('storage/'.$pegawai->foto) }}" alt="Foto" class="rounded-circle me-3" width="80" height="80" style="object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person text-white" style="font-size: 2rem;"></i>
                    </div>
                    @endif
                    <div>
                        <h5 class="mb-1">{{ $pegawai->nama }}</h5>
                        <p class="text-muted mb-0">NIP: {{ $pegawai->nip ?? '-' }}</p>
                        <p class="text-muted mb-0">{{ $pegawai->jabatan ?? '-' }} - {{ $pegawai->unitKerja?->nama ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="kepegawaianTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button">
            <i class="bi bi-mortarboard me-1"></i>Pendidikan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan" type="button">
            <i class="bi bi-briefcase me-1"></i>Jabatan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pangkat-tab" data-bs-toggle="tab" data-bs-target="#pangkat" type="button">
            <i class="bi bi-award me-1"></i>Pangkat
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pelatihan-tab" data-bs-toggle="tab" data-bs-target="#pelatihan" type="button">
            <i class="bi bi-journal-bookmark me-1"></i>Pelatihan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" type="button">
            <i class="bi bi-file-earmark me-1"></i>Dokumen
        </button>
    </li>
</ul>

<div class="tab-content" id="kepegawaianTabContent">
    {{-- Tab Riwayat Pendidikan --}}
    <div class="tab-pane fade show active" id="pendidikan" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Pendidikan</span>
                <a href="{{ route('kepegawaian.pegawai.riwayat.pendidikan.create', $pegawai) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenjang</th>
                                <th>Institusi</th>
                                <th>Jurusan</th>
                                <th>Tahun Lulus</th>
                                <th>No. Ijazah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai->riwayatPendidikan as $p)
                            <tr>
                                <td><span class="badge bg-info">{{ $p->jenjang }}</span></td>
                                <td>{{ $p->nama_institusi }}</td>
                                <td>{{ $p->jurusan ?? '-' }}</td>
                                <td>{{ $p->tahun_lulus ?? '-' }}</td>
                                <td>{{ $p->no_ijazah ?? '-' }}</td>
                                <td>
                                    @if($p->file_ijazah)
                                    <a href="{{ asset('storage/'.$p->file_ijazah) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat File">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('kepegawaian.pegawai.riwayat.pendidikan.edit', [$pegawai, $p]) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pegawai.riwayat.pendidikan.destroy', [$pegawai, $p]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Belum ada data pendidikan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Riwayat Jabatan --}}
    <div class="tab-pane fade" id="jabatan" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Jabatan</span>
                <a href="{{ route('kepegawaian.pegawai.riwayat.jabatan.create', $pegawai) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jabatan</th>
                                <th>Jenis</th>
                                <th>Unit Kerja</th>
                                <th>TMT Jabatan</th>
                                <th>TMT Selesai</th>
                                <th>No. SK</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai->riwayatJabatan as $j)
                            <tr>
                                <td>{{ $j->nama_jabatan }}</td>
                                <td><span class="badge bg-secondary">{{ $j->jenis_jabatan }}</span></td>
                                <td>{{ $j->unit_kerja ?? '-' }}</td>
                                <td>{{ $j->tmt_jabatan ? \Carbon\Carbon::parse($j->tmt_jabatan)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $j->tmt_selesai ? \Carbon\Carbon::parse($j->tmt_selesai)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $j->no_sk ?? '-' }}</td>
                                <td>
                                    @if($j->file_sk)
                                    <a href="{{ asset('storage/'.$j->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('kepegawaian.pegawai.riwayat.jabatan.edit', [$pegawai, $j]) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pegawai.riwayat.jabatan.destroy', [$pegawai, $j]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Belum ada data jabatan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Riwayat Pangkat --}}
    <div class="tab-pane fade" id="pangkat" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Pangkat/Golongan</span>
                <a href="{{ route('kepegawaian.pegawai.riwayat.pangkat.create', $pegawai) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Golongan</th>
                                <th>Pangkat</th>
                                <th>TMT Pangkat</th>
                                <th>Masa Kerja</th>
                                <th>No. SK</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai->riwayatPangkat as $pk)
                            <tr>
                                <td><span class="badge bg-primary">{{ $pk->golongan }}</span></td>
                                <td>{{ $pk->pangkat }}</td>
                                <td>{{ $pk->tmt_pangkat ? \Carbon\Carbon::parse($pk->tmt_pangkat)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $pk->masa_kerja_tahun ?? 0 }} tahun {{ $pk->masa_kerja_bulan ?? 0 }} bulan</td>
                                <td>{{ $pk->no_sk ?? '-' }}</td>
                                <td>
                                    @if($pk->file_sk)
                                    <a href="{{ asset('storage/'.$pk->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('kepegawaian.pegawai.riwayat.pangkat.edit', [$pegawai, $pk]) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pegawai.riwayat.pangkat.destroy', [$pegawai, $pk]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Belum ada data pangkat</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Riwayat Pelatihan --}}
    <div class="tab-pane fade" id="pelatihan" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Pelatihan/Diklat</span>
                <a href="{{ route('kepegawaian.pegawai.riwayat.pelatihan.create', $pegawai) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pelatihan</th>
                                <th>Jenis</th>
                                <th>Penyelenggara</th>
                                <th>Tahun</th>
                                <th>Jumlah Jam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai->riwayatPelatihan as $pl)
                            <tr>
                                <td>{{ $pl->nama_pelatihan }}</td>
                                <td><span class="badge bg-success">{{ $pl->jenis_pelatihan }}</span></td>
                                <td>{{ $pl->penyelenggara ?? '-' }}</td>
                                <td>{{ $pl->tahun ?? '-' }}</td>
                                <td>{{ $pl->jumlah_jam ?? '-' }} jam</td>
                                <td>
                                    @if($pl->file_sertifikat)
                                    <a href="{{ asset('storage/'.$pl->file_sertifikat) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('kepegawaian.pegawai.riwayat.pelatihan.edit', [$pegawai, $pl]) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pegawai.riwayat.pelatihan.destroy', [$pegawai, $pl]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Belum ada data pelatihan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Dokumen --}}
    <div class="tab-pane fade" id="dokumen" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Dokumen Kepegawaian</span>
                <a href="{{ route('kepegawaian.pegawai.riwayat.dokumen.create', $pegawai) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Nama Dokumen</th>
                                <th>Nomor</th>
                                <th>Tanggal Terbit</th>
                                <th>Berlaku s/d</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawai->dokumenKepegawaian as $d)
                            <tr>
                                <td><span class="badge bg-dark">{{ str_replace('_', ' ', $d->jenis_dokumen) }}</span></td>
                                <td>{{ $d->nama_dokumen }}</td>
                                <td>{{ $d->nomor_dokumen ?? '-' }}</td>
                                <td>{{ $d->tanggal_terbit ? \Carbon\Carbon::parse($d->tanggal_terbit)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($d->tanggal_expired)
                                        @if(\Carbon\Carbon::parse($d->tanggal_expired)->isPast())
                                        <span class="text-danger">{{ \Carbon\Carbon::parse($d->tanggal_expired)->format('d/m/Y') }} (Expired)</span>
                                        @else
                                        {{ \Carbon\Carbon::parse($d->tanggal_expired)->format('d/m/Y') }}
                                        @endif
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($d->file_dokumen)
                                    <a href="{{ asset('storage/'.$d->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('kepegawaian.pegawai.riwayat.dokumen.edit', [$pegawai, $d]) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pegawai.riwayat.dokumen.destroy', [$pegawai, $d]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Belum ada dokumen</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Pegawai
    </a>
</div>
@endsection
