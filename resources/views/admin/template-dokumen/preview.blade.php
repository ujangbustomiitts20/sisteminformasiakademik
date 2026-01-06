@extends('layouts.app')

@section('title', 'Preview - ' . $templateDokumen->nama)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-dokumen.index') }}">Template Dokumen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-dokumen.show', $templateDokumen->hashid) }}">{{ $templateDokumen->nama }}</a></li>
            <li class="breadcrumb-item active">Preview</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Preview Template</h1>
            <p class="text-muted mb-0">{{ $templateDokumen->nama }}</p>
        </div>
        <div>
            <a href="{{ route('admin.template-dokumen.show', $templateDokumen->hashid) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.template-dokumen.edit', $templateDokumen->hashid) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit Template
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Preview -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Hasil Preview</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                </div>
                <div class="card-body">
                    <!-- Document Preview -->
                    <div class="border rounded p-4" style="min-height: 600px; background: #fafafa;">
                        @if($templateDokumen->tampilkan_kop)
                            <div class="text-center border-bottom pb-3 mb-4">
                                @if($templateDokumen->tampilkan_logo)
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo" height="60" class="mb-2" onerror="this.style.display='none'">
                                @endif
                                <h5 class="mb-1">{{ setting('nama_institusi', 'INSTITUT TEKNOLOGI') }}</h5>
                                <p class="text-muted small mb-0">{{ setting('alamat_institusi', 'Alamat Institusi') }}</p>
                            </div>
                        @endif

                        @if($rendered['judul'])
                            <div class="text-center mb-4">
                                <h4 class="fw-bold text-decoration-underline">{{ $rendered['judul'] }}</h4>
                                @if($rendered['nomor'])
                                    <p class="mb-0">Nomor: {{ $rendered['nomor'] }}</p>
                                @endif
                            </div>
                        @endif

                        @if($rendered['isi'])
                            <div class="mb-4" style="text-align: justify; line-height: 1.8;">
                                {!! nl2br(e($rendered['isi'])) !!}
                            </div>
                        @endif

                        @if($rendered['penutup'])
                            <div class="mb-4" style="text-align: justify;">
                                {!! nl2br(e($rendered['penutup'])) !!}
                            </div>
                        @endif

                        <!-- Signature Area -->
                        <div class="row mt-5">
                            @if($templateDokumen->pejabat1 || $templateDokumen->pejabat2)
                                @if($templateDokumen->pejabat2)
                                    <!-- Two Signatures -->
                                    <div class="col-6 text-center">
                                        @if($templateDokumen->pejabat1)
                                            <p class="mb-0">{{ $templateDokumen->pejabat_1_label ?? 'Mengetahui' }},</p>
                                            <div style="height: 80px;" class="d-flex align-items-center justify-content-center">
                                                @if($templateDokumen->tampilkan_ttd_digital && $templateDokumen->pejabat1->tanda_tangan)
                                                    <img src="{{ $templateDokumen->pejabat1->tanda_tangan_url }}" alt="TTD" style="max-height: 60px;">
                                                @endif
                                            </div>
                                            <p class="mb-0 fw-bold text-decoration-underline">{{ $templateDokumen->pejabat1->nama }}</p>
                                            <p class="small mb-0">{{ $templateDokumen->pejabat1->nip ? 'NIP. ' . $templateDokumen->pejabat1->nip : '' }}</p>
                                        @endif
                                    </div>
                                    <div class="col-6 text-center">
                                        <p class="mb-0">{{ $templateDokumen->pejabat_2_label ?? $templateDokumen->pejabat2->jabatan }},</p>
                                        <div style="height: 80px;" class="d-flex align-items-center justify-content-center">
                                            @if($templateDokumen->tampilkan_ttd_digital && $templateDokumen->pejabat2->tanda_tangan)
                                                <img src="{{ $templateDokumen->pejabat2->tanda_tangan_url }}" alt="TTD" style="max-height: 60px;">
                                            @endif
                                        </div>
                                        <p class="mb-0 fw-bold text-decoration-underline">{{ $templateDokumen->pejabat2->nama }}</p>
                                        <p class="small mb-0">{{ $templateDokumen->pejabat2->nip ? 'NIP. ' . $templateDokumen->pejabat2->nip : '' }}</p>
                                    </div>
                                @else
                                    <!-- Single Signature -->
                                    <div class="col-6 offset-6 text-center">
                                        <p class="mb-0">{{ setting('kota_institusi', 'Kota') }}, {{ $rendered['tanggal'] ?? now()->format('d F Y') }}</p>
                                        <p class="mb-0 mt-2">{{ $templateDokumen->pejabat_1_label ?? $templateDokumen->pejabat1->jabatan }},</p>
                                        <div style="height: 80px;" class="d-flex align-items-center justify-content-center position-relative">
                                            @if($templateDokumen->tampilkan_stempel && $templateDokumen->pejabat1->stempel)
                                                <img src="{{ $templateDokumen->pejabat1->stempel_url }}" alt="Stempel" style="max-height: 80px; position: absolute; opacity: 0.7;">
                                            @endif
                                            @if($templateDokumen->tampilkan_ttd_digital && $templateDokumen->pejabat1->tanda_tangan)
                                                <img src="{{ $templateDokumen->pejabat1->tanda_tangan_url }}" alt="TTD" style="max-height: 60px; position: relative; z-index: 1;">
                                            @endif
                                        </div>
                                        <p class="mb-0 fw-bold text-decoration-underline">{{ $templateDokumen->pejabat1->nama }}</p>
                                        <p class="small mb-0">{{ $templateDokumen->pejabat1->nip ? 'NIP. ' . $templateDokumen->pejabat1->nip : '' }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>

                        @if($templateDokumen->catatan_bawah)
                            <hr class="mt-5">
                            <p class="text-muted small mb-0">{{ $rendered['catatan_bawah'] ?? $templateDokumen->catatan_bawah }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Data Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Data Contoh</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.template-dokumen.preview', $templateDokumen->hashid) }}" method="GET">
                        @foreach($templateDokumen->fields->sortBy('urutan') as $field)
                            <div class="mb-3">
                                <label class="form-label small">{{ $field->label }}</label>
                                <input type="text" class="form-control form-control-sm" name="fields[{{ $field->kode_field }}]" value="{{ $data[$field->kode_field] ?? $field->nilai_default }}">
                            </div>
                        @endforeach

                        @if($templateDokumen->fields->count() == 0)
                            <p class="text-muted small">
                                Template ini belum memiliki field dinamis.
                                <a href="{{ route('admin.template-dokumen.fields', $templateDokumen->hashid) }}">Tambah field</a>
                            </p>
                        @endif

                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Preview
                        </button>
                    </form>
                </div>
            </div>

            <!-- Data Used -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-code me-2"></i>Data yang Digunakan</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded small mb-0" style="max-height: 300px; overflow: auto;">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .breadcrumb, .btn, .card-header, .col-lg-4, nav {
        display: none !important;
    }
    .col-lg-8 {
        width: 100% !important;
        max-width: 100% !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-body {
        padding: 0 !important;
    }
    .border {
        border: none !important;
    }
}
</style>
@endpush
