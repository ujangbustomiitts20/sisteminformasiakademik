@extends('layouts.app')

@section('title', 'Kelola Tenaga Kependidikan')

@section('content')
<div class="page-title">
    <h4>Kelola Tenaga Kependidikan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Tenaga Kependidikan</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Daftar Tenaga Kependidikan</span>
        <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pegawai
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="pegawaiTable" class="table table-hover table-striped" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="120">NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Unit Kerja</th>
                        <th>Jabatan</th>
                        <th>Jenis</th>
                        <th width="80">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawai as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $p->nip ?? '-' }}</code></td>
                        <td>
                            <strong>{{ $p->nama }}</strong>
                            @if($p->email)
                            <br><small class="text-muted">{{ $p->email }}</small>
                            @endif
                        </td>
                        <td>{{ $p->unitKerja->nama ?? '-' }}</td>
                        <td>{{ $p->namaJabatan->nama ?? $p->jabatan ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $p->jenis_pegawai }}</span></td>
                        <td>
                            @if($p->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @elseif($p->status == 'Cuti')
                            <span class="badge bg-warning">Cuti</span>
                            @elseif($p->status == 'Pensiun')
                            <span class="badge bg-info">Pensiun</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.pegawai.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.pegawai.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kepegawaian.pegawai.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pegawai ini?')">
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
    $('#pegawaiTable').DataTable({
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
            { orderable: false, targets: [0, 7] },
            { searchable: false, targets: [0, 7] }
        ]
    });
});
</script>
@endpush
