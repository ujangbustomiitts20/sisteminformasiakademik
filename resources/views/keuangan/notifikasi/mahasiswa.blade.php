@extends('layouts.app')

@section('title', 'Notifikasi Saya')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Notifikasi</h1>
            <p class="text-muted">Pemberitahuan terkait tagihan dan cicilan Anda</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            @if($notifikasi->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada notifikasi</h5>
                    <p class="text-muted mb-0">Anda akan menerima notifikasi terkait tagihan dan cicilan di sini.</p>
                </div>
            </div>
            @else
            <div class="list-group">
                @foreach($notifikasi as $item)
                <div class="list-group-item {{ is_null($item->read_at) ? 'list-group-item-warning' : '' }}">
                    <div class="d-flex w-100 justify-content-between">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                @switch($item->jenis)
                                    @case('reminder')
                                        <div class="bg-info rounded-circle p-2">
                                            <i class="fas fa-bell text-white"></i>
                                        </div>
                                        @break
                                    @case('tagihan_jatuh_tempo')
                                        <div class="bg-danger rounded-circle p-2">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                        @break
                                    @case('cicilan_jatuh_tempo')
                                        <div class="bg-warning rounded-circle p-2">
                                            <i class="fas fa-calendar text-white"></i>
                                        </div>
                                        @break
                                    @case('denda')
                                        <div class="bg-dark rounded-circle p-2">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                        @break
                                    @case('pembayaran_berhasil')
                                        <div class="bg-success rounded-circle p-2">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                        @break
                                    @default
                                        <div class="bg-secondary rounded-circle p-2">
                                            <i class="fas fa-info text-white"></i>
                                        </div>
                                @endswitch
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $item->judul }}</h6>
                                <p class="mb-1">{{ $item->pesan }}</p>
                                @if($item->reference_type === 'App\\Models\\Tagihan' && $item->reference_id)
                                <a href="{{ route('tagihan.mahasiswa') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-eye"></i> Lihat Tagihan
                                </a>
                                @endif
                                @if($item->reference_type === 'App\\Models\\Cicilan' && $item->reference_id)
                                <a href="{{ route('cicilan.tracking') }}" class="btn btn-sm btn-outline-success mt-2">
                                    <i class="fas fa-eye"></i> Lihat Cicilan
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            @if(is_null($item->read_at))
                            <br><span class="badge bg-warning">Baru</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($notifikasi->hasPages())
            <div class="mt-4">
                {{ $notifikasi->withQueryString()->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
