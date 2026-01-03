@extends('layouts.app')

@section('title', 'Kurikulum Prodi')

@section('content')
<div class="page-title">
    <h4>Kurikulum Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Kurikulum</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Kurikulum - {{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kurikulum</th>
                        <th>Tahun</th>
                        <th>SKS Wajib</th>
                        <th>SKS Pilihan</th>
                        <th>Total MK</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulums as $kurikulum)
                    <tr>
                        <td><strong>{{ $kurikulum->kode }}</strong></td>
                        <td>{{ $kurikulum->nama }}</td>
                        <td>{{ $kurikulum->tahun_mulai }} - {{ $kurikulum->tahun_selesai ?? 'Sekarang' }}</td>
                        <td class="text-center">{{ $kurikulum->total_sks_wajib ?? 0 }}</td>
                        <td class="text-center">{{ $kurikulum->total_sks_pilihan ?? 0 }}</td>
                        <td class="text-center">{{ $kurikulum->mata_kuliah_count }}</td>
                        <td>
                            @if($kurikulum->is_aktif)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('kaprodi.kurikulum.show', $kurikulum) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data kurikulum</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
