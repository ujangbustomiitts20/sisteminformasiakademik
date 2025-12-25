@extends('layouts.app')

@section('title', 'Backup Database')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Backup Database</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Backup Database</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('backup.cleanup') }}" method="POST" class="d-inline" 
                  onsubmit="return confirm('Hapus backup lama? Hanya 5 backup terbaru yang akan disimpan.')">
                @csrf
                <input type="hidden" name="keep" value="5">
                <button type="submit" class="btn btn-outline-warning">
                    <i class="bi bi-trash me-1"></i>Cleanup
                </button>
            </form>
            <form action="{{ route('backup.create') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary" id="btnBackup">
                    <i class="bi bi-download me-1"></i>Buat Backup Baru
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-archive text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Backup</h6>
                            <h4 class="mb-0">{{ $diskSpace['total_backups'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-hdd text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Ukuran Backup</h6>
                            <h4 class="mb-0">{{ $diskSpace['total_size_formatted'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-hdd-stack text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Disk Tersedia</h6>
                            <h4 class="mb-0">{{ $diskSpace['disk_free_formatted'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-database text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Database</h6>
                            <h4 class="mb-0 text-truncate" style="max-width: 150px;">{{ config('database.connections.mysql.database') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-archive me-2"></i>Daftar Backup</span>
            <span class="badge bg-primary">{{ $backups->count() }} file</span>
        </div>
        <div class="card-body p-0">
            @if($backups->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Tanggal</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $index => $backup)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <i class="bi bi-file-earmark-zip text-warning me-2"></i>
                                <span class="fw-medium">{{ $backup['name'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $backup['size_formatted'] }}</span>
                            </td>
                            <td>
                                <span title="{{ $backup['date'] }}">{{ $backup['date_formatted'] }}</span>
                                <br>
                                <small class="text-muted">{{ $backup['date'] }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('backup.download', $backup['name']) }}" 
                                       class="btn btn-outline-primary" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success" 
                                            title="Restore"
                                            onclick="confirmRestore('{{ $backup['name'] }}')">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            title="Hapus"
                                            onclick="confirmDelete('{{ $backup['name'] }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-archive text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">Belum ada backup</h5>
                <p class="text-muted">Klik tombol "Buat Backup Baru" untuk membuat backup database.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="bi bi-info-circle me-2"></i>Informasi Backup
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="bi bi-check-circle text-success me-2"></i>Tips Backup</h6>
                    <ul class="small text-muted">
                        <li>Lakukan backup secara rutin (minimal seminggu sekali)</li>
                        <li>Simpan backup di lokasi terpisah (cloud storage, external drive)</li>
                        <li>Test restore secara berkala untuk memastikan backup valid</li>
                        <li>Hapus backup lama untuk menghemat ruang disk</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-exclamation-triangle text-warning me-2"></i>Peringatan</h6>
                    <ul class="small text-muted">
                        <li>Restore akan menimpa semua data yang ada</li>
                        <li>Pastikan tidak ada user aktif saat restore</li>
                        <li>Backup sebelum melakukan restore</li>
                        <li>Proses backup/restore mungkin memakan waktu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="restoreForm" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Restore</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>Peringatan!</strong> Restore akan menimpa SEMUA data yang ada di database. 
                        Pastikan Anda sudah membuat backup data terbaru sebelum melanjutkan.
                    </div>
                    <p>Anda yakin ingin me-restore database dari file:</p>
                    <p class="fw-bold" id="restoreFilename"></p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmRestore" required>
                        <label class="form-check-label" for="confirmRestore">
                            Saya mengerti dan ingin melanjutkan restore
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Database
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Hapus Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus file backup:</p>
                    <p class="fw-bold" id="deleteFilename"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Restore confirmation
function confirmRestore(filename) {
    document.getElementById('restoreFilename').textContent = filename;
    document.getElementById('restoreForm').action = '{{ url("backup") }}/' + filename + '/restore';
    document.getElementById('confirmRestore').checked = false;
    new bootstrap.Modal(document.getElementById('restoreModal')).show();
}

// Delete confirmation
function confirmDelete(filename) {
    document.getElementById('deleteFilename').textContent = filename;
    document.getElementById('deleteForm').action = '{{ url("backup") }}/' + filename;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Show loading on backup
document.getElementById('btnBackup')?.closest('form').addEventListener('submit', function() {
    const btn = document.getElementById('btnBackup');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Membuat backup...';
});
</script>
@endpush
