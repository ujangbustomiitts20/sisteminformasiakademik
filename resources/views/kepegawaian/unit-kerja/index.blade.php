@extends('layouts.app')

@section('title', 'Kelola Unit Kerja')

@section('content')
<div class="page-title">
    <h4>Kelola Unit Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Unit Kerja</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-building me-2"></i>Daftar Unit Kerja</span>
        <a href="{{ route('kepegawaian.unit-kerja.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Unit Kerja
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="unitKerjaTable" class="table table-hover table-striped" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="100">Kode</th>
                        <th>Nama Unit Kerja</th>
                        <th>Induk</th>
                        <th width="100">Jml Pegawai</th>
                        <th width="80">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitKerja as $index => $unit)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $unit->kode }}</code></td>
                        <td>
                            <strong>{{ $unit->nama }}</strong>
                            @if($unit->deskripsi)
                            <br><small class="text-muted">{{ Str::limit($unit->deskripsi, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $unit->parent->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $unit->pegawai_count }}</span>
                        </td>
                        <td>
                            @if($unit->is_active)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.unit-kerja.edit', $unit) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kepegawaian.unit-kerja.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus unit kerja ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#unitKerjaTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 6] },
            { searchable: false, targets: [0, 6] }
        ]
    });
});
</script>
@endpush
