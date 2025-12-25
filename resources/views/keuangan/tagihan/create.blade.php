@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Tambah Tagihan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Tagihan Baru</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('tagihan.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                                <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                                    <option value="">Pilih Mahasiswa</option>
                                    @foreach($mahasiswa as $mhs)
                                        <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>{{ $mhs->nim }} - {{ $mhs->nama }}</option>
                                    @endforeach
                                </select>
                                @error('mahasiswa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($tahunAkademik as $ta)
                                        <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                                    @endforeach
                                </select>
                                @error('tahun_akademik_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Tagihan <span class="text-danger">*</span></label>
                                <select name="jenis_tagihan" class="form-select @error('jenis_tagihan') is-invalid @enderror" id="jenisTagihan" required>
                                    <option value="">Pilih Jenis</option>
                                    @foreach(\App\Models\Tarif::JENIS as $jenis)
                                        <option value="{{ $jenis }}" {{ old('jenis_tagihan') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                    @endforeach
                                    <option value="Lainnya" {{ old('jenis_tagihan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis_tagihan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tarif (Opsional)</label>
                                <select name="tarif_id" class="form-select @error('tarif_id') is-invalid @enderror" id="tarifSelect">
                                    <option value="">Pilih Tarif (Opsional)</option>
                                    @foreach($tarif as $t)
                                        <option value="{{ $t->id }}" data-nominal="{{ $t->nominal }}" data-jenis="{{ $t->jenis }}" {{ old('tarif_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tarif }} - Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tarif_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" id="nominalInput" value="{{ old('nominal') }}" min="0" required>
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo') }}" required>
                                @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan_tagihan" class="form-control @error('keterangan_tagihan') is-invalid @enderror" rows="3">{{ old('keterangan_tagihan') }}</textarea>
                            @error('keterangan_tagihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tarifSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('nominalInput').value = selected.dataset.nominal;
        document.getElementById('jenisTagihan').value = selected.dataset.jenis;
    }
});
</script>
@endpush
@endsection
