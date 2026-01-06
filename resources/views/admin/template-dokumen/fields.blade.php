@extends('layouts.app')

@section('title', 'Kelola Field - ' . $templateDokumen->nama)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-dokumen.index') }}">Template Dokumen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-dokumen.show', $templateDokumen->hashid) }}">{{ $templateDokumen->nama }}</a></li>
            <li class="breadcrumb-item active">Kelola Field</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola Field</h1>
            <p class="text-muted mb-0">
                <code class="bg-light px-2 py-1 rounded me-2">{{ $templateDokumen->kode }}</code>
                {{ $templateDokumen->nama }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.template-dokumen.show', $templateDokumen->hashid) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahField">
                <i class="bi bi-plus-lg me-1"></i> Tambah Field
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Fields List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Field ({{ $templateDokumen->fields->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($templateDokumen->fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 ps-4" style="width: 40px;">#</th>
                                        <th class="border-0">Kode / Placeholder</th>
                                        <th class="border-0">Label</th>
                                        <th class="border-0">Tipe</th>
                                        <th class="border-0">Sumber Data</th>
                                        <th class="border-0 text-center">Wajib</th>
                                        <th class="border-0 text-end pe-4" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templateDokumen->fields->sortBy('urutan') as $field)
                                        <tr>
                                            <td class="ps-4 text-muted">{{ $field->urutan }}</td>
                                            <td>
                                                <code class="bg-primary bg-opacity-10 text-primary px-2 py-1 rounded">{!! '{'.$field->kode_field.'}' !!}</code>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">{{ $field->label }}</span>
                                                @if($field->nilai_default)
                                                    <br><small class="text-muted">Default: {{ Str::limit($field->nilai_default, 30) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $tipeFields[$field->tipe] ?? $field->tipe }}</span>
                                                @if($field->opsi)
                                                    <br><small class="text-muted">{{ count($field->opsi) }} opsi</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($field->sumber_data)
                                                    <small class="text-info"><i class="bi bi-database me-1"></i>{{ $field->sumber_data }}</small>
                                                @else
                                                    <span class="text-muted">Manual</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($field->wajib)
                                                    <span class="badge bg-danger">Ya</span>
                                                @else
                                                    <span class="badge bg-light text-muted">Tidak</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $field->hashid }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $field->hashid }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit Field -->
                                        <div class="modal fade" id="modalEdit{{ $field->hashid }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.template-dokumen.update-field', [$templateDokumen->hashid, $field->hashid]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Field: {{ $field->label }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Kode Field <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="kode_field" value="{{ $field->kode_field }}" required>
                                                                <small class="text-muted">Akan menjadi placeholder: {!! '{'.$field->kode_field.'}' !!}</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Label <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="label" value="{{ $field->label }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Tipe Input <span class="text-danger">*</span></label>
                                                                <select class="form-select" name="tipe" required>
                                                                    @foreach($tipeFields as $key => $label)
                                                                        <option value="{{ $key }}" {{ $field->tipe == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Opsi (untuk select/radio/checkbox)</label>
                                                                <textarea class="form-control" name="opsi" rows="3" placeholder="key1:Label 1&#10;key2:Label 2">@if($field->opsi)@foreach($field->opsi as $k => $v){{ $k }}:{{ $v }}&#10;@endforeach @endif</textarea>
                                                                <small class="text-muted">Format: key:Label (satu per baris)</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Nilai Default</label>
                                                                <input type="text" class="form-control" name="nilai_default" value="{{ $field->nilai_default }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Sumber Data (Auto-fill)</label>
                                                                <select class="form-select" name="sumber_data">
                                                                    <option value="">-- Manual Input --</option>
                                                                    @foreach($sumberData as $key => $label)
                                                                        <option value="{{ $key }}" {{ $field->sumber_data == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <small class="text-muted">Jika dipilih, field akan terisi otomatis dari data terkait</small>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label class="form-label">Urutan</label>
                                                                    <input type="number" class="form-control" name="urutan" value="{{ $field->urutan }}" min="0">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label">&nbsp;</label>
                                                                    <div class="form-check mt-2">
                                                                        <input class="form-check-input" type="checkbox" name="wajib" id="wajib_edit_{{ $field->hashid }}" {{ $field->wajib ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="wajib_edit_{{ $field->hashid }}">Field Wajib</label>
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

                                        <!-- Modal Hapus Field -->
                                        <div class="modal fade" id="modalHapus{{ $field->hashid }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus field <strong>{{ $field->label }}</strong>?</p>
                                                        <div class="alert alert-warning small">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            Placeholder <code>{!! '{'.$field->kode_field.'}' !!}</code> tidak akan diganti lagi pada dokumen.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('admin.template-dokumen.destroy-field', [$templateDokumen->hashid, $field->hashid]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-input-cursor-text fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-3">Belum ada field untuk template ini.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahField">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Field Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Add Common Fields -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Field Cepat</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Klik untuk menambahkan field umum:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="nama" data-label="Nama Lengkap" data-sumber="mahasiswa.nama">Nama</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="nim" data-label="NIM" data-sumber="mahasiswa.nim">NIM</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="program_studi" data-label="Program Studi" data-sumber="mahasiswa.program_studi">Prodi</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="fakultas" data-label="Fakultas" data-sumber="mahasiswa.fakultas">Fakultas</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="tempat_lahir" data-label="Tempat Lahir" data-sumber="mahasiswa.tempat_lahir">Tempat Lahir</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="tanggal_lahir" data-label="Tanggal Lahir" data-sumber="mahasiswa.tanggal_lahir">Tgl Lahir</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="alamat" data-label="Alamat" data-sumber="mahasiswa.alamat">Alamat</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="angkatan" data-label="Angkatan" data-sumber="mahasiswa.angkatan">Angkatan</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="semester" data-label="Semester" data-sumber="">Semester</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="ipk" data-label="IPK" data-sumber="mahasiswa.ipk">IPK</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="no_surat" data-label="Nomor Surat" data-sumber="">No Surat</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-field" data-kode="keperluan" data-label="Keperluan" data-sumber="">Keperluan</button>
                    </div>
                </div>
            </div>

            <!-- Sumber Data Reference -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-database me-2"></i>Sumber Data</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Field dapat diisi otomatis dari data berikut:</p>
                    
                    <div class="mb-3">
                        <h6 class="small text-uppercase text-muted mb-2">Mahasiswa</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($sumberData as $key => $label)
                                @if(str_starts_with($key, 'mahasiswa.'))
                                    <code class="bg-light px-2 py-1 rounded small">{{ $key }}</code>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="small text-uppercase text-muted mb-2">Dosen</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($sumberData as $key => $label)
                                @if(str_starts_with($key, 'dosen.'))
                                    <code class="bg-light px-2 py-1 rounded small">{{ $key }}</code>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <h6 class="small text-uppercase text-muted mb-2">Pegawai</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($sumberData as $key => $label)
                                @if(str_starts_with($key, 'pegawai.'))
                                    <code class="bg-light px-2 py-1 rounded small">{{ $key }}</code>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Field -->
<div class="modal fade" id="modalTambahField" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.template-dokumen.store-field', $templateDokumen->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Field Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Field <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_field" id="kode_field" required placeholder="nama_mahasiswa">
                        <small class="text-muted">Gunakan snake_case. Akan menjadi placeholder: {nama_mahasiswa}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="label" id="label_field" required placeholder="Nama Mahasiswa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Input <span class="text-danger">*</span></label>
                        <select class="form-select" name="tipe" id="tipe_field" required>
                            @foreach($tipeFields as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opsi (untuk select/radio/checkbox)</label>
                        <textarea class="form-control" name="opsi" rows="3" placeholder="key1:Label 1&#10;key2:Label 2"></textarea>
                        <small class="text-muted">Format: key:Label (satu per baris)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai Default</label>
                        <input type="text" class="form-control" name="nilai_default">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sumber Data (Auto-fill)</label>
                        <select class="form-select" name="sumber_data" id="sumber_data_field">
                            <option value="">-- Manual Input --</option>
                            @foreach($sumberData as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Jika dipilih, field akan terisi otomatis dari data terkait</small>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Urutan</label>
                            <input type="number" class="form-control" name="urutan" value="{{ $templateDokumen->fields->count() + 1 }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="wajib" id="wajib_field">
                                <label class="form-check-label" for="wajib_field">Field Wajib</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Field
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
    // Quick field buttons
    document.querySelectorAll('.quick-field').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const kode = this.dataset.kode;
            const label = this.dataset.label;
            const sumber = this.dataset.sumber;
            
            document.getElementById('kode_field').value = kode;
            document.getElementById('label_field').value = label;
            document.getElementById('sumber_data_field').value = sumber;
            
            // Open modal
            var modal = new bootstrap.Modal(document.getElementById('modalTambahField'));
            modal.show();
        });
    });
});
</script>
@endpush
