@extends('layouts.app')

@section('title', 'Detail Template - ' . $templateDokumen->nama)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-dokumen.index') }}">Template Dokumen</a></li>
            <li class="breadcrumb-item active">{{ $templateDokumen->nama }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $templateDokumen->nama }}</h1>
            <p class="text-muted mb-0">
                <code class="bg-light px-2 py-1 rounded me-2">{{ $templateDokumen->kode }}</code>
                @php
                    $badgeClass = match($templateDokumen->kategori) {
                        'akademik' => 'bg-info',
                        'keuangan' => 'bg-warning text-dark',
                        'kepegawaian' => 'bg-primary',
                        'pmb' => 'bg-success',
                        'umum' => 'bg-secondary',
                        default => 'bg-secondary'
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $kategoris[$templateDokumen->kategori] ?? $templateDokumen->kategori }}</span>
                @if($templateDokumen->aktif)
                    <span class="badge bg-success ms-1">Aktif</span>
                @else
                    <span class="badge bg-secondary ms-1">Nonaktif</span>
                @endif
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.template-dokumen.fields', $templateDokumen->hashid) }}" class="btn btn-primary">
                <i class="bi bi-list-check me-1"></i> Kelola Field
            </a>
            <a href="{{ route('admin.template-dokumen.preview', $templateDokumen->hashid) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-1"></i> Preview
            </a>
            <a href="{{ route('admin.template-dokumen.edit', $templateDokumen->hashid) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('admin.template-dokumen.duplicate', $templateDokumen->hashid) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-copy me-1"></i> Duplikat
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Template Content -->
        <div class="col-lg-8">
            <!-- Template Sections -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Konten Template</h5>
                </div>
                <div class="card-body">
                    @if($templateDokumen->template_judul)
                        <div class="mb-4">
                            <label class="form-label text-muted small">JUDUL</label>
                            <div class="p-3 bg-light rounded">
                                <strong class="fs-5">{{ $templateDokumen->template_judul }}</strong>
                            </div>
                        </div>
                    @endif

                    @if($templateDokumen->template_nomor)
                        <div class="mb-4">
                            <label class="form-label text-muted small">FORMAT NOMOR SURAT</label>
                            <div class="p-3 bg-light rounded">
                                <code>{{ $templateDokumen->template_nomor }}</code>
                            </div>
                        </div>
                    @endif

                    @if($templateDokumen->template_isi)
                        <div class="mb-4">
                            <label class="form-label text-muted small">ISI DOKUMEN</label>
                            <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $templateDokumen->template_isi }}</div>
                        </div>
                    @endif

                    @if($templateDokumen->template_penutup)
                        <div class="mb-4">
                            <label class="form-label text-muted small">PENUTUP</label>
                            <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $templateDokumen->template_penutup }}</div>
                        </div>
                    @endif

                    @if($templateDokumen->catatan_bawah)
                        <div class="mb-0">
                            <label class="form-label text-muted small">CATATAN BAWAH</label>
                            <div class="p-3 bg-light rounded text-muted small">{{ $templateDokumen->catatan_bawah }}</div>
                        </div>
                    @endif

                    @if(!$templateDokumen->template_judul && !$templateDokumen->template_nomor && !$templateDokumen->template_isi)
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-file-earmark fs-1 d-block mb-2"></i>
                            Konten template belum diisi.
                            <a href="{{ route('admin.template-dokumen.edit', $templateDokumen->hashid) }}">Isi sekarang</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fields List -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-input-cursor-text me-2"></i>Field Dinamis</h5>
                    <a href="{{ route('admin.template-dokumen.fields', $templateDokumen->hashid) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($templateDokumen->fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Placeholder</th>
                                        <th class="border-0">Label</th>
                                        <th class="border-0">Tipe</th>
                                        <th class="border-0">Sumber Data</th>
                                        <th class="border-0 text-center">Wajib</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templateDokumen->fields->sortBy('urutan') as $field)
                                        <tr>
                                            <td><code>{!! '{'.$field->kode_field.'}' !!}</code></td>
                                            <td>{{ $field->label }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ App\Models\TemplateDokumenField::TIPE[$field->tipe] ?? $field->tipe }}</span>
                                            </td>
                                            <td>
                                                @if($field->sumber_data)
                                                    <small class="text-muted">{{ $field->sumber_data }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($field->wajib)
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @else
                                                    <i class="bi bi-circle text-muted"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-ui-checks fs-1 d-block mb-2"></i>
                            Belum ada field untuk template ini.
                            <a href="{{ route('admin.template-dokumen.fields', $templateDokumen->hashid) }}" class="d-block mt-2">Tambah field sekarang</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    @if($templateDokumen->deskripsi)
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">DESKRIPSI</label>
                            <p class="mb-0">{{ $templateDokumen->deskripsi }}</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">OPSI TAMPILAN</label>
                        <div class="d-flex flex-wrap gap-2">
                            @if($templateDokumen->tampilkan_kop)
                                <span class="badge bg-light text-dark"><i class="bi bi-check me-1"></i>Kop Surat</span>
                            @endif
                            @if($templateDokumen->tampilkan_logo)
                                <span class="badge bg-light text-dark"><i class="bi bi-check me-1"></i>Logo</span>
                            @endif
                            @if($templateDokumen->tampilkan_ttd_digital)
                                <span class="badge bg-light text-dark"><i class="bi bi-check me-1"></i>TTD Digital</span>
                            @endif
                            @if($templateDokumen->tampilkan_stempel)
                                <span class="badge bg-light text-dark"><i class="bi bi-check me-1"></i>Stempel</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted small mb-1">TIMESTAMP</label>
                        <p class="mb-0 small">
                            Dibuat: {{ $templateDokumen->created_at->format('d/m/Y H:i') }}<br>
                            Diupdate: {{ $templateDokumen->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pejabat Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Pejabat Penandatangan</h5>
                </div>
                <div class="card-body">
                    @if($templateDokumen->pejabat1)
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">PEJABAT 1</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $templateDokumen->pejabat1->nama }}</div>
                                    <small class="text-muted">{{ $templateDokumen->pejabat_1_label ?? $templateDokumen->pejabat1->jabatan }}</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">PEJABAT 1</label>
                            <p class="text-muted mb-0">Belum diatur</p>
                        </div>
                    @endif

                    @if($templateDokumen->pejabat2)
                        <div class="mb-0">
                            <label class="form-label text-muted small mb-1">PEJABAT 2</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-person text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $templateDokumen->pejabat2->nama }}</div>
                                    <small class="text-muted">{{ $templateDokumen->pejabat_2_label ?? $templateDokumen->pejabat2->jabatan }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Placeholders Reference -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-code-slash me-2"></i>Placeholder Tersedia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-2">DARI FIELD</label>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($templateDokumen->fields as $field)
                                <code class="bg-light px-2 py-1 rounded small">{!! '{'.$field->kode_field.'}' !!}</code>
                            @empty
                                <span class="text-muted small">Belum ada field</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-muted small mb-2">SISTEM</label>
                        <div class="d-flex flex-wrap gap-1">
                            <code class="bg-light px-2 py-1 rounded small">{tanggal_sekarang}</code>
                            <code class="bg-light px-2 py-1 rounded small">{hari_ini}</code>
                            <code class="bg-light px-2 py-1 rounded small">{bulan_romawi}</code>
                            <code class="bg-light px-2 py-1 rounded small">{tahun}</code>
                            <code class="bg-light px-2 py-1 rounded small">{nama_institusi}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
