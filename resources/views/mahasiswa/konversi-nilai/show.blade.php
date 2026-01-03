@extends('layouts.app')

@section('title', 'Detail Konversi Nilai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Konversi Nilai</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mahasiswa.konversi-nilai.index') }}">Konversi Nilai</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('mahasiswa.konversi-nilai.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    @php
                        $statusConfig = [
                            'draft' => ['class' => 'secondary', 'icon' => 'file-earmark', 'label' => 'Draft'],
                            'diproses' => ['class' => 'info', 'icon' => 'hourglass-split', 'label' => 'Sedang Diproses'],
                            'disetujui' => ['class' => 'success', 'icon' => 'check-circle', 'label' => 'Disetujui'],
                            'ditolak' => ['class' => 'danger', 'icon' => 'x-circle', 'label' => 'Ditolak'],
                        ];
                        $config = $statusConfig[$pengajuanKonversi->status] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'label' => 'Unknown'];
                    @endphp
                    <div class="display-4 text-{{ $config['class'] }} mb-3">
                        <i class="bi bi-{{ $config['icon'] }}"></i>
                    </div>
                    <h4 class="mb-1">{{ $config['label'] }}</h4>
                    @if($pengajuanKonversi->nomor_pengajuan)
                    <p class="text-muted mb-0">Nomor: {{ $pengajuanKonversi->nomor_pengajuan }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Info Konversi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Konversi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Tanggal Dibuat</td>
                            <td>{{ $pengajuanKonversi->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Mata Kuliah</td>
                            <td>{{ $pengajuanKonversi->detailKonversi->count() }} MK</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total SKS Asal</td>
                            <td>{{ $pengajuanKonversi->detailKonversi->sum('sks_asal') }} SKS</td>
                        </tr>
                        <tr>
                            <td class="text-muted">MK Disetujui</td>
                            <td>{{ $pengajuanKonversi->detailKonversi->where('status', 'disetujui')->count() }} MK</td>
                        </tr>
                        @if($pengajuanKonversi->tanggal_diproses)
                        <tr>
                            <td class="text-muted">Tanggal Diproses</td>
                            <td>{{ $pengajuanKonversi->tanggal_diproses->format('d M Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Asal Perguruan Tinggi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-building me-2"></i>Asal Perguruan Tinggi</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $pengajuanKonversi->universitas_asal ?? '-' }}</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted">NIM Asal</td>
                            <td>{{ $pengajuanKonversi->nim_asal ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td>{{ $pengajuanKonversi->program_studi_asal ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Masuk</td>
                            <td>{{ $pengajuanKonversi->tahun_masuk_asal ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Dokumen Pendukung -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-file-earmark me-2"></i>Dokumen Pendukung</h5>
                </div>
                <div class="card-body">
                    @if($pengajuanKonversi->dokumen_transkrip)
                    <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_transkrip) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Transkrip Nilai
                    </a>
                    @endif
                    @if($pengajuanKonversi->dokumen_silabus)
                    <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_silabus) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Silabus Mata Kuliah
                    </a>
                    @endif
                    @if($pengajuanKonversi->dokumen_pendukung)
                    <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_pendukung) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i> Dokumen Pendukung
                    </a>
                    @endif
                    @if(!$pengajuanKonversi->dokumen_transkrip && !$pengajuanKonversi->dokumen_silabus && !$pengajuanKonversi->dokumen_pendukung)
                    <p class="text-muted mb-0">Tidak ada dokumen</p>
                    @endif
                </div>
            </div>

            @if($pengajuanKonversi->catatan)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $pengajuanKonversi->catatan }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Detail Mata Kuliah -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-list-check me-2"></i>Detail Mata Kuliah</h5>
                </div>
                <div class="card-body">
                    @if($pengajuanKonversi->detailKonversi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode MK Asal</th>
                                    <th>Nama MK Asal</th>
                                    <th class="text-center">SKS</th>
                                    <th class="text-center">Nilai Asal</th>
                                    <th>MK Tujuan (Setara)</th>
                                    <th class="text-center">Nilai Konversi</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuanKonversi->detailKonversi as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->kode_mk_asal }}</td>
                                    <td>{{ $detail->nama_mk_asal }}</td>
                                    <td class="text-center">{{ $detail->sks_asal }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $detail->nilai_asal }}</span>
                                    </td>
                                    <td>
                                        @if($detail->mataKuliah)
                                            {{ $detail->mataKuliah->kode }} - {{ $detail->mataKuliah->nama }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($detail->nilai_konversi)
                                            <span class="badge bg-primary">{{ $detail->nilai_konversi }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = match($detail->status ?? 'menunggu') {
                                                'menunggu' => 'warning',
                                                'disetujui' => 'success',
                                                'ditolak' => 'danger',
                                                'tidak_dikonversi' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $detail->status ?? 'Menunggu')) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($detail->alasan)
                                <tr>
                                    <td colspan="8" class="bg-light">
                                        <small><strong>Catatan:</strong> {{ $detail->alasan }}</small>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td class="text-center"><strong>{{ $pengajuanKonversi->detailKonversi->sum('sks_asal') }}</strong></td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Ringkasan -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="mb-1">{{ $pengajuanKonversi->detailKonversi->count() }}</h3>
                                    <small class="text-muted">Total MK Asal</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-1">{{ $pengajuanKonversi->detailKonversi->where('status', 'disetujui')->count() }}</h3>
                                    <small>MK Disetujui</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    @php
                                        $sksDisetujui = $pengajuanKonversi->detailKonversi->where('status', 'disetujui')->sum('sks_asal');
                                    @endphp
                                    <h3 class="mb-1">{{ $sksDisetujui }}</h3>
                                    <small>SKS Dikonversi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="text-muted mt-3">Belum ada detail mata kuliah</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
