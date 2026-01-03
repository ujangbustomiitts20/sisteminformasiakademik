@extends('layouts.app')

@section('title', 'Monitoring EDOM')

@section('content')
<div class="page-title">
    <h4>Monitoring EDOM - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">EDOM</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Periode EDOM</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Tahun Akademik</th>
                                <th class="text-center">Status</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodes as $periode)
                            <tr>
                                <td>{{ $periode->nama ?? 'Periode ' . $periode->id }}</td>
                                <td>{{ $periode->tahunAkademik->nama ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $periode->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($periode->status ?? 'Tidak Aktif') }}
                                    </span>
                                </td>
                                <td>{{ $periode->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $periode->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada periode EDOM</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $periodes->links() }}
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Rekap per Program Studi</h6>
            </div>
            <div class="card-body">
                @forelse($rekapPerProdi as $prodi)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span>{{ $prodi->nama }}</span>
                    <span class="badge bg-primary">{{ $prodi->dosen->sum('rekap_edom_count') ?? 0 }} rekap</span>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
