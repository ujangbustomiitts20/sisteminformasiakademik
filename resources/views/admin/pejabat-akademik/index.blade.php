@extends('layouts.app')

@section('title', 'Konfigurasi Pejabat Akademik')

@section('content')
<div class="page-title">
    <h4>Konfigurasi Pejabat Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pejabat Akademik</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Buat Pejabat Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pejabat-akademik.quick-create') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" id="roleSelect" class="form-select" required>
                                <option value="">Pilih Role</option>
                                <option value="kaprodi">Ketua Prodi</option>
                                <option value="dekan">Dekan</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="prodiField" style="display:none;">
                            <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <select name="program_studi_id" class="form-select">
                                <option value="">Pilih Prodi</option>
                                @foreach($fakultasList as $fak)
                                    <optgroup label="{{ $fak->nama }}">
                                        @foreach($fak->programStudi as $prodi)
                                        @php
                                            $hasKaprodi = $kaprodis->contains(function($k) use ($prodi) {
                                                return $k->dosen && $k->dosen->program_studi_id == $prodi->id;
                                            });
                                        @endphp
                                        <option value="{{ $prodi->id }}" {{ $hasKaprodi ? 'disabled' : '' }}>
                                            {{ $prodi->nama }} {{ $hasKaprodi ? '✓ (Sudah ada Kaprodi)' : '' }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="fakultasField" style="display:none;">
                            <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                            <select name="fakultas_id" class="form-select">
                                <option value="">Pilih Fakultas</option>
                                @foreach($fakultasList as $fak)
                                <option value="{{ $fak->id }}" {{ $fak->has_dekan ? 'disabled' : '' }}>
                                    {{ $fak->nama }} {{ $fak->has_dekan ? '✓ (Sudah ada Dekan)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                            <select name="dosen_id" id="dosenSelect" class="form-select" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosenAvailable as $dosen)
                                <option value="{{ $dosen->id }}">
                                    {{ $dosen->nidn }} - {{ $dosen->nama }} ({{ $dosen->programStudi->nama ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i> Angkat
                            </button>
                        </div>
                    </div>
                    @if($dosenAvailable->isEmpty())
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Tidak ada dosen yang tersedia. Semua dosen sudah memiliki jabatan (kaprodi/dekan).
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daftar Kaprodi -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Ketua Program Studi</h5>
                <span class="badge bg-light text-primary">{{ $kaprodis->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIDN</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Fakultas</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kaprodis as $kaprodi)
                            <tr>
                                <td><code>{{ $kaprodi->dosen->nidn ?? '-' }}</code></td>
                                <td>
                                    <strong>{{ $kaprodi->dosen->nama ?? $kaprodi->name }}</strong><br>
                                    <small class="text-muted">{{ $kaprodi->email }}</small>
                                </td>
                                <td>{{ $kaprodi->dosen->programStudi->nama ?? '-' }}</td>
                                <td>{{ $kaprodi->dosen->programStudi->fakultas->nama ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.pejabat-akademik.remove-kaprodi', $kaprodi) }}" method="POST" 
                                          onsubmit="return confirm('Hapus kaprodi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada Kaprodi terdaftar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Dekan -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Dekan Fakultas</h5>
                <span class="badge bg-light text-success">{{ $dekans->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIDN</th>
                                <th>Nama</th>
                                <th>Fakultas</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dekans as $dekan)
                            <tr>
                                <td><code>{{ $dekan->dosen->nidn ?? '-' }}</code></td>
                                <td>
                                    <strong>{{ $dekan->dosen->nama ?? $dekan->name }}</strong><br>
                                    <small class="text-muted">{{ $dekan->email }}</small>
                                </td>
                                <td>{{ $dekan->dosen->programStudi->fakultas->nama ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.pejabat-akademik.remove-dekan', $dekan) }}" method="POST"
                                          onsubmit="return confirm('Hapus dekan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada Dekan terdaftar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dosen Available untuk diassign -->
@if($dosenAvailable->count() > 0)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Dosen yang Bisa Di-assign</h5>
        <span class="badge bg-secondary">{{ $dosenAvailable->count() }} dosen</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($dosenAvailable->take(12) as $dosen)
            @php
                // Cek apakah prodi dosen sudah ada kaprodi
                $prodiHasKaprodi = $kaprodis->contains(function($k) use ($dosen) {
                    return $k->dosen && $k->dosen->program_studi_id == $dosen->program_studi_id;
                });
                // Cek apakah fakultas dosen sudah ada dekan
                $fakultasHasDekan = false;
                if ($dosen->programStudi && $dosen->programStudi->fakultas) {
                    $fakultasHasDekan = $dekans->contains(function($d) use ($dosen) {
                        return $d->dosen && $d->dosen->programStudi && 
                               $d->dosen->programStudi->fakultas_id == $dosen->programStudi->fakultas_id;
                    });
                }
            @endphp
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $dosen->nama }}</h6>
                                <small class="text-muted d-block">NIDN: {{ $dosen->nidn }}</small>
                                <small class="text-muted">{{ $dosen->programStudi->nama ?? '-' }}</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                @if(!$prodiHasKaprodi && $dosen->program_studi_id)
                                <form action="{{ route('admin.pejabat-akademik.store-kaprodi') }}" method="POST" class="d-inline" onsubmit="return confirm('Jadikan {{ $dosen->nama }} sebagai Kaprodi {{ $dosen->programStudi->nama ?? '' }}?')">
                                    @csrf
                                    <input type="hidden" name="dosen_id" value="{{ $dosen->id }}">
                                    <input type="hidden" name="program_studi_id" value="{{ $dosen->program_studi_id }}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm" title="Jadikan Kaprodi">
                                        <i class="bi bi-person-badge"></i> Kaprodi
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Prodi sudah ada kaprodi">
                                    <i class="bi bi-person-badge"></i> Kaprodi
                                </button>
                                @endif

                                @if(!$fakultasHasDekan && $dosen->programStudi && $dosen->programStudi->fakultas)
                                <form action="{{ route('admin.pejabat-akademik.store-dekan') }}" method="POST" class="d-inline" onsubmit="return confirm('Jadikan {{ $dosen->nama }} sebagai Dekan {{ $dosen->programStudi->fakultas->nama ?? '' }}?')">
                                    @csrf
                                    <input type="hidden" name="dosen_id" value="{{ $dosen->id }}">
                                    <input type="hidden" name="fakultas_id" value="{{ $dosen->programStudi->fakultas_id }}">
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Jadikan Dekan">
                                        <i class="bi bi-mortarboard"></i> Dekan
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Fakultas sudah ada dekan">
                                    <i class="bi bi-mortarboard"></i> Dekan
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($dosenAvailable->count() > 12)
        <p class="text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Menampilkan 12 dari {{ $dosenAvailable->count() }} dosen. Gunakan form di atas untuk memilih dosen lainnya.</p>
        @endif
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    const prodiField = document.getElementById('prodiField');
    const fakultasField = document.getElementById('fakultasField');
    
    if (this.value === 'kaprodi') {
        prodiField.style.display = 'block';
        fakultasField.style.display = 'none';
        prodiField.querySelector('select').required = true;
        fakultasField.querySelector('select').required = false;
    } else if (this.value === 'dekan') {
        prodiField.style.display = 'none';
        fakultasField.style.display = 'block';
        prodiField.querySelector('select').required = false;
        fakultasField.querySelector('select').required = true;
    } else {
        prodiField.style.display = 'none';
        fakultasField.style.display = 'none';
        prodiField.querySelector('select').required = false;
        fakultasField.querySelector('select').required = false;
    }
});
</script>
@endpush
