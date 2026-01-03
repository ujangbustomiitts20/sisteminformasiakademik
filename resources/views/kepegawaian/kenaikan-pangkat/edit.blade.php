@extends('layouts.app')

@section('title', 'Edit Kenaikan Pangkat')

@section('content')
<div class="page-title">
    <h4>Edit Kenaikan Pangkat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}">Kenaikan Pangkat</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Form Edit Kenaikan Pangkat
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.kenaikan-pangkat.update', $kenaikanPangkat) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Info Pegawai (readonly) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Pegawai</label>
                    <div class="form-control bg-light">
                        @if($kenaikanPangkat->dosen)
                        <i class="bi bi-person me-1"></i>{{ $kenaikanPangkat->dosen->nama_lengkap }} 
                        <span class="badge bg-info">Dosen</span>
                        @elseif($kenaikanPangkat->pegawai)
                        <i class="bi bi-person me-1"></i>{{ $kenaikanPangkat->pegawai->nama }} 
                        <span class="badge bg-secondary">Tendik</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-calendar me-2"></i>Periode & Jenis</h5>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Periode <span class="text-danger">*</span></label>
                    <select name="periode" class="form-select @error('periode') is-invalid @enderror" required>
                        @foreach($periodeList as $key => $value)
                        <option value="{{ $key }}" {{ old('periode', $kenaikanPangkat->periode) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('periode')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror" value="{{ old('tahun', $kenaikanPangkat->tahun) }}" min="2000" max="2100" required>
                    @error('tahun')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Kenaikan <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                        @foreach($jenisList as $key => $value)
                        <option value="{{ $key }}" {{ old('jenis', $kenaikanPangkat->jenis) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('jenis')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-arrow-left-right me-2"></i>Detail Pangkat</h5>
            
            <!-- Pangkat Lama -->
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Pangkat Lama</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Pangkat</label>
                            <input type="text" name="pangkat_lama" class="form-control" value="{{ old('pangkat_lama', $kenaikanPangkat->pangkat_lama) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Golongan</label>
                            <select name="golongan_lama" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach($pangkatList as $key => $value)
                                <option value="{{ $key }}" {{ old('golongan_lama', $kenaikanPangkat->golongan_lama) == $key ? 'selected' : '' }}>{{ $key }} - {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">TMT Pangkat Lama</label>
                            <input type="date" name="tmt_pangkat_lama" class="form-control" value="{{ old('tmt_pangkat_lama', $kenaikanPangkat->tmt_pangkat_lama?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pangkat Baru -->
            <div class="card bg-success bg-opacity-10 mb-3">
                <div class="card-body">
                    <h6 class="mb-3 text-success">Pangkat Baru <span class="text-danger">*</span></h6>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Pangkat <span class="text-danger">*</span></label>
                            <input type="text" name="pangkat_baru" class="form-control @error('pangkat_baru') is-invalid @enderror" value="{{ old('pangkat_baru', $kenaikanPangkat->pangkat_baru) }}" required>
                            @error('pangkat_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Golongan <span class="text-danger">*</span></label>
                            <select name="golongan_baru" class="form-select @error('golongan_baru') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach($pangkatList as $key => $value)
                                <option value="{{ $key }}" {{ old('golongan_baru', $kenaikanPangkat->golongan_baru) == $key ? 'selected' : '' }}>{{ $key }} - {{ $value }}</option>
                                @endforeach
                            </select>
                            @error('golongan_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">TMT Pangkat Baru <span class="text-danger">*</span></label>
                            <input type="date" name="tmt_pangkat_baru" class="form-control @error('tmt_pangkat_baru') is-invalid @enderror" value="{{ old('tmt_pangkat_baru', $kenaikanPangkat->tmt_pangkat_baru?->format('Y-m-d')) }}" required>
                            @error('tmt_pangkat_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-clock me-2"></i>Masa Kerja & Penilaian</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Masa Kerja <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" name="masa_kerja_tahun" class="form-control" value="{{ old('masa_kerja_tahun', $kenaikanPangkat->masa_kerja_tahun) }}" min="0" required>
                                <span class="input-group-text">Thn</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" name="masa_kerja_bulan" class="form-control" value="{{ old('masa_kerja_bulan', $kenaikanPangkat->masa_kerja_bulan) }}" min="0" max="11" required>
                                <span class="input-group-text">Bln</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ old('pendidikan_terakhir', $kenaikanPangkat->pendidikan_terakhir) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Angka Kredit</label>
                    <input type="number" name="angka_kredit" class="form-control" value="{{ old('angka_kredit', $kenaikanPangkat->angka_kredit) }}" min="0" step="0.01">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Penilaian Kinerja</label>
                    <textarea name="penilaian_kinerja" class="form-control" rows="2">{{ old('penilaian_kinerja', $kenaikanPangkat->penilaian_kinerja) }}</textarea>
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-folder me-2"></i>Dokumen</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Dokumen SK (PDF)</label>
                    @if($kenaikanPangkat->dokumen_sk)
                    <div class="mb-2">
                        <a href="{{ Storage::url($kenaikanPangkat->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Lihat
                        </a>
                    </div>
                    @endif
                    <input type="file" name="dokumen_sk" class="form-control" accept=".pdf">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dokumen PAK (PDF)</label>
                    @if($kenaikanPangkat->dokumen_pak)
                    <div class="mb-2">
                        <a href="{{ Storage::url($kenaikanPangkat->dokumen_pak) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Lihat
                        </a>
                    </div>
                    @endif
                    <input type="file" name="dokumen_pak" class="form-control" accept=".pdf">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dokumen SKP (PDF)</label>
                    @if($kenaikanPangkat->dokumen_skp)
                    <div class="mb-2">
                        <a href="{{ Storage::url($kenaikanPangkat->dokumen_skp) }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Lihat
                        </a>
                    </div>
                    @endif
                    <input type="file" name="dokumen_skp" class="form-control" accept=".pdf">
                </div>
            </div>
            <div class="form-text mb-3">Format: PDF, Maks: 5MB per file. Kosongkan jika tidak ingin mengubah.</div>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $kenaikanPangkat->catatan) }}</textarea>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('kepegawaian.kenaikan-pangkat.show', $kenaikanPangkat) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
