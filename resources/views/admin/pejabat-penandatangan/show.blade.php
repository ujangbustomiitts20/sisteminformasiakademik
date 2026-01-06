@extends('layouts.app')

@section('title', 'Detail Pejabat Penandatangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pejabat Penandatangan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pejabat-penandatangan.index') }}">Pejabat Penandatangan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.pejabat-penandatangan.edit', $pejabatPenandatangan->hashid) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.pejabat-penandatangan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Pejabat</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Kode</th>
                            <td><code>{{ $pejabatPenandatangan->kode }}</code></td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td><strong>{{ $pejabatPenandatangan->nama_lengkap }}</strong></td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>{{ $pejabatPenandatangan->jabatan }}</td>
                        </tr>
                        <tr>
                            <th>NIP/NIDN</th>
                            <td>{{ $pejabatPenandatangan->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pangkat/Golongan</th>
                            <td>{{ $pejabatPenandatangan->pangkat_golongan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>
                                <span class="badge bg-{{ $pejabatPenandatangan->kategori == 'pimpinan' ? 'danger' : ($pejabatPenandatangan->kategori == 'akademik' ? 'info' : ($pejabatPenandatangan->kategori == 'keuangan' ? 'warning' : 'secondary')) }}">
                                    {{ $kategoris[$pejabatPenandatangan->kategori] ?? $pejabatPenandatangan->kategori }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($pejabatPenandatangan->isBerlaku())
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Masa Berlaku</th>
                            <td>
                                @if($pejabatPenandatangan->berlaku_mulai || $pejabatPenandatangan->berlaku_sampai)
                                    {{ $pejabatPenandatangan->berlaku_mulai?->format('d/m/Y') ?? '...' }}
                                    s/d
                                    {{ $pejabatPenandatangan->berlaku_sampai?->format('d/m/Y') ?? '...' }}
                                @else
                                    <span class="text-muted">Tidak ditentukan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Urutan</th>
                            <td>{{ $pejabatPenandatangan->urutan }}</td>
                        </tr>
                        @if($pejabatPenandatangan->catatan)
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $pejabatPenandatangan->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dokumen yang Ditandatangani</h5>
                </div>
                <div class="card-body">
                    @if($pejabatPenandatangan->dokumen_terkait && count($pejabatPenandatangan->dokumen_terkait) > 0)
                        <div class="row">
                            @foreach($pejabatPenandatangan->dokumen_terkait as $dok)
                                <div class="col-md-4 mb-2">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-file-earmark-text me-1"></i>
                                        {{ $dokumens[$dok] ?? $dok }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada dokumen yang ditentukan (dapat menandatangani semua dokumen)</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tanda Tangan Digital</h5>
                </div>
                <div class="card-body text-center">
                    @if($pejabatPenandatangan->tanda_tangan)
                        <img src="{{ $pejabatPenandatangan->tanda_tangan_url }}" alt="Tanda Tangan" class="img-fluid border rounded mb-3" style="max-height: 150px; background: #f8f9fa;">
                        <form action="{{ route('admin.pejabat-penandatangan.delete-ttd', $pejabatPenandatangan->hashid) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus tanda tangan?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    @else
                        <div class="text-muted py-4">
                            <i class="bi bi-pen fs-1"></i>
                            <p class="mb-0">Belum ada tanda tangan</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stempel</h5>
                </div>
                <div class="card-body text-center">
                    @if($pejabatPenandatangan->stempel)
                        <img src="{{ $pejabatPenandatangan->stempel_url }}" alt="Stempel" class="img-fluid border rounded mb-3" style="max-height: 150px; background: #f8f9fa;">
                        <form action="{{ route('admin.pejabat-penandatangan.delete-stempel', $pejabatPenandatangan->hashid) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus stempel?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    @else
                        <div class="text-muted py-4">
                            <i class="bi bi-patch-check fs-1"></i>
                            <p class="mb-0">Belum ada stempel</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Penggunaan di Template</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Gunakan kode berikut di template cetak:</p>
                    <code class="d-block p-2 bg-light rounded small">
                        pejabat('{{ $pejabatPenandatangan->kode }}')
                    </code>
                    <p class="small text-muted mt-2 mb-0">Atau akses properti:</p>
                    <code class="d-block p-2 bg-light rounded small mt-1">
                        pejabat('{{ $pejabatPenandatangan->kode }}')->nama_lengkap<br>
                        pejabat('{{ $pejabatPenandatangan->kode }}')->jabatan<br>
                        pejabat('{{ $pejabatPenandatangan->kode }}')->nip<br>
                        pejabat('{{ $pejabatPenandatangan->kode }}')->tanda_tangan_url
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
