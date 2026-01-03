@extends('layouts.app')

@section('title', 'Setting Jam Kerja')

@section('content')
<div class="page-title">
    <h4>Setting Jam Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.index') }}">Presensi</a></li>
            <li class="breadcrumb-item active">Setting Jam Kerja</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Daftar Setting Jam Kerja -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Daftar Setting Jam Kerja</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="30">No</th>
                                <th>Nama Setting</th>
                                <th class="text-center">Jam Masuk</th>
                                <th class="text-center">Jam Keluar</th>
                                <th class="text-center">Toleransi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settings as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->nama_setting }}</strong>
                                    @if($item->is_active)
                                    <span class="badge bg-success ms-1">Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}</td>
                                <td class="text-center">{{ $item->toleransi_terlambat }} menit</td>
                                <td class="text-center">
                                    @if($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editSetting({{ json_encode($item) }})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-clock text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">Belum ada setting jam kerja</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tambah/Edit Setting -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i><span id="formTitle">Tambah Setting Jam Kerja</span>
            </div>
            <div class="card-body">
                <form id="settingForm" action="{{ route('kepegawaian.presensi.setting.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Setting <span class="text-danger">*</span></label>
                        <input type="text" name="nama_setting" id="nama_setting" class="form-control @error('nama_setting') is-invalid @enderror" value="{{ old('nama_setting') }}" placeholder="Contoh: Jam Kerja Reguler" required>
                        @error('nama_setting')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jam Masuk <span class="text-danger">*</span></label>
                            <input type="time" name="jam_masuk" id="jam_masuk" class="form-control @error('jam_masuk') is-invalid @enderror" value="{{ old('jam_masuk', '08:00') }}" required>
                            @error('jam_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jam Keluar <span class="text-danger">*</span></label>
                            <input type="time" name="jam_keluar" id="jam_keluar" class="form-control @error('jam_keluar') is-invalid @enderror" value="{{ old('jam_keluar', '16:00') }}" required>
                            @error('jam_keluar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Toleransi Terlambat (menit) <span class="text-danger">*</span></label>
                        <input type="number" name="toleransi_terlambat" id="toleransi_terlambat" class="form-control @error('toleransi_terlambat') is-invalid @enderror" value="{{ old('toleransi_terlambat', 15) }}" min="0" required>
                        <div class="form-text">Waktu yang diberikan setelah jam masuk sebelum dinyatakan terlambat.</div>
                        @error('toleransi_terlambat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Jadikan Aktif</label>
                        </div>
                        <div class="form-text">Jika dicentang, setting lain akan dinonaktifkan.</div>
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i><span id="btnSubmitText">Simpan</span>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-3 bg-light">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Setting Aktif:</strong> Hanya satu setting yang bisa aktif. Setting aktif digunakan untuk perhitungan keterlambatan.</p>
                <p class="small mb-0"><strong>Toleransi:</strong> Durasi dalam menit yang diizinkan untuk terlambat sebelum status berubah menjadi "Terlambat".</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editSetting(setting) {
    document.getElementById('formTitle').textContent = 'Edit Setting Jam Kerja';
    document.getElementById('btnSubmitText').textContent = 'Update';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('settingForm').action = '{{ url("kepegawaian/presensi/setting") }}/' + setting.hashid;
    
    document.getElementById('nama_setting').value = setting.nama_setting;
    document.getElementById('jam_masuk').value = setting.jam_masuk ? setting.jam_masuk.substring(0, 5) : '08:00';
    document.getElementById('jam_keluar').value = setting.jam_keluar ? setting.jam_keluar.substring(0, 5) : '16:00';
    document.getElementById('toleransi_terlambat').value = setting.toleransi_terlambat;
    document.getElementById('is_active').checked = setting.is_active == 1;
    
    // Scroll to form
    document.getElementById('settingForm').scrollIntoView({ behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Setting Jam Kerja';
    document.getElementById('btnSubmitText').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('settingForm').action = '{{ route("kepegawaian.presensi.setting.store") }}';
    document.getElementById('settingForm').reset();
    document.getElementById('jam_masuk').value = '08:00';
    document.getElementById('jam_keluar').value = '16:00';
    document.getElementById('toleransi_terlambat').value = 15;
}
</script>
@endpush
@endsection
