@extends('layouts.app')

@section('title', 'Pengaturan Gaji Pegawai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pengaturan Gaji Pegawai</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.index') }}">Slip Gaji</a></li>
                    <li class="breadcrumb-item active">Pengaturan Gaji</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#generateMassalModal">
                <i class="bi bi-lightning me-1"></i> Generate Massal
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pengaturan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="tipe" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="dosen" {{ request('tipe') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="tendik" {{ request('tipe') == 'tendik' ? 'selected' : '' }}>Tendik</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama pegawai..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Pegawai</th>
                            <th>Tipe</th>
                            <th class="text-end">Gaji Pokok</th>
                            <th>Berlaku</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengaturans as $pengaturan)
                        <tr>
                            <td>
                                <strong>{{ $pengaturan->nama_pegawai }}</strong>
                                @if($pengaturan->dosen)
                                    <br><small class="text-muted">{{ $pengaturan->dosen->nidn ?? '-' }}</small>
                                @elseif($pengaturan->pegawai)
                                    <br><small class="text-muted">{{ $pengaturan->pegawai->nip ?? '-' }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $pengaturan->tipe_pegawai == 'Dosen' ? 'success' : 'info' }}">
                                    {{ $pengaturan->tipe_pegawai }}
                                </span>
                            </td>
                            <td class="text-end">Rp {{ number_format($pengaturan->gaji_pokok, 0, ',', '.') }}</td>
                            <td>
                                @if($pengaturan->berlaku_mulai)
                                    {{ $pengaturan->berlaku_mulai->format('d/m/Y') }}
                                    @if($pengaturan->berlaku_sampai)
                                        - {{ $pengaturan->berlaku_sampai->format('d/m/Y') }}
                                    @endif
                                @else
                                    <span class="text-muted">Tidak ditentukan</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $pengaturan->aktif ? 'success' : 'secondary' }}">
                                    {{ $pengaturan->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('kepegawaian.slip-gaji.pengaturan.show', $pengaturan->hashid) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('kepegawaian.slip-gaji.pengaturan.destroy', $pengaturan->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengaturan ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0">Belum ada pengaturan gaji</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $pengaturans->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.slip-gaji.pengaturan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengaturan Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                                <select name="tipe_pegawai" id="tipePegawaiModal" class="form-select" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="tendik">Tenaga Kependidikan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="dosenSelectModal" style="display: none;">
                                <label class="form-label">Dosen <span class="text-danger">*</span></label>
                                <select name="pegawai_id" class="form-select pegawai-select-modal" disabled>
                                    <option value="">Pilih Dosen</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="tendikSelectModal" style="display: none;">
                                <label class="form-label">Tendik <span class="text-danger">*</span></label>
                                <select name="pegawai_id" class="form-select pegawai-select-modal" disabled>
                                    <option value="">Pilih Tendik</option>
                                    @foreach($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="gaji_pokok" class="form-control" value="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Berlaku Mulai</label>
                                <input type="date" name="berlaku_mulai" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>

                    <hr>
                    <h6>Komponen Gaji Tambahan</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%"></th>
                                    <th>Komponen</th>
                                    <th>Jenis</th>
                                    <th width="30%">Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($komponens as $komponen)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1">
                                    </td>
                                    <td>{{ $komponen->nama }}</td>
                                    <td>
                                        <span class="badge bg-{{ $komponen->jenis == 'pendapatan' ? 'success' : 'danger' }}">
                                            {{ ucfirst($komponen->jenis) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                            class="form-control form-control-sm" value="{{ $komponen->nilai_default }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

<!-- Modal Generate Massal -->
<div class="modal fade" id="generateMassalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.slip-gaji.pengaturan.generate-massal') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Pengaturan Gaji Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Fitur ini akan membuat pengaturan gaji untuk semua pegawai yang belum memiliki pengaturan, dengan gaji pokok dan komponen yang sama.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-select" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="dosen">Semua Dosen Aktif</option>
                                    <option value="tendik">Semua Tenaga Kependidikan Aktif</option>
                                    <option value="semua">Semua Pegawai (Dosen + Tendik)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="gaji_pokok" class="form-control" required min="0" step="1000" placeholder="Contoh: 5000000">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="overwrite" id="overwriteCheck" value="1">
                            <label class="form-check-label" for="overwriteCheck">
                                <strong>Update pengaturan yang sudah ada</strong>
                                <small class="text-muted d-block">Centang jika ingin menimpa gaji pokok & komponen pegawai yang sudah memiliki pengaturan</small>
                            </label>
                        </div>
                    </div>

                    <hr>
                    <h6>Komponen Gaji Tambahan</h6>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="5%"></th>
                                    <th>Komponen</th>
                                    <th>Jenis</th>
                                    <th width="30%">Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($komponens as $komponen)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="komponen[{{ $komponen->id }}][aktif]" value="1" {{ $komponen->wajib ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        {{ $komponen->nama }}
                                        @if($komponen->wajib)
                                            <span class="badge bg-warning text-dark">Wajib</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $komponen->jenis == 'pendapatan' ? 'success' : 'danger' }}">
                                            {{ ucfirst($komponen->jenis) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" name="komponen[{{ $komponen->id }}][nilai]" 
                                            class="form-control form-control-sm" value="{{ $komponen->nilai_default }}" min="0">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnGenerateMassal">
                        <i class="bi bi-lightning me-1"></i> Generate Massal
                    </button>
                    <button type="button" class="btn btn-success disabled" id="btnGenerateLoading" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Memproses...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipePegawaiModal = document.getElementById('tipePegawaiModal');
    const dosenSelectModal = document.getElementById('dosenSelectModal');
    const tendikSelectModal = document.getElementById('tendikSelectModal');

    tipePegawaiModal?.addEventListener('change', function() {
        const val = this.value;
        dosenSelectModal.style.display = val === 'dosen' ? 'block' : 'none';
        tendikSelectModal.style.display = val === 'tendik' ? 'block' : 'none';
        
        dosenSelectModal.querySelector('select').disabled = val !== 'dosen';
        tendikSelectModal.querySelector('select').disabled = val !== 'tendik';
    });

    // Loading state untuk Generate Massal
    const generateMassalForm = document.querySelector('#generateMassalModal form');
    const btnGenerate = document.getElementById('btnGenerateMassal');
    const btnLoading = document.getElementById('btnGenerateLoading');

    generateMassalForm?.addEventListener('submit', function(e) {
        // Tampilkan loading
        btnGenerate.style.display = 'none';
        btnLoading.style.display = 'inline-block';
        
        // Disable semua input dalam form
        const inputs = generateMassalForm.querySelectorAll('input, select, button');
        inputs.forEach(input => input.disabled = true);
        btnLoading.disabled = false; // Keep visible but disabled
    });

    // Loading state untuk form Tambah Pengaturan
    const addForm = document.querySelector('#addModal form');
    const btnSimpan = addForm?.querySelector('button[type="submit"]');
    
    addForm?.addEventListener('submit', function(e) {
        if (btnSimpan) {
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
        }
    });
});
</script>
@endpush
@endsection
