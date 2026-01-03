@extends('layouts.app')

@section('title', 'Kurikulum')

@section('content')
<div class="page-title">
    <h4>Kurikulum - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Kurikulum</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Kurikulum</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kurikulum</th>
                        <th>Program Studi</th>
                        <th>Tahun</th>
                        <th class="text-center">Jumlah MK</th>
                        <th class="text-center">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulums as $kurikulum)
                    <tr>
                        <td><strong>{{ $kurikulum->nama }}</strong></td>
                        <td>{{ $kurikulum->programStudi->nama ?? '-' }}</td>
                        <td>{{ $kurikulum->tahun_mulai ?? '-' }}{{ $kurikulum->tahun_selesai ? ' - ' . $kurikulum->tahun_selesai : '' }}</td>
                        <td class="text-center"><span class="badge bg-primary">{{ $kurikulum->mata_kuliah_count }}</span></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $kurikulum->is_aktif ? 'success' : 'secondary' }}">
                                {{ $kurikulum->is_aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.kurikulum.show', $kurikulum) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Tidak ada data kurikulum</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $kurikulums->links() }}
    </div>
</div>
@endsection
