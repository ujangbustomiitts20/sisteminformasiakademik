@extends('layouts.app')

@section('title', 'Input Presensi')

@section('content')
<div class="page-title">
    <h4>{{ isset($presensi) ? 'Edit' : 'Input' }} Presensi Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.index') }}">Presensi</a></li>
            <li class="breadcrumb-item active">{{ isset($presensi) ? 'Edit' : 'Input' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-check me-2"></i>Form Presensi
            </div>
            <div class="card-body">
                <form action="{{ isset($presensi) ? route('kepegawaian.presensi.update', $presensi) : route('kepegawaian.presensi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($presensi))
                    @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipe_pegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" {{ isset($presensi) ? 'disabled' : 'required' }}>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ (old('tipe_pegawai', isset($presensi) && $presensi->dosen_id ? 'dosen' : '')) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ (old('tipe_pegawai', isset($presensi) && $presensi->pegawai_id ? 'pegawai' : '')) == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                            @error('tipe_pegawai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_selected" id="pegawai_selected" class="form-select" {{ isset($presensi) ? 'disabled' : 'required' }}>
                                <option value="">-- Pilih Pegawai --</option>
                            </select>
                            <input type="hidden" name="dosen_id" id="dosen_id" value="{{ old('dosen_id', $presensi->dosen_id ?? '') }}">
                            <input type="hidden" name="pegawai_id" id="pegawai_id" value="{{ old('pegawai_id', $presensi->pegawai_id ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', isset($presensi) ? $presensi->tanggal : date('Y-m-d')) }}" required>
                            @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Masuk</label>
                            <input type="time" name="jam_masuk" class="form-control @error('jam_masuk') is-invalid @enderror" value="{{ old('jam_masuk', isset($presensi) ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '') }}">
                            @error('jam_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Keluar</label>
                            <input type="time" name="jam_keluar" class="form-control @error('jam_keluar') is-invalid @enderror" value="{{ old('jam_keluar', isset($presensi) && $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') : '') }}">
                            @error('jam_keluar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="hadir" {{ old('status', $presensi->status ?? '') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ old('status', $presensi->status ?? '') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="izin" {{ old('status', $presensi->status ?? '') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ old('status', $presensi->status ?? '') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="cuti" {{ old('status', $presensi->status ?? '') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="dinas_luar" {{ old('status', $presensi->status ?? '') == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
                            <option value="alpha" {{ old('status', $presensi->status ?? '') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Foto Masuk</label>
                            <input type="file" name="foto_masuk" class="form-control @error('foto_masuk') is-invalid @enderror" accept="image/*">
                            @error('foto_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($presensi) && $presensi->foto_masuk)
                            <div class="mt-2">
                                <img src="{{ Storage::url($presensi->foto_masuk) }}" alt="Foto Masuk" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto Keluar</label>
                            <input type="file" name="foto_keluar" class="form-control @error('foto_keluar') is-invalid @enderror" accept="image/*">
                            @error('foto_keluar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($presensi) && $presensi->foto_keluar)
                            <div class="mt-2">
                                <img src="{{ Storage::url($presensi->foto_keluar) }}" alt="Foto Keluar" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude_masuk" class="form-control" value="{{ old('latitude_masuk', $presensi->latitude_masuk ?? '') }}" placeholder="-6.123456">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude_masuk" class="form-control" value="{{ old('longitude_masuk', $presensi->longitude_masuk ?? '') }}" placeholder="106.123456">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2" placeholder="Keterangan tambahan...">{{ old('keterangan', $presensi->keterangan ?? '') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>{{ isset($presensi) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('kepegawaian.presensi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Jam Kerja
            </div>
            <div class="card-body">
                @if(isset($settingJamKerja))
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Jam Masuk</td>
                        <td><strong>{{ $settingJamKerja->jam_masuk }}</strong></td>
                    </tr>
                    <tr>
                        <td>Jam Keluar</td>
                        <td><strong>{{ $settingJamKerja->jam_keluar }}</strong></td>
                    </tr>
                    <tr>
                        <td>Toleransi Terlambat</td>
                        <td><strong>{{ $settingJamKerja->toleransi_terlambat }} menit</strong></td>
                    </tr>
                    <tr>
                        <td>Hari Kerja</td>
                        <td>
                            @php
                                $hariKerja = json_decode($settingJamKerja->hari_kerja ?? '[]');
                                $namaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            @endphp
                            @foreach($hariKerja as $h)
                            <span class="badge bg-primary me-1">{{ $namaHari[$h-1] ?? $h }}</span>
                            @endforeach
                        </td>
                    </tr>
                </table>
                @else
                <p class="text-muted mb-0">Setting jam kerja belum dikonfigurasi.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dosenList = @json($dosen ?? []);
const pegawaiList = @json($pegawai ?? []);
const editMode = {{ isset($presensi) ? 'true' : 'false' }};
const currentDosenId = '{{ $presensi->dosen_id ?? '' }}';
const currentPegawaiId = '{{ $presensi->pegawai_id ?? '' }}';

document.addEventListener('DOMContentLoaded', function() {
    if (editMode) {
        // Set current value for edit mode
        const tipePegawai = currentDosenId ? 'dosen' : 'pegawai';
        document.getElementById('tipe_pegawai').value = tipePegawai;
        loadPegawai(tipePegawai, currentDosenId || currentPegawaiId);
    }
});

document.getElementById('tipe_pegawai').addEventListener('change', function() {
    loadPegawai(this.value);
});

function loadPegawai(tipePegawai, selectedId = null) {
    const select = document.getElementById('pegawai_selected');
    select.innerHTML = '<option value="">-- Pilih Pegawai --</option>';
    document.getElementById('dosen_id').value = '';
    document.getElementById('pegawai_id').value = '';
    
    if (tipePegawai === 'dosen') {
        dosenList.forEach(d => {
            const selected = selectedId && d.id == selectedId ? 'selected' : '';
            select.innerHTML += `<option value="${d.id}" ${selected}>${d.nama_lengkap} (${d.nidn || d.nip})</option>`;
        });
        if (selectedId) document.getElementById('dosen_id').value = selectedId;
    } else if (tipePegawai === 'pegawai') {
        pegawaiList.forEach(p => {
            const selected = selectedId && p.id == selectedId ? 'selected' : '';
            select.innerHTML += `<option value="${p.id}" ${selected}>${p.nama} (${p.nip})</option>`;
        });
        if (selectedId) document.getElementById('pegawai_id').value = selectedId;
    }
}

document.getElementById('pegawai_selected').addEventListener('change', function() {
    const tipePegawai = document.getElementById('tipe_pegawai').value;
    const value = this.value;
    
    if (tipePegawai === 'dosen') {
        document.getElementById('dosen_id').value = value;
        document.getElementById('pegawai_id').value = '';
    } else {
        document.getElementById('pegawai_id').value = value;
        document.getElementById('dosen_id').value = '';
    }
});
</script>
@endpush
