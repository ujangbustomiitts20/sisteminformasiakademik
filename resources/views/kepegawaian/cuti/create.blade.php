@extends('layouts.app')

@section('title', 'Ajukan Cuti')

@section('content')
<div class="page-title">
    <h4>Ajukan Cuti Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.cuti.index') }}">Cuti Pegawai</a></li>
            <li class="breadcrumb-item active">Ajukan Cuti</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-plus me-2"></i>Form Pengajuan Cuti
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.cuti.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipe_pegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ old('tipe_pegawai') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ old('tipe_pegawai') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                            @error('tipe_pegawai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_selected" id="pegawai_selected" class="form-select @error('dosen_id') is-invalid @enderror @error('pegawai_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pegawai --</option>
                            </select>
                            <input type="hidden" name="dosen_id" id="dosen_id">
                            <input type="hidden" name="pegawai_id" id="pegawai_id">
                            @error('dosen_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('pegawai_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                        <select name="jenis_cuti" class="form-select @error('jenis_cuti') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                            <option value="sakit" {{ old('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                            <option value="melahirkan" {{ old('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                            <option value="besar" {{ old('jenis_cuti') == 'besar' ? 'selected' : '' }}>Cuti Besar</option>
                            <option value="alasan_penting" {{ old('jenis_cuti') == 'alasan_penting' ? 'selected' : '' }}>Cuti Alasan Penting</option>
                            <option value="diluar_tanggungan" {{ old('jenis_cuti') == 'diluar_tanggungan' ? 'selected' : '' }}>Cuti Di Luar Tanggungan Negara</option>
                        </select>
                        @error('jenis_cuti')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                            @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                            @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jumlah Hari</label>
                            <input type="number" name="jumlah_hari" id="jumlah_hari" class="form-control" value="{{ old('jumlah_hari', 0) }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Cuti <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" placeholder="Jelaskan alasan pengajuan cuti..." required>{{ old('alasan') }}</textarea>
                        @error('alasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Selama Cuti</label>
                        <textarea name="alamat_selama_cuti" class="form-control @error('alamat_selama_cuti') is-invalid @enderror" rows="2" placeholder="Alamat yang dapat dihubungi selama cuti...">{{ old('alamat_selama_cuti') }}</textarea>
                        @error('alamat_selama_cuti')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon Selama Cuti</label>
                        <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx">
                        @error('no_telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Atasan Langsung</label>
                        <select name="atasan_langsung_id" class="form-select @error('atasan_langsung_id') is-invalid @enderror">
                            <option value="">-- Pilih Atasan Langsung --</option>
                            @foreach($dosen as $d)
                            <option value="{{ $d->id }}" {{ old('atasan_langsung_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_lengkap }} ({{ $d->nidn ?? $d->nip }})</option>
                            @endforeach
                        </select>
                        @error('atasan_langsung_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen Pendukung</label>
                        <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Format: PDF, JPG, PNG. Maks: 2MB. Wajib untuk cuti sakit (surat dokter).</div>
                        @error('dokumen_pendukung')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Ajukan Cuti
                        </button>
                        <a href="{{ route('kepegawaian.cuti.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle me-2"></i>Informasi Cuti
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Jenis Cuti:</h6>
                    <ul class="small mb-0">
                        <li><strong>Cuti Tahunan:</strong> Maks 12 hari/tahun</li>
                        <li><strong>Cuti Sakit:</strong> Sesuai surat dokter</li>
                        <li><strong>Cuti Melahirkan:</strong> 3 bulan</li>
                        <li><strong>Cuti Besar:</strong> Maks 3 bulan</li>
                        <li><strong>Cuti Alasan Penting:</strong> Maks 2 bulan</li>
                        <li><strong>Cuti Di Luar Tanggungan:</strong> Maks 3 tahun</li>
                    </ul>
                </div>
                <hr>
                <div id="saldoCutiInfo" class="d-none">
                    <h6>Saldo Cuti Tahun {{ date('Y') }}:</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Total:</span>
                        <span id="totalCuti">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Digunakan:</span>
                        <span id="cutiDigunakan">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><strong>Sisa:</strong></span>
                        <span id="sisaCuti"><strong>-</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dosenList = @json($dosens);
const pegawaiList = @json($pegawais);

document.getElementById('tipe_pegawai').addEventListener('change', function() {
    const select = document.getElementById('pegawai_selected');
    const tipePegawai = this.value;
    
    select.innerHTML = '<option value="">-- Pilih Pegawai --</option>';
    document.getElementById('dosen_id').value = '';
    document.getElementById('pegawai_id').value = '';
    document.getElementById('saldoCutiInfo').classList.add('d-none');
    
    if (tipePegawai === 'dosen') {
        dosenList.forEach(d => {
            select.innerHTML += `<option value="${d.id}">${d.nama_lengkap} (${d.nidn || d.nip})</option>`;
        });
    } else if (tipePegawai === 'pegawai') {
        pegawaiList.forEach(p => {
            select.innerHTML += `<option value="${p.id}">${p.nama} (${p.nip})</option>`;
        });
    }
});

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
    
    // TODO: Load saldo cuti via AJAX
});

function calculateDays() {
    const start = document.getElementById('tanggal_mulai').value;
    const end = document.getElementById('tanggal_selesai').value;
    
    if (start && end) {
        const startDate = new Date(start);
        const endDate = new Date(end);
        const diffTime = endDate - startDate;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        if (diffDays > 0) {
            document.getElementById('jumlah_hari').value = diffDays;
        } else {
            document.getElementById('jumlah_hari').value = 0;
        }
    }
}

document.getElementById('tanggal_mulai').addEventListener('change', calculateDays);
document.getElementById('tanggal_selesai').addEventListener('change', calculateDays);
</script>
@endpush
