@extends('layouts.app')

@section('title', 'Kelola Prasyarat - ' . $mataKuliah->nama)

@section('content')
<div class="page-title">
    <h4>Kelola Prasyarat Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prasyarat.index') }}">Prasyarat MK</a></li>
            <li class="breadcrumb-item active">{{ $mataKuliah->kode }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Info Mata Kuliah -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-book me-2"></i>Informasi Mata Kuliah</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">Kode</td>
                        <td><strong>{{ $mataKuliah->kode }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $mataKuliah->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS</td>
                        <td>{{ $mataKuliah->sks }} SKS</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>
                            <span class="badge bg-primary">Semester {{ $mataKuliah->semester }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis</td>
                        <td>
                            <span class="badge bg-{{ $mataKuliah->jenis == 'Wajib' ? 'danger' : 'info' }}">
                                {{ $mataKuliah->jenis }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mataKuliah->programStudi->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Info Penting -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Catatan Penting</h6>
                <ul class="small text-muted mb-0">
                    <li>Prasyarat hanya bisa dari semester yang lebih rendah</li>
                    <li><strong>Wajib</strong>: Mahasiswa harus lulus dengan nilai minimal</li>
                    <li><strong>Pilihan</strong>: Cukup pernah mengambil MK tersebut</li>
                    <li>Perubahan akan berlaku untuk pengisian KRS selanjutnya</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form Prasyarat -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Daftar Prasyarat</h6>
            </div>
            <div class="card-body">
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

                <form action="{{ route('prasyarat.update', $mataKuliah->hashid) }}" method="POST" id="formPrasyarat">
                    @csrf
                    @method('PUT')

                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablePrasyarat">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Mata Kuliah Prasyarat</th>
                                    <th width="20%">Jenis</th>
                                    <th width="20%">Nilai Minimal</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mataKuliah->prasyaratDetail as $index => $p)
                                <tr class="prasyarat-row">
                                    <td>
                                        <select name="prasyarat[{{ $index }}][mata_kuliah_prasyarat_id]" class="form-select" required>
                                            <option value="">-- Pilih MK --</option>
                                            @foreach($mataKuliahTersedia as $mk)
                                            <option value="{{ $mk->id }}" {{ $p->mata_kuliah_prasyarat_id == $mk->id ? 'selected' : '' }}>
                                                {{ $mk->kode }} - {{ $mk->nama }} (Sem {{ $mk->semester }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="prasyarat[{{ $index }}][jenis_prasyarat]" class="form-select jenis-prasyarat" required>
                                            <option value="wajib" {{ $p->jenis_prasyarat == 'wajib' ? 'selected' : '' }}>Wajib Lulus</option>
                                            <option value="pilihan" {{ $p->jenis_prasyarat == 'pilihan' ? 'selected' : '' }}>Pernah Ambil</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="prasyarat[{{ $index }}][nilai_minimal]" class="form-select nilai-minimal" {{ $p->jenis_prasyarat == 'pilihan' ? 'disabled' : '' }}>
                                            @foreach(['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D'] as $nilai)
                                            <option value="{{ $nilai }}" {{ $p->nilai_minimal == $nilai ? 'selected' : '' }}>{{ $nilai }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr id="emptyRow">
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3"></i>
                                        <p class="mb-0">Belum ada prasyarat. Klik tombol "Tambah Prasyarat" untuk menambahkan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-success" id="btnTambahPrasyarat">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Prasyarat
                        </button>
                        <div>
                            <a href="{{ route('prasyarat.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Prasyarat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- MK yang membutuhkan ini sebagai prasyarat -->
        @if($mataKuliah->mataKuliahYangMembutuhkan->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-arrow-right-circle me-2"></i>MK yang Membutuhkan {{ $mataKuliah->kode }} sebagai Prasyarat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($mataKuliah->mataKuliahYangMembutuhkan as $mkLanjut)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary me-2">Sem {{ $mkLanjut->semester }}</span>
                            <span>{{ $mkLanjut->kode }} - {{ $mkLanjut->nama }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
let rowIndex = {{ $mataKuliah->prasyaratDetail->count() }};

const mataKuliahOptions = `
    <option value="">-- Pilih MK --</option>
    @foreach($mataKuliahTersedia as $mk)
    <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }} (Sem {{ $mk->semester }})</option>
    @endforeach
`;

const nilaiOptions = `
    @foreach(['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D'] as $nilai)
    <option value="{{ $nilai }}" {{ $nilai == 'D' ? 'selected' : '' }}>{{ $nilai }}</option>
    @endforeach
`;

// Tambah baris prasyarat baru
document.getElementById('btnTambahPrasyarat').addEventListener('click', function() {
    // Hapus empty row jika ada
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const tbody = document.querySelector('#tablePrasyarat tbody');
    const newRow = document.createElement('tr');
    newRow.className = 'prasyarat-row';
    newRow.innerHTML = `
        <td>
            <select name="prasyarat[${rowIndex}][mata_kuliah_prasyarat_id]" class="form-select" required>
                ${mataKuliahOptions}
            </select>
        </td>
        <td>
            <select name="prasyarat[${rowIndex}][jenis_prasyarat]" class="form-select jenis-prasyarat" required>
                <option value="wajib">Wajib Lulus</option>
                <option value="pilihan">Pernah Ambil</option>
            </select>
        </td>
        <td>
            <select name="prasyarat[${rowIndex}][nilai_minimal]" class="form-select nilai-minimal">
                ${nilaiOptions}
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-hapus-row">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    rowIndex++;

    // Bind event untuk jenis prasyarat baru
    bindJenisChange();
});

// Hapus baris
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-hapus-row')) {
        e.target.closest('tr').remove();
        
        // Jika tidak ada baris, tampilkan empty row
        const rows = document.querySelectorAll('.prasyarat-row');
        if (rows.length === 0) {
            const tbody = document.querySelector('#tablePrasyarat tbody');
            tbody.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3"></i>
                        <p class="mb-0">Belum ada prasyarat. Klik tombol "Tambah Prasyarat" untuk menambahkan.</p>
                    </td>
                </tr>
            `;
        }
    }
});

// Toggle nilai minimal berdasarkan jenis
function bindJenisChange() {
    document.querySelectorAll('.jenis-prasyarat').forEach(function(select) {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const nilaiSelect = row.querySelector('.nilai-minimal');
            if (this.value === 'pilihan') {
                nilaiSelect.disabled = true;
                nilaiSelect.value = '';
            } else {
                nilaiSelect.disabled = false;
                if (!nilaiSelect.value) nilaiSelect.value = 'D';
            }
        });
    });
}

// Initial bind
bindJenisChange();
</script>
@endpush
@endsection
