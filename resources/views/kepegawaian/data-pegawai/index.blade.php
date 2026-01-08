@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Data Pegawai</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item active">Data Pegawai</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Total Pegawai</h6>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-mortarboard fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Dosen Aktif</h6>
                            <h3 class="mb-0">{{ number_format($stats['dosen_aktif']) }} <small class="text-muted fs-6">/ {{ $stats['total_dosen'] }}</small></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-badge fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Tendik Aktif</h6>
                            <h3 class="mb-0">{{ number_format($stats['tendik_aktif']) }} <small class="text-muted fs-6">/ {{ $stats['total_tendik'] }}</small></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-building fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Unit Kerja</h6>
                            <h3 class="mb-0">{{ $unitKerjaList->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs & Filter -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="pegawaiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'semua' ? 'active' : '' }}" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab">
                        <i class="bi bi-people me-1"></i> Semua
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'dosen' ? 'active' : '' }}" id="dosen-tab" data-bs-toggle="tab" data-bs-target="#dosen" type="button" role="tab">
                        <i class="bi bi-mortarboard me-1"></i> Dosen
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'tendik' ? 'active' : '' }}" id="tendik-tab" data-bs-toggle="tab" data-bs-target="#tendik" type="button" role="tab">
                        <i class="bi bi-person-badge me-1"></i> Tenaga Kependidikan
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <input type="hidden" name="tab" id="tabInput" value="{{ $tab }}">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, NIP, NIDN, email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="prodi" class="form-select">
                        <option value="">Semua Prodi</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="unit_kerja" class="form-select">
                        <option value="">Semua Unit Kerja</option>
                        @foreach($unitKerjaList as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_kerja') == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>

            <!-- Tab Content -->
            <div class="tab-content" id="pegawaiTabContent">
                <!-- Tab Semua -->
                <div class="tab-pane fade {{ $tab == 'semua' ? 'show active' : '' }}" id="semua" role="tabpanel">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover table-striped align-middle table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama</th>
                                    <th>NIP/NIDN</th>
                                    <th width="80">Jenis</th>
                                    <th>Unit/Prodi</th>
                                    <th width="70">Status</th>
                                    <th width="90">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = ($semuaPaginated->currentPage() - 1) * $semuaPaginated->perPage() + 1; @endphp
                                @forelse($semuaPaginated as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <strong>{{ $item->nama }}</strong>
                                        @if(isset($item->gelar_depan) || isset($item->gelar_belakang))
                                            <br><small class="text-muted">{{ $item->gelar_depan ?? '' }} {{ $item->gelar_belakang ?? '' }}</small>
                                        @elseif(isset($item->jabatan))
                                            <br><small class="text-muted">{{ $item->jabatan }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($item->nidn))
                                            <small>NIDN: {{ $item->nidn }}</small>
                                            @if($item->nip)<br><small class="text-muted">NIP: {{ $item->nip }}</small>@endif
                                        @else
                                            <small>NIP: {{ $item->nip ?? '-' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($item->nidn))
                                            <span class="badge bg-success">Dosen</span>
                                        @else
                                            <span class="badge bg-info">Tendik</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->programStudi->nama ?? $item->unitKerja->nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($item->status) == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($item->nidn))
                                            <a href="{{ route('kepegawaian.index', $item->hashid) }}" class="btn btn-sm btn-outline-primary" title="Kepegawaian"><i class="bi bi-folder"></i></a>
                                            <a href="{{ route('dosen.show', $item->hashid) }}" class="btn btn-sm btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                        @else
                                            <a href="{{ route('kepegawaian.pegawai.riwayat.index', $item->hashid) }}" class="btn btn-sm btn-outline-primary" title="Kepegawaian"><i class="bi bi-folder"></i></a>
                                            <a href="{{ route('kepegawaian.pegawai.show', $item->hashid) }}" class="btn btn-sm btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mb-0">Tidak ada data</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Menampilkan {{ $semuaPaginated->firstItem() ?? 0 }} - {{ $semuaPaginated->lastItem() ?? 0 }} dari {{ $semuaPaginated->total() }} data</small>
                        {{ $semuaPaginated->appends(['tab' => 'semua'])->links() }}
                    </div>
                </div>

                <!-- Tab Dosen -->
                <div class="tab-pane fade {{ $tab == 'dosen' ? 'show active' : '' }}" id="dosen" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total: {{ $dosensPaginated->total() }} dosen</span>
                        <a href="{{ route('dosen.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Dosen
                        </a>
                    </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover table-striped align-middle table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama</th>
                                    <th>NIDN</th>
                                    <th>NIP</th>
                                    <th>Program Studi</th>
                                    <th width="70">Status</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dosensPaginated as $index => $dosen)
                                <tr>
                                    <td>{{ ($dosensPaginated->currentPage() - 1) * $dosensPaginated->perPage() + $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $dosen->nama }}</strong>
                                        @if($dosen->gelar_depan || $dosen->gelar_belakang)
                                            <br><small class="text-muted">{{ $dosen->gelar_depan }} {{ $dosen->gelar_belakang }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $dosen->nidn ?? '-' }}</td>
                                    <td>{{ $dosen->nip ?? '-' }}</td>
                                    <td>{{ $dosen->programStudi->nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($dosen->status) == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ $dosen->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kepegawaian.index', $dosen->hashid) }}" class="btn btn-sm btn-outline-primary" title="Kepegawaian"><i class="bi bi-folder"></i></a>
                                        <a href="{{ route('dosen.show', $dosen->hashid) }}" class="btn btn-sm btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('dosen.edit', $dosen->hashid) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mb-0">Tidak ada data dosen</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Menampilkan {{ $dosensPaginated->firstItem() ?? 0 }} - {{ $dosensPaginated->lastItem() ?? 0 }} dari {{ $dosensPaginated->total() }} data</small>
                        {{ $dosensPaginated->appends(['tab' => 'dosen'])->links() }}
                    </div>
                </div>

                <!-- Tab Tendik -->
                <div class="tab-pane fade {{ $tab == 'tendik' ? 'show active' : '' }}" id="tendik" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total: {{ $pegawaisPaginated->total() }} tenaga kependidikan</span>
                        <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Tendik
                        </a>
                    </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover table-striped align-middle table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Jabatan</th>
                                    <th>Unit Kerja</th>
                                    <th width="70">Status</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pegawaisPaginated as $index => $pegawai)
                                <tr>
                                    <td>{{ ($pegawaisPaginated->currentPage() - 1) * $pegawaisPaginated->perPage() + $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $pegawai->nama }}</strong>
                                        @if($pegawai->email)
                                            <br><small class="text-muted">{{ $pegawai->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $pegawai->nip ?? '-' }}</td>
                                    <td>{{ $pegawai->jabatan ?? '-' }}</td>
                                    <td>{{ $pegawai->unitKerja->nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($pegawai->status) == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($pegawai->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai->hashid) }}" class="btn btn-sm btn-outline-primary" title="Kepegawaian"><i class="bi bi-folder"></i></a>
                                        <a href="{{ route('kepegawaian.pegawai.show', $pegawai->hashid) }}" class="btn btn-sm btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('kepegawaian.pegawai.edit', $pegawai->hashid) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mb-0">Tidak ada data tenaga kependidikan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Menampilkan {{ $pegawaisPaginated->firstItem() ?? 0 }} - {{ $pegawaisPaginated->lastItem() ?? 0 }} dari {{ $pegawaisPaginated->total() }} data</small>
                        {{ $pegawaisPaginated->appends(['tab' => 'tendik'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update hidden tab input when tab changes
document.querySelectorAll('#pegawaiTab button').forEach(btn => {
    btn.addEventListener('shown.bs.tab', function(event) {
        const tabId = event.target.getAttribute('data-bs-target').replace('#', '');
        document.getElementById('tabInput').value = tabId;
    });
});
</script>
@endpush
@endsection
