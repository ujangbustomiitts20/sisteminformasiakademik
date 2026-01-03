@extends('layouts.app')

@section('title', 'Data Kepegawaian - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Data Kepegawaian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.show', $dosen) }}">{{ $dosen->nama }}</a></li>
            <li class="breadcrumb-item active">Kepegawaian</li>
        </ol>
    </nav>
</div>

<!-- Info Dosen -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                @if($dosen->foto)
                <img src="{{ Storage::url($dosen->foto) }}" alt="{{ $dosen->nama }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                @else
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                </div>
                @endif
            </div>
            <div class="col">
                <h5 class="mb-1">{{ $dosen->gelar_depan }} {{ $dosen->nama }}{{ $dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '' }}</h5>
                <p class="text-muted mb-0">
                    <code>{{ $dosen->nidn }}</code> &bull; 
                    {{ $dosen->programStudi->nama ?? '-' }} &bull;
                    {{ $dosen->jabatan_fungsional ?? '-' }} &bull;
                    {{ $dosen->golongan ?? '-' }}
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('dosen.show', $dosen) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Profil
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Nav Tabs -->
<ul class="nav nav-tabs" id="kepegawaianTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button">
            <i class="bi bi-mortarboard me-1"></i>Pendidikan
            <span class="badge bg-primary ms-1">{{ $dosen->riwayatPendidikan->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan" type="button">
            <i class="bi bi-briefcase me-1"></i>Jabatan
            <span class="badge bg-primary ms-1">{{ $dosen->riwayatJabatan->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pangkat-tab" data-bs-toggle="tab" data-bs-target="#pangkat" type="button">
            <i class="bi bi-award me-1"></i>Pangkat
            <span class="badge bg-primary ms-1">{{ $dosen->riwayatPangkat->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pelatihan-tab" data-bs-toggle="tab" data-bs-target="#pelatihan" type="button">
            <i class="bi bi-journal-check me-1"></i>Pelatihan
            <span class="badge bg-primary ms-1">{{ $dosen->riwayatPelatihan->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" type="button">
            <i class="bi bi-file-earmark-text me-1"></i>Dokumen
            <span class="badge bg-primary ms-1">{{ $dosen->dokumenKepegawaian->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="kepegawaianTabContent">
    <!-- Tab Pendidikan -->
    <div class="tab-pane fade show active" id="pendidikan" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-mortarboard me-2"></i>Riwayat Pendidikan</span>
                <a href="{{ route('kepegawaian.pendidikan.create', $dosen) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            </div>
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
                                <th>Dokumen</th>
                                <th width="100">Aksi</th>
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
                                <td>
                                    @if($pend->file_ijazah)
                                    <a href="{{ Storage::url($pend->file_ijazah) }}" target="_blank" class="btn btn-outline-info btn-sm" title="Ijazah">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                    @if($pend->file_transkrip)
                                    <a href="{{ Storage::url($pend->file_transkrip) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Transkrip">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kepegawaian.pendidikan.edit', [$dosen, $pend]) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pendidikan.destroy', [$dosen, $pend]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-mortarboard text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada data riwayat pendidikan</p>
                    <a href="{{ route('kepegawaian.pendidikan.create', $dosen) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Pendidikan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Jabatan -->
    <div class="tab-pane fade" id="jabatan" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-briefcase me-2"></i>Riwayat Jabatan Fungsional</span>
                <a href="{{ route('kepegawaian.jabatan.create', $dosen) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            </div>
            <div class="card-body">
                @if($dosen->riwayatJabatan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jabatan</th>
                                <th>No. SK</th>
                                <th>TMT</th>
                                <th>Angka Kredit</th>
                                <th>Dokumen</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosen->riwayatJabatan as $jab)
                            <tr>
                                <td><strong>{{ $jab->jabatan_fungsional }}</strong></td>
                                <td>{{ $jab->no_sk }}</td>
                                <td>{{ $jab->tmt_jabatan->format('d M Y') }}</td>
                                <td>{{ $jab->angka_kredit ?? '-' }}</td>
                                <td>
                                    @if($jab->file_sk)
                                    <a href="{{ Storage::url($jab->file_sk) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kepegawaian.jabatan.edit', [$dosen, $jab]) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.jabatan.destroy', [$dosen, $jab]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-briefcase text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada data riwayat jabatan</p>
                    <a href="{{ route('kepegawaian.jabatan.create', $dosen) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Jabatan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Pangkat -->
    <div class="tab-pane fade" id="pangkat" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-award me-2"></i>Riwayat Pangkat/Golongan</span>
                <a href="{{ route('kepegawaian.pangkat.create', $dosen) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            </div>
            <div class="card-body">
                @if($dosen->riwayatPangkat->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Golongan</th>
                                <th>Pangkat</th>
                                <th>No. SK</th>
                                <th>TMT</th>
                                <th>Masa Kerja</th>
                                <th>Dokumen</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosen->riwayatPangkat as $pkt)
                            <tr>
                                <td><span class="badge bg-success">{{ $pkt->golongan }}</span></td>
                                <td>{{ $pkt->pangkat }}</td>
                                <td>{{ $pkt->no_sk }}</td>
                                <td>{{ $pkt->tmt_pangkat->format('d M Y') }}</td>
                                <td>{{ $pkt->masa_kerja_tahun ?? 0 }} Th {{ $pkt->masa_kerja_bulan ?? 0 }} Bl</td>
                                <td>
                                    @if($pkt->file_sk)
                                    <a href="{{ Storage::url($pkt->file_sk) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kepegawaian.pangkat.edit', [$dosen, $pkt]) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pangkat.destroy', [$dosen, $pkt]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-award text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada data riwayat pangkat</p>
                    <a href="{{ route('kepegawaian.pangkat.create', $dosen) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Pangkat
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Pelatihan -->
    <div class="tab-pane fade" id="pelatihan" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-check me-2"></i>Riwayat Pelatihan/Diklat</span>
                <a href="{{ route('kepegawaian.pelatihan.create', $dosen) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            </div>
            <div class="card-body">
                @if($dosen->riwayatPelatihan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Nama Pelatihan</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Sertifikat</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosen->riwayatPelatihan as $plt)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $plt->jenis }}</span></td>
                                <td>{{ $plt->nama_pelatihan }}</td>
                                <td>{{ $plt->penyelenggara }}</td>
                                <td>{{ $plt->tanggal_mulai->format('d M Y') }}</td>
                                <td>{{ $plt->jumlah_jam ?? '-' }} JP</td>
                                <td>
                                    @if($plt->file_sertifikat)
                                    <a href="{{ Storage::url($plt->file_sertifikat) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kepegawaian.pelatihan.edit', [$dosen, $plt]) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.pelatihan.destroy', [$dosen, $plt]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-journal-check text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada data riwayat pelatihan</p>
                    <a href="{{ route('kepegawaian.pelatihan.create', $dosen) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Pelatihan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Dokumen -->
    <div class="tab-pane fade" id="dokumen" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text me-2"></i>Dokumen Kepegawaian</span>
                <a href="{{ route('kepegawaian.dokumen.create', $dosen) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            </div>
            <div class="card-body">
                @if($dosen->dokumenKepegawaian->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Nama Dokumen</th>
                                <th>No. Dokumen</th>
                                <th>Tgl. Terbit</th>
                                <th>Masa Berlaku</th>
                                <th>File</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosen->dokumenKepegawaian as $dok)
                            <tr>
                                <td><span class="badge bg-dark">{{ $dok->jenis_dokumen }}</span></td>
                                <td>{{ $dok->nama_dokumen }}</td>
                                <td>{{ $dok->no_dokumen ?? '-' }}</td>
                                <td>{{ $dok->tanggal_terbit ? $dok->tanggal_terbit->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($dok->tanggal_berlaku)
                                        @if($dok->isValid())
                                        <span class="badge bg-success">s/d {{ $dok->tanggal_berlaku->format('d M Y') }}</span>
                                        @else
                                        <span class="badge bg-danger">Expired {{ $dok->tanggal_berlaku->format('d M Y') }}</span>
                                        @endif
                                    @else
                                    <span class="badge bg-secondary">Seumur Hidup</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ Storage::url($dok->file_dokumen) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('kepegawaian.dokumen.edit', [$dosen, $dok]) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.dokumen.destroy', [$dosen, $dok]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
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
                    <a href="{{ route('kepegawaian.dokumen.create', $dosen) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Dokumen
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Restore active tab from URL hash
    document.addEventListener('DOMContentLoaded', function() {
        var hash = window.location.hash;
        if (hash) {
            var tab = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }
        
        // Update URL hash when tab changes
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function(e) {
                history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
            });
        });
    });
</script>
@endpush
