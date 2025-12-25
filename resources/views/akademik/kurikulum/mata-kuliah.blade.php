@extends('layouts.app')

@section('title', 'Kelola Mata Kuliah Kurikulum')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Kelola Mata Kuliah</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kurikulum.index') }}">Kurikulum</a></li>
                <li class="breadcrumb-item active">{{ $kurikulum->nama }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kurikulum.show', $kurikulum) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Tambah Mata Kuliah
            </div>
            <div class="card-body">
                <form id="formAddMk">
                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <select name="mata_kuliah_id" id="mata_kuliah_id" class="form-select" required>
                            <option value="">-- Pilih MK --</option>
                            @foreach($availableMk as $mk)
                            <option value="{{ $mk->hashid }}" data-semester="{{ $mk->semester }}" data-sks="{{ $mk->sks }}">
                                {{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester Rekomendasi</label>
                        <select name="semester_rekomendasi" id="semester_rekomendasi" class="form-select" required>
                            @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            <option value="Wajib">Wajib</option>
                            <option value="Wajib Prodi">Wajib Prodi</option>
                            <option value="Pilihan">Pilihan</option>
                            <option value="Pilihan Prodi">Pilihan Prodi</option>
                            <option value="MKU">MKU (Mata Kuliah Umum)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Tambahkan
                    </button>
                </form>

                @if($availableMk->count() == 0)
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Semua mata kuliah dari prodi ini sudah ditambahkan ke kurikulum.
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Statistik
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total MK</span>
                    <strong id="statTotalMk">{{ $kurikulum->mataKuliah->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total SKS</span>
                    <strong id="statTotalSks">{{ $kurikulum->mataKuliah->sum('sks') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list me-2"></i>Daftar Mata Kuliah
            </div>
            <div class="card-body p-0">
                @for($semester = 1; $semester <= 8; $semester++)
                @if($mkBySemester[$semester]->count() > 0)
                <div class="border-bottom">
                    <div class="p-3 bg-light">
                        <strong>Semester {{ $semester }}</strong>
                        <span class="badge bg-primary float-end">{{ $mkBySemester[$semester]->sum('sks') }} SKS</span>
                    </div>
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama MK</th>
                                <th class="text-center">SKS</th>
                                <th>Kategori</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mkBySemester[$semester] as $mk)
                            <tr id="row-{{ $mk->hashid }}">
                                <td><code>{{ $mk->kode }}</code></td>
                                <td>{{ $mk->nama }}</td>
                                <td class="text-center">{{ $mk->sks }}</td>
                                <td>
                                    <span class="badge bg-{{ $mk->pivot->kategori == 'Wajib' ? 'danger' : ($mk->pivot->kategori == 'Pilihan' ? 'info' : ($mk->pivot->kategori == 'MKU' ? 'primary' : 'secondary')) }}">
                                        {{ $mk->pivot->kategori }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMk('{{ $mk->hashid }}', '{{ $mk->nama }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                @endfor

                @if($kurikulum->mataKuliah->count() == 0)
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">Belum ada mata kuliah dalam kurikulum ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-select semester based on MK default semester
document.getElementById('mata_kuliah_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.dataset.semester) {
        document.getElementById('semester_rekomendasi').value = option.dataset.semester;
    }
});

// Add MK
document.getElementById('formAddMk').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("kurikulum.add-mata-kuliah", $kurikulum) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            mata_kuliah_id: formData.get('mata_kuliah_id'),
            semester_rekomendasi: formData.get('semester_rekomendasi'),
            kategori: formData.get('kategori'),
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Gagal menambahkan mata kuliah');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan!');
    });
});

// Delete MK
function deleteMk(mkHashid, mkName) {
    if (!confirm(`Hapus "${mkName}" dari kurikulum?`)) return;
    
    fetch(`{{ url('/kurikulum/' . $kurikulum->hashid . '/mata-kuliah') }}/${mkHashid}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('row-' + mkHashid).remove();
            location.reload();
        } else {
            alert(data.error || 'Gagal menghapus mata kuliah');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan!');
    });
}
</script>
@endpush
