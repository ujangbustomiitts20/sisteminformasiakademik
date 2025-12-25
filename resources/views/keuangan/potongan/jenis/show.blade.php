@extends('layouts.app')

@section('title', 'Detail Jenis Potongan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Detail Jenis Potongan</h5>
                    <div>
                        <a href="{{ route('jenis-potongan.edit', $jenisPotongan) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kode</th>
                            <td><code>{{ $jenisPotongan->kode }}</code></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $jenisPotongan->nama }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>
                                @php
                                    $badgeKategori = match($jenisPotongan->kategori) {
                                        'diskon' => 'info',
                                        'potongan_khusus' => 'warning',
                                        'promo' => 'danger',
                                        'keringanan' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeKategori }}">{{ $jenisPotongan->kategori_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $jenisPotongan->deskripsi ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Nilai</th>
                            <td>{{ $jenisPotongan->tipe_nilai_label }}</td>
                        </tr>
                        <tr>
                            <th>Nilai Default</th>
                            <td><strong class="text-success">{{ $jenisPotongan->nilai_default_label }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nilai Maksimal</th>
                            <td>{{ $jenisPotongan->nilai_max ? 'Rp ' . number_format($jenisPotongan->nilai_max, 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Stackable</th>
                            <td>
                                @if($jenisPotongan->is_stackable)
                                    <span class="badge bg-success">Ya</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Prioritas</th>
                            <td>{{ $jenisPotongan->prioritas }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($jenisPotongan->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $jenisPotongan->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('jenis-potongan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <form action="{{ route('jenis-potongan.toggle-status', $jenisPotongan) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $jenisPotongan->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $jenisPotongan->is_active ? 'ban' : 'check' }} me-1"></i>
                                {{ $jenisPotongan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <!-- Periode Diskon Terkait -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Periode Diskon Terkait</h6>
                </div>
                <div class="card-body">
                    @if($jenisPotongan->periodeDiskon->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jenisPotongan->periodeDiskon as $periode)
                                        <tr>
                                            <td><code>{{ $periode->kode }}</code></td>
                                            <td>{{ $periode->nama }}</td>
                                            <td>{{ $periode->tanggal_mulai->format('d M Y') }} - {{ $periode->tanggal_selesai->format('d M Y') }}</td>
                                            <td><span class="badge bg-{{ $periode->status_badge }}">{{ $periode->status_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada periode diskon</p>
                    @endif
                </div>
            </div>

            <!-- Potongan Mahasiswa Terkait -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Potongan Mahasiswa Terkait</h6>
                </div>
                <div class="card-body">
                    @if($jenisPotongan->potonganMahasiswa->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Mahasiswa</th>
                                        <th>Nilai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jenisPotongan->potonganMahasiswa as $potongan)
                                        <tr>
                                            <td><code>{{ $potongan->kode }}</code></td>
                                            <td>{{ $potongan->mahasiswa->nama ?? '-' }}</td>
                                            <td>{{ $potongan->nilai_label }}</td>
                                            <td><span class="badge bg-{{ $potongan->status_badge }}">{{ $potongan->status_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada potongan mahasiswa</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
