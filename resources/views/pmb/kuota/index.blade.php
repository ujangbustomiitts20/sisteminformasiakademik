@extends('layouts.app')

@section('title', 'Kuota PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kuota PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Kuota</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kuota
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalGenerate">
                <i class="bi bi-lightning me-1"></i> Generate Batch
            </button>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCopy">
                <i class="bi bi-copy me-1"></i> Copy dari Gelombang
            </button>
        </div>
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
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Gelombang</label>
                    <select name="gelombang" class="form-select">
                        <option value="">-- Semua Gelombang --</option>
                        @foreach($gelombangs as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang') == $g->id ? 'selected' : '' }}>
                                {{ $g->periodePmb->nama ?? '-' }} - Gel. {{ $g->nomor_gelombang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Program Studi</label>
                    <select name="prodi" class="form-select">
                        <option value="">-- Semua Prodi --</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jalur Seleksi</label>
                    <select name="jalur" class="form-select">
                        <option value="">-- Semua Jalur --</option>
                        @foreach($jalurs as $jalur)
                            <option value="{{ $jalur->id }}" {{ request('jalur') == $jalur->id ? 'selected' : '' }}>
                                {{ $jalur->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('pmb.kuota.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gelombang</th>
                            <th>Program Studi</th>
                            <th>Jalur Seleksi</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Terisi</th>
                            <th class="text-center">Sisa</th>
                            <th class="text-center">Passing Grade</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kuotaList as $index => $kuota)
                            <tr>
                                <td>{{ $kuotaList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-medium">{{ $kuota->gelombangPmb->periodePmb->nama ?? '-' }}</span><br>
                                    <small class="text-muted">Gelombang {{ $kuota->gelombangPmb->nomor_gelombang ?? '-' }}</small>
                                </td>
                                <td>{{ $kuota->programStudi->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $kuota->jalurSeleksi->nama ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $kuota->kuota }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $kuota->terisi > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $kuota->terisi }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php $sisa = $kuota->kuota - $kuota->terisi; @endphp
                                    <span class="badge {{ $sisa > 0 ? 'bg-primary' : 'bg-danger' }}">
                                        {{ $sisa }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ $kuota->passing_grade ? number_format($kuota->passing_grade, 1) : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pmb.kuota.edit', $kuota->hashid) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('pmb.kuota.destroy', $kuota->hashid) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus kuota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                {{ $kuota->terisi > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data kuota.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $kuotaList->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pmb.kuota.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kuota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Gelombang <span class="text-danger">*</span></label>
                        <select name="gelombang_pmb_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">
                                    {{ $g->periodePmb->nama ?? '-' }} - Gel. {{ $g->nomor_gelombang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                        <select name="jalur_seleksi_id" class="form-select" required>
                            <option value="">-- Pilih Jalur Seleksi --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota <span class="text-danger">*</span></label>
                            <input type="number" name="kuota" class="form-control" min="0" value="30" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Grade</label>
                            <input type="number" name="passing_grade" class="form-control" 
                                   min="0" max="100" step="0.1" placeholder="0 - 100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Generate Batch -->
<div class="modal fade" id="modalGenerate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('pmb.kuota.generate-batch') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Kuota Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Generate kuota untuk semua kombinasi Prodi × Jalur yang dipilih.
                        Kombinasi yang sudah ada akan dilewati.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gelombang Target <span class="text-danger">*</span></label>
                        <select name="gelombang_pmb_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">
                                    {{ $g->periodePmb->nama ?? '-' }} - Gel. {{ $g->nomor_gelombang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota Default <span class="text-danger">*</span></label>
                            <input type="number" name="kuota_default" class="form-control" 
                                   min="1" value="30" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Grade Default</label>
                            <input type="number" name="passing_grade_default" class="form-control" 
                                   min="0" max="100" step="0.1" placeholder="Opsional">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="checkAllProdi">
                                    <label class="form-check-label fw-bold" for="checkAllProdi">Pilih Semua</label>
                                </div>
                                <hr class="my-2">
                                @foreach($prodis as $prodi)
                                    <div class="form-check">
                                        <input class="form-check-input prodi-check" type="checkbox" 
                                               name="program_studi_ids[]" value="{{ $prodi->id }}"
                                               id="prodi{{ $prodi->id }}">
                                        <label class="form-check-label" for="prodi{{ $prodi->id }}">
                                            {{ $prodi->nama }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="checkAllJalur">
                                    <label class="form-check-label fw-bold" for="checkAllJalur">Pilih Semua</label>
                                </div>
                                <hr class="my-2">
                                @foreach($jalurs as $jalur)
                                    <div class="form-check">
                                        <input class="form-check-input jalur-check" type="checkbox" 
                                               name="jalur_seleksi_ids[]" value="{{ $jalur->id }}"
                                               id="jalur{{ $jalur->id }}">
                                        <label class="form-check-label" for="jalur{{ $jalur->id }}">
                                            {{ $jalur->nama }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Kuota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Copy dari Gelombang -->
<div class="modal fade" id="modalCopy" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pmb.kuota.copy-gelombang') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Copy Kuota dari Gelombang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Salin seluruh setting kuota dari gelombang sumber ke gelombang target.
                        Kuota yang sudah ada di target akan dilewati.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gelombang Sumber <span class="text-danger">*</span></label>
                        <select name="source_gelombang_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang Sumber --</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">
                                    {{ $g->periodePmb->nama ?? '-' }} - Gel. {{ $g->nomor_gelombang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gelombang Target <span class="text-danger">*</span></label>
                        <select name="target_gelombang_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang Target --</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">
                                    {{ $g->periodePmb->nama ?? '-' }} - Gel. {{ $g->nomor_gelombang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Copy Kuota</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check All Prodi
    document.getElementById('checkAllProdi').addEventListener('change', function() {
        document.querySelectorAll('.prodi-check').forEach(cb => cb.checked = this.checked);
    });

    // Check All Jalur
    document.getElementById('checkAllJalur').addEventListener('change', function() {
        document.querySelectorAll('.jalur-check').forEach(cb => cb.checked = this.checked);
    });
});
</script>
@endpush
