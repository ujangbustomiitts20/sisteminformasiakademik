@extends('layouts.app')

@section('title', 'Detail Activity Log')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Activity Log</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activity-log.index') }}">Activity Log</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Informasi Log
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 150px;">ID</td>
                            <td>: #{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu</td>
                            <td>: {{ $log->created_at->format('d F Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">User</td>
                            <td>: {{ $log->user ? $log->user->name . ' (' . $log->user->email . ')' : 'System' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Action</td>
                            <td>: <span class="badge bg-{{ $log->action_badge }}">{{ ucfirst($log->action) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Model</td>
                            <td>: {{ $log->model_type ? class_basename($log->model_type) . ' #' . $log->model_id : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deskripsi</td>
                            <td>: {{ $log->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">IP Address</td>
                            <td>: <code>{{ $log->ip_address ?? '-' }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($log->user_agent)
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-globe me-2"></i>User Agent
                </div>
                <div class="card-body">
                    <code class="small">{{ $log->user_agent }}</code>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-6">
            @if($log->old_values)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-arrow-left-circle me-2"></i>Old Values
                </div>
                <div class="card-body">
                    <pre class="mb-0"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif
            
            @if($log->new_values)
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-arrow-right-circle me-2"></i>New Values
                </div>
                <div class="card-body">
                    <pre class="mb-0"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            @endif
            
            @if(!$log->old_values && !$log->new_values)
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-file-earmark-x display-4 text-muted"></i>
                    <p class="text-muted mt-2">Tidak ada data perubahan</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
