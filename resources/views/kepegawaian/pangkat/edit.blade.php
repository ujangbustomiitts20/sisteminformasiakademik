@extends('layouts.app')

@section('title', 'Edit Riwayat Pangkat - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Edit Riwayat Pangkat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.index', $dosen) }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Edit Pangkat</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-award me-2"></i>Edit Riwayat Pangkat/Golongan - {{ $dosen->nama }}
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.pangkat.update', [$dosen, $pangkat]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="golongan" class="form-label">Golongan <span class="text-danger">*</span></label>
                        <select name="golongan" id="golongan" class="form-select @error('golongan') is-invalid @enderror" required>
                            <option value="">-- Pilih Golongan --</option>
                            <option value="III/a" {{ old('golongan', $pangkat->golongan) == 'III/a' ? 'selected' : '' }}>III/a</option>
                            <option value="III/b" {{ old('golongan', $pangkat->golongan) == 'III/b' ? 'selected' : '' }}>III/b</option>
                            <option value="III/c" {{ old('golongan', $pangkat->golongan) == 'III/c' ? 'selected' : '' }}>III/c</option>
                            <option value="III/d" {{ old('golongan', $pangkat->golongan) == 'III/d' ? 'selected' : '' }}>III/d</option>
                            <option value="IV/a" {{ old('golongan', $pangkat->golongan) == 'IV/a' ? 'selected' : '' }}>IV/a</option>
                            <option value="IV/b" {{ old('golongan', $pangkat->golongan) == 'IV/b' ? 'selected' : '' }}>IV/b</option>
                            <option value="IV/c" {{ old('golongan', $pangkat->golongan) == 'IV/c' ? 'selected' : '' }}>IV/c</option>
                            <option value="IV/d" {{ old('golongan', $pangkat->golongan) == 'IV/d' ? 'selected' : '' }}>IV/d</option>
                            <option value="IV/e" {{ old('golongan', $pangkat->golongan) == 'IV/e' ? 'selected' : '' }}>IV/e</option>
                        </select>
                        @error('golongan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pangkat" class="form-label">Pangkat <span class="text-danger">*</span></label>
                        <select name="pangkat" id="pangkat" class="form-select @error('pangkat') is-invalid @enderror" required>
                            <option value="">-- Pilih Pangkat --</option>
                            <option value="Penata Muda" {{ old('pangkat', $pangkat->pangkat) == 'Penata Muda' ? 'selected' : '' }}>Penata Muda</option>
                            <option value="Penata Muda Tk. I" {{ old('pangkat', $pangkat->pangkat) == 'Penata Muda Tk. I' ? 'selected' : '' }}>Penata Muda Tk. I</option>
                            <option value="Penata" {{ old('pangkat', $pangkat->pangkat) == 'Penata' ? 'selected' : '' }}>Penata</option>
                            <option value="Penata Tk. I" {{ old('pangkat', $pangkat->pangkat) == 'Penata Tk. I' ? 'selected' : '' }}>Penata Tk. I</option>
                            <option value="Pembina" {{ old('pangkat', $pangkat->pangkat) == 'Pembina' ? 'selected' : '' }}>Pembina</option>
                            <option value="Pembina Tk. I" {{ old('pangkat', $pangkat->pangkat) == 'Pembina Tk. I' ? 'selected' : '' }}>Pembina Tk. I</option>
                            <option value="Pembina Utama Muda" {{ old('pangkat', $pangkat->pangkat) == 'Pembina Utama Muda' ? 'selected' : '' }}>Pembina Utama Muda</option>
                            <option value="Pembina Utama Madya" {{ old('pangkat', $pangkat->pangkat) == 'Pembina Utama Madya' ? 'selected' : '' }}>Pembina Utama Madya</option>
                            <option value="Pembina Utama" {{ old('pangkat', $pangkat->pangkat) == 'Pembina Utama' ? 'selected' : '' }}>Pembina Utama</option>
                        </select>
                        @error('pangkat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="no_sk" class="form-label">No. SK <span class="text-danger">*</span></label>
                        <input type="text" name="no_sk" id="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $pangkat->no_sk) }}" required>
                        @error('no_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_sk" class="form-label">Tanggal SK <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_sk" id="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk', $pangkat->tanggal_sk ? $pangkat->tanggal_sk->format('Y-m-d') : '') }}" required>
                        @error('tanggal_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tmt_pangkat" class="form-label">TMT Pangkat <span class="text-danger">*</span></label>
                        <input type="date" name="tmt_pangkat" id="tmt_pangkat" class="form-control @error('tmt_pangkat') is-invalid @enderror" value="{{ old('tmt_pangkat', $pangkat->tmt_pangkat ? $pangkat->tmt_pangkat->format('Y-m-d') : '') }}" required>
                        @error('tmt_pangkat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="pejabat_penetap" class="form-label">Pejabat Penetap</label>
                        <input type="text" name="pejabat_penetap" id="pejabat_penetap" class="form-control @error('pejabat_penetap') is-invalid @enderror" value="{{ old('pejabat_penetap', $pangkat->pejabat_penetap) }}">
                        @error('pejabat_penetap')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="masa_kerja_tahun" class="form-label">Masa Kerja (Tahun)</label>
                        <input type="number" step="0.01" name="masa_kerja_tahun" id="masa_kerja_tahun" class="form-control @error('masa_kerja_tahun') is-invalid @enderror" value="{{ old('masa_kerja_tahun', $pangkat->masa_kerja_tahun) }}" min="0">
                        @error('masa_kerja_tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="masa_kerja_bulan" class="form-label">Masa Kerja (Bulan)</label>
                        <input type="number" step="0.01" name="masa_kerja_bulan" id="masa_kerja_bulan" class="form-control @error('masa_kerja_bulan') is-invalid @enderror" value="{{ old('masa_kerja_bulan', $pangkat->masa_kerja_bulan) }}" min="0" max="12">
                        @error('masa_kerja_bulan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="file_sk" class="form-label">File SK</label>
                        <input type="file" name="file_sk" id="file_sk" class="form-control @error('file_sk') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
                        @if($pangkat->file_sk)
                        <div class="mt-2">
                            <a href="{{ Storage::url($pangkat->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Lihat File SK
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $pangkat->keterangan) }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('kepegawaian.index', $dosen) }}#pangkat" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-select pangkat based on golongan
    document.getElementById('golongan').addEventListener('change', function() {
        const pangkatSelect = document.getElementById('pangkat');
        const mapping = {
            'III/a': 'Penata Muda',
            'III/b': 'Penata Muda Tk. I',
            'III/c': 'Penata',
            'III/d': 'Penata Tk. I',
            'IV/a': 'Pembina',
            'IV/b': 'Pembina Tk. I',
            'IV/c': 'Pembina Utama Muda',
            'IV/d': 'Pembina Utama Madya',
            'IV/e': 'Pembina Utama'
        };
        
        if (mapping[this.value]) {
            pangkatSelect.value = mapping[this.value];
        }
    });
</script>
@endpush
@endsection
