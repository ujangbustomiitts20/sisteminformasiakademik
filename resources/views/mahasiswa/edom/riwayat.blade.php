@extends('layouts.app')

@section('title', 'Riwayat EDOM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Riwayat Evaluasi Dosen</h1>
            <p class="text-muted mb-0">Daftar evaluasi dosen yang telah Anda isi</p>
        </div>
        <a href="{{ route('mahasiswa.edom.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($riwayat->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Belum ada riwayat evaluasi dosen.</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen</th>
                            <th>Tanggal Pengisian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $i => $r)
                        <tr>
                            <td>{{ $riwayat->firstItem() + $i }}</td>
                            <td>{{ $r->periodeEdom->nama ?? '-' }}</td>
                            <td>{{ $r->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                            <td>{{ $r->dosen->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal_isi)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $riwayat->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
