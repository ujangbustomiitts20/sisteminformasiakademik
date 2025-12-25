@extends('layouts.app')

@section('title', 'Manajemen Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Manajemen Notifikasi</h1>
            <p class="text-muted">Kelola notifikasi tagihan dan cicilan</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ number_format($stats['belum_dibaca']) }}</h3>
                    <small>Belum Dibaca</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ number_format($stats['reminder']) }}</h3>
                    <small>Reminder</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ number_format($stats['tagihan_jatuh_tempo'] + $stats['cicilan_jatuh_tempo']) }}</h3>
                    <small>Jatuh Tempo</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ number_format($stats['denda']) }}</h3>
                    <small>Denda</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ number_format($stats['pembayaran']) }}</h3>
                    <small>Pembayaran</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Jenis Notifikasi</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="reminder" {{ request('jenis') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                        <option value="tagihan_jatuh_tempo" {{ request('jenis') == 'tagihan_jatuh_tempo' ? 'selected' : '' }}>Tagihan Jatuh Tempo</option>
                        <option value="cicilan_jatuh_tempo" {{ request('jenis') == 'cicilan_jatuh_tempo' ? 'selected' : '' }}>Cicilan Jatuh Tempo</option>
                        <option value="denda" {{ request('jenis') == 'denda' ? 'selected' : '' }}>Denda</option>
                        <option value="pembayaran_berhasil" {{ request('jenis') == 'pembayaran_berhasil' ? 'selected' : '' }}>Pembayaran</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Dibaca</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-between mb-3">
        <div>
            <form action="{{ route('notifikasi.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </button>
            </form>
        </div>
        <div>
            <button type="button" class="btn btn-danger btn-sm" id="btnBulkDelete" disabled>
                <i class="fas fa-trash"></i> Hapus Terpilih
            </button>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Mahasiswa</th>
                            <th>Jenis</th>
                            <th>Judul</th>
                            <th>Pesan</th>
                            <th>Channel</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifikasi as $item)
                        <tr class="{{ is_null($item->read_at) ? 'table-warning' : '' }}">
                            <td>
                                <input type="checkbox" class="form-check-input select-item" value="{{ $item->id }}">
                            </td>
                            <td>
                                @if($item->mahasiswa)
                                    <strong>{{ $item->mahasiswa->nama }}</strong><br>
                                    <small class="text-muted">{{ $item->mahasiswa->nim ?? '-' }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @switch($item->jenis)
                                    @case('reminder')
                                        <span class="badge bg-info">Reminder</span>
                                        @break
                                    @case('tagihan_jatuh_tempo')
                                        <span class="badge bg-danger">Tagihan Jatuh Tempo</span>
                                        @break
                                    @case('cicilan_jatuh_tempo')
                                        <span class="badge bg-warning text-dark">Cicilan Jatuh Tempo</span>
                                        @break
                                    @case('denda')
                                        <span class="badge bg-dark">Denda</span>
                                        @break
                                    @case('pembayaran_berhasil')
                                        <span class="badge bg-success">Pembayaran</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $item->jenis }}</span>
                                @endswitch
                            </td>
                            <td>
                                <strong>{{ $item->judul }}</strong>
                            </td>
                            <td>
                                <span title="{{ $item->pesan }}">
                                    {{ Str::limit($item->pesan, 50) }}
                                </span>
                            </td>
                            <td>
                                @switch($item->channel)
                                    @case('email')
                                        <span class="badge bg-primary"><i class="fas fa-envelope"></i> Email</span>
                                        @break
                                    @case('whatsapp')
                                        <span class="badge bg-success"><i class="fab fa-whatsapp"></i> WhatsApp</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary"><i class="fas fa-database"></i> Database</span>
                                @endswitch
                            </td>
                            <td>
                                {{ $item->created_at->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @switch($item->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @break
                                    @case('sent')
                                        <span class="badge bg-info">Terkirim</span>
                                        @if($item->sent_at)
                                        <br><small>{{ $item->sent_at->format('d/m H:i') }}</small>
                                        @endif
                                        @break
                                    @case('read')
                                        <span class="badge bg-success">Dibaca</span>
                                        @if($item->read_at)
                                        <br><small>{{ $item->read_at->format('d/m H:i') }}</small>
                                        @endif
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger">Gagal</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if(is_null($item->read_at))
                                    <button type="button" class="btn btn-sm btn-outline-success btn-mark-read" 
                                            data-id="{{ $item->id }}" title="Tandai Dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    <form action="{{ route('notifikasi.destroy', $item->id) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada notifikasi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notifikasi->hasPages())
        <div class="card-footer">
            {{ $notifikasi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Bulk Delete Form -->
<form id="bulkDeleteForm" action="{{ route('notifikasi.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.select-item');
    const btnBulkDelete = document.getElementById('btnBulkDelete');

    selectAll.addEventListener('change', function() {
        selectItems.forEach(item => item.checked = this.checked);
        updateBulkDeleteButton();
    });

    selectItems.forEach(item => {
        item.addEventListener('change', updateBulkDeleteButton);
    });

    function updateBulkDeleteButton() {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        btnBulkDelete.disabled = checkedItems.length === 0;
    }

    // Bulk delete
    btnBulkDelete.addEventListener('click', function() {
        if (!confirm('Hapus notifikasi yang dipilih?')) return;

        const checkedItems = document.querySelectorAll('.select-item:checked');
        const ids = Array.from(checkedItems).map(item => item.value);
        
        document.getElementById('bulkDeleteIds').value = JSON.stringify(ids);
        document.getElementById('bulkDeleteForm').submit();
    });

    // Mark as read
    document.querySelectorAll('.btn-mark-read').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`/admin/notifikasi/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
