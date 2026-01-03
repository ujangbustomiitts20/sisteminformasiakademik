@extends('layouts.app')

@section('title', 'Konfigurasi Cetak')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Konfigurasi Cetak</h1>
            <p class="text-muted mb-0">Kelola pengaturan template cetakan dokumen</p>
        </div>
        <div>
            <form action="{{ route('konfigurasi-cetak.reset-all') }}" method="POST" class="d-inline" 
                  onsubmit="return confirm('Reset semua konfigurasi ke default?')">
                @csrf
                <button type="submit" class="btn btn-outline-warning">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Semua ke Default
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @foreach($konfigurasi as $config)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 {{ !$config->is_active ? 'border-secondary opacity-75' : '' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-pdf me-2"></i>{{ $config->nama }}
                    </h5>
                    @if(!$config->is_active)
                        <span class="badge bg-secondary">Nonaktif</span>
                    @else
                        <span class="badge bg-success">Aktif</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td width="40%">Kode</td>
                            <td><code>{{ $config->kode }}</code></td>
                        </tr>
                        <tr>
                            <td>Ukuran</td>
                            <td>{{ strtoupper($config->ukuran_kertas) }} ({{ ucfirst($config->orientasi) }})</td>
                        </tr>
                        <tr>
                            <td>Margin</td>
                            <td>{{ $config->margin_top }}/{{ $config->margin_right }}/{{ $config->margin_bottom }}/{{ $config->margin_left }} mm</td>
                        </tr>
                        <tr>
                            <td>Kop Surat</td>
                            <td>
                                @if($config->tampilkan_kop)
                                    <span class="badge bg-success">Ya</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Tanda Tangan</td>
                            <td>
                                @if($config->tampilkan_ttd)
                                    <span class="badge bg-success">{{ $config->jabatan_ttd ?: 'Ya' }}</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2">
                        <a href="{{ route('konfigurasi-cetak.edit', $config->hashid) }}" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('konfigurasi-cetak.preview-pdf', $config->hashid) }}" class="btn btn-outline-info btn-sm" target="_blank" title="Preview PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <a href="{{ route('konfigurasi-cetak.preview', $config->hashid) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Preview HTML">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form action="{{ route('konfigurasi-cetak.reset', $config->hashid) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Reset konfigurasi ini ke default?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning btn-sm" title="Reset ke Default">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($konfigurasi->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-file-earmark-x display-1 text-muted"></i>
            <h4 class="mt-3">Belum Ada Konfigurasi</h4>
            <p class="text-muted">Jalankan seeder untuk menambahkan konfigurasi default.</p>
            <code>php artisan db:seed --class=KonfigurasiCetakSeeder</code>
        </div>
    </div>
    @endif
</div>
@endsection
