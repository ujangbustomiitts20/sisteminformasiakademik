@extends('layouts.app')

@section('title', 'Biaya Pendaftaran PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Biaya Pendaftaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Biaya Pendaftaran</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Biaya
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalGenerate">
                <i class="bi bi-lightning-fill me-1"></i> Generate Massal
            </button>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalCopy">
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
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Gelombang PMB</label>
                    <select name="gelombang" class="form-select">
                        <option value="">-- Semua Gelombang --</option>
                        @foreach($gelombangs as $gel)
                            <option value="{{ $gel->id }}" {{ request('gelombang') == $gel->id ? 'selected' : '' }}>
                                {{ $gel->periodePmb->nama ?? '' }} - {{ $gel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('pmb.biaya-pendaftaran.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Gelombang</th>
                            <th>Jalur Seleksi</th>
                            <th>Program Studi</th>
                            <th class="text-end">Biaya Formulir</th>
                            <th class="text-end">Biaya Ujian</th>
                            <th class="text-end">Total Biaya</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($biayaList as $biaya)
                        <tr>
                            <td>
                                <small class="text-muted d-block">{{ $biaya->gelombangPmb->periodePmb->nama ?? '' }}</small>
                                {{ $biaya->gelombangPmb->nama ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $biaya->jalurSeleksi->kode ?? '' }}</span>
                                {{ $biaya->jalurSeleksi->nama ?? 'N/A' }}
                            </td>
                            <td>
                                @if($biaya->programStudi)
                                    {{ $biaya->programStudi->nama }}
                                @else
                                    <span class="text-muted fst-italic">Semua Prodi (Umum)</span>
                                @endif
                            </td>
                            <td class="text-end">{{ format_rupiah($biaya->biaya_formulir) }}</td>
                            <td class="text-end">{{ format_rupiah($biaya->biaya_ujian) }}</td>
                            <td class="text-end fw-bold text-primary">{{ format_rupiah($biaya->total_biaya) }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.biaya-pendaftaran.edit', $biaya->hashid) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('pmb.biaya-pendaftaran.destroy', $biaya->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus biaya ini?')">
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data biaya pendaftaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $biayaList->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pmb.biaya-pendaftaran.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Biaya Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Gelombang PMB <span class="text-danger">*</span></label>
                        <select name="gelombang_pmb_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gel)
                                <option value="{{ $gel->id }}">
                                    {{ $gel->periodePmb->nama ?? '' }} - {{ $gel->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                        <select name="jalur_seleksi_id" class="form-select" required>
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Program Studi</label>
                        <select name="program_studi_id" class="form-select">
                            <option value="">-- Semua Prodi (Umum) --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan untuk biaya umum semua prodi</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya Formulir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_formulir" class="form-control" required min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya Ujian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_ujian" class="form-control" required min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Generate Massal -->
<div class="modal fade" id="modalGenerate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('pmb.biaya-pendaftaran.generate-batch') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-lightning-fill me-2"></i>Generate Biaya Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Fitur ini akan membuat biaya pendaftaran secara massal untuk gelombang yang dipilih.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gelombang PMB <span class="text-danger">*</span></label>
                        <select name="gelombang_pmb_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gel)
                                <option value="{{ $gel->id }}">
                                    {{ $gel->periodePmb->nama ?? '' }} - {{ $gel->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Generate <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="generate_type" id="typeAllJalur" value="all_jalur" checked>
                            <label class="form-check-label" for="typeAllJalur">
                                <strong>Biaya Umum per Jalur</strong>
                                <small class="text-muted d-block">Berlaku untuk semua prodi (tanpa prodi spesifik)</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="generate_type" id="typeSpecificProdi" value="specific_prodi">
                            <label class="form-check-label" for="typeSpecificProdi">
                                <strong>Biaya Spesifik per Prodi</strong>
                                <small class="text-muted d-block">Biaya berbeda untuk setiap prodi</small>
                            </label>
                        </div>
                    </div>

                    <div id="jalurSelection" class="mb-3">
                        <label class="form-label">Pilih Jalur Seleksi <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach($jalurs as $jalur)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jalur_seleksi_ids[]" value="{{ $jalur->id }}" id="jalur{{ $jalur->id }}" checked>
                                    <label class="form-check-label" for="jalur{{ $jalur->id }}">{{ $jalur->nama }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="prodiSelection" class="mb-3" style="display: none;">
                        <label class="form-label">Pilih Program Studi <span class="text-danger">*</span></label>
                        <div class="row" style="max-height: 200px; overflow-y: auto;">
                            @foreach($prodis as $prodi)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="program_studi_ids[]" value="{{ $prodi->id }}" id="prodi{{ $prodi->id }}">
                                    <label class="form-check-label" for="prodi{{ $prodi->id }}">{{ $prodi->nama }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya Formulir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_formulir" class="form-control" required min="0" value="150000">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya Ujian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_ujian" class="form-control" required min="0" value="100000">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-lightning-fill me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Copy dari Gelombang -->
<div class="modal fade" id="modalCopy" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pmb.biaya-pendaftaran.copy-gelombang') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-copy me-2"></i>Copy dari Gelombang Lain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Salin semua pengaturan biaya dari gelombang sumber ke gelombang tujuan.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gelombang Sumber <span class="text-danger">*</span></label>
                        <select name="source_gelombang_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang Sumber --</option>
                            @foreach($gelombangs as $gel)
                                <option value="{{ $gel->id }}">
                                    {{ $gel->periodePmb->nama ?? '' }} - {{ $gel->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Gelombang yang biayanya akan disalin</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gelombang Tujuan <span class="text-danger">*</span></label>
                        <select name="target_gelombang_id" class="form-select" required>
                            <option value="">-- Pilih Gelombang Tujuan --</option>
                            @foreach($gelombangs as $gel)
                                <option value="{{ $gel->id }}">
                                    {{ $gel->periodePmb->nama ?? '' }} - {{ $gel->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Gelombang yang akan menerima biaya</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-copy me-1"></i> Copy Biaya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeAllJalur = document.getElementById('typeAllJalur');
    const typeSpecificProdi = document.getElementById('typeSpecificProdi');
    const jalurSelection = document.getElementById('jalurSelection');
    const prodiSelection = document.getElementById('prodiSelection');

    function toggleSelection() {
        if (typeAllJalur.checked) {
            jalurSelection.style.display = 'block';
            prodiSelection.style.display = 'none';
        } else {
            jalurSelection.style.display = 'none';
            prodiSelection.style.display = 'block';
        }
    }

    typeAllJalur.addEventListener('change', toggleSelection);
    typeSpecificProdi.addEventListener('change', toggleSelection);
});
</script>
@endpush
