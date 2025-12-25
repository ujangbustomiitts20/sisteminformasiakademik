@extends('layouts.app')

@section('title', 'Detail Mata Kuliah')

@section('content')
<div class="page-title">
    <h4>Detail Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a></li>
            <li class="breadcrumb-item active">{{ $mataKuliah->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-book me-2"></i>Informasi Mata Kuliah
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 200px;">Kode</th>
                        <td><code>{{ $mataKuliah->kode }}</code></td>
                    </tr>
                    <tr>
                        <th>Nama Mata Kuliah</th>
                        <td>{{ $mataKuliah->nama }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>{{ $mataKuliah->programStudi->nama }}</td>
                    </tr>
                    <tr>
                        <th>Fakultas</th>
                        <td>{{ $mataKuliah->programStudi->fakultas->nama }}</td>
                    </tr>
                    <tr>
                        <th>SKS</th>
                        <td><span class="badge bg-primary">{{ $mataKuliah->sks }} SKS</span></td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td>Semester {{ $mataKuliah->semester }}</td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td>
                            @if($mataKuliah->jenis == 'Wajib')
                            <span class="badge bg-danger">Wajib</span>
                            @else
                            <span class="badge bg-info">Pilihan</span>
                            @endif
                        </td>
                    </tr>
                    @if($mataKuliah->deskripsi)
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $mataKuliah->deskripsi }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                @if(auth()->user()->role == 'admin')
                <a href="{{ route('mata-kuliah.edit', $mataKuliah) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar3 me-2"></i>Jadwal Kuliah
            </div>
            <div class="card-body p-0">
                @forelse($mataKuliah->jadwalKuliah as $jadwal)
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <h6 class="mb-1">{{ $jadwal->kelas }}</h6>
                    <div class="small text-muted">
                        <i class="bi bi-person me-1"></i>{{ $jadwal->dosen->nama ?? '-' }}<br>
                        <i class="bi bi-clock me-1"></i>{{ $jadwal->hari }}, {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Belum ada jadwal kuliah</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
