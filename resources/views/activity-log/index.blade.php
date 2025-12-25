@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Activity Log</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Log</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('activity-log.export', request()->all()) }}" class="btn btn-outline-success me-2">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearModal">
                <i class="bi bi-trash me-1"></i>Hapus Log Lama
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('activity-log.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">Semua</option>
                            @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Waktu</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Deskripsi</th>
                            <th>IP Address</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <small>{{ $log->created_at->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                <a href="#">{{ $log->user->name }}</a>
                                @else
                                <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action_badge }}">{{ ucfirst($log->action) }}</span>
                            </td>
                            <td>
                                {{ Str::limit($log->description, 60) }}
                                @if($log->model_type)
                                <br><small class="text-muted">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</small>
                                @endif
                            </td>
                            <td><code class="small">{{ $log->ip_address ?? '-' }}</code></td>
                            <td>
                                <a href="{{ route('activity-log.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-journal-x display-4 text-muted"></i>
                                <p class="text-muted mt-2">Tidak ada activity log</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Clear Log Modal -->
<div class="modal fade" id="clearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('activity-log.clear') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Log Lama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Hapus semua log aktivitas yang lebih dari:</p>
                    <select name="days" class="form-select">
                        <option value="7">7 hari</option>
                        <option value="30" selected>30 hari</option>
                        <option value="60">60 hari</option>
                        <option value="90">90 hari</option>
                        <option value="180">180 hari</option>
                        <option value="365">1 tahun</option>
                    </select>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Tindakan ini tidak dapat dibatalkan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Log</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
