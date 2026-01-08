@extends('layouts.app')

@section('title', 'Kelola Dosen')

@section('content')
<div class="page-title">
    <h4>Kelola Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dosen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>Daftar Dosen</span>
        <a href="{{ route('dosen.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Dosen
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dosenTable" class="table table-hover table-striped" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="120">NIDN</th>
                        <th>Nama Dosen</th>
                        <th>Program Studi</th>
                        <th width="100">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dosen as $index => $d)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $d->nidn }}</code></td>
                        <td>
                            <strong>{{ $d->nama }}</strong>
                            @if($d->email)
                            <br><small class="text-muted">{{ $d->email }}</small>
                            @endif
                        </td>
                        <td>{{ $d->programStudi->nama ?? '-' }}</td>
                        <td>
                            @if($d->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('dosen.show', $d) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.index', $d) }}" class="btn btn-outline-primary" title="Kepegawaian">
                                    <i class="bi bi-person-badge"></i>
                                </a>
                                <a href="{{ route('dosen.edit', $d) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('dosen.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dosen ini?')">
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
    $('#dosenTable').DataTable({
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
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 5] }
        ]
    });
});
</script>
@endpush
