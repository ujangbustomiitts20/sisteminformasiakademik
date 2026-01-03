@extends('layouts.app')

@section('title', 'Input Nilai Seleksi')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Input Nilai Seleksi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.seleksi.index') }}">Seleksi</a></li>
                <li class="breadcrumb-item active">Input Nilai</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pmb.seleksi.input-nilai') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Gelombang PMB</label>
                        <select name="gelombang" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ request('gelombang') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jalur Seleksi</label>
                        <select name="jalur" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}" {{ request('jalur') == $jalur->id ? 'selected' : '' }}>
                                    {{ $jalur->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('pmb.seleksi.input-nilai') }}" class="btn btn-secondary d-block">Reset Filter</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('gelombang') && $peserta->count() > 0)
    <form action="{{ route('pmb.seleksi.store-nilai') }}" method="POST">
        @csrf
        <input type="hidden" name="gelombang_id" value="{{ request('gelombang') }}">
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2"></i>Input Nilai Peserta</span>
                <span class="badge bg-primary">{{ $peserta->count() }} Peserta</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No. Pendaftaran</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                @foreach($komponenNilai as $komponen)
                                    <th class="text-center">
                                        {{ $komponen['label'] }}
                                        <br><small class="text-muted">(Bobot: {{ $komponen['bobot'] }}%)</small>
                                    </th>
                                @endforeach
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peserta as $index => $camaba)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $camaba->no_pendaftaran }}</td>
                                <td>{{ $camaba->nama_lengkap }}</td>
                                <td>{{ $camaba->programStudi->nama ?? '-' }}</td>
                                @foreach($komponenNilai as $key => $komponen)
                                    @php
                                        $nilaiExist = $camaba->nilaiSeleksi->where('komponen_nilai', $key)->first();
                                    @endphp
                                    <td class="text-center">
                                        <input type="hidden" name="nilai[{{ $camaba->id }}][{{ $key }}][komponen]" value="{{ $key }}">
                                        <input type="hidden" name="nilai[{{ $camaba->id }}][{{ $key }}][bobot]" value="{{ $komponen['bobot'] }}">
                                        <input type="number" 
                                               name="nilai[{{ $camaba->id }}][{{ $key }}][nilai]" 
                                               class="form-control form-control-sm text-center nilai-input"
                                               style="width: 70px; display: inline-block;"
                                               min="0" max="100" step="0.01"
                                               value="{{ old("nilai.{$camaba->id}.{$key}.nilai", $nilaiExist->nilai ?? '') }}"
                                               data-camaba="{{ $camaba->id }}"
                                               data-bobot="{{ $komponen['bobot'] }}">
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <strong class="total-nilai" id="total-{{ $camaba->id }}">
                                        {{ $camaba->nilaiSeleksi->sum('nilai_akhir') ?? 0 }}
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('pmb.seleksi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Simpan Nilai
                    </button>
                </div>
            </div>
        </div>
    </form>
    @elseif(request('gelombang'))
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people fs-1 text-muted mb-3"></i>
            <p class="text-muted">Tidak ada peserta dengan kriteria yang dipilih</p>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-funnel fs-1 text-muted mb-3"></i>
            <p class="text-muted">Silakan pilih gelombang PMB terlebih dahulu</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nilaiInputs = document.querySelectorAll('.nilai-input');
        
        nilaiInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateTotal(this.dataset.camaba);
            });
        });

        function calculateTotal(camabaId) {
            const inputs = document.querySelectorAll(`input[data-camaba="${camabaId}"]`);
            let total = 0;
            
            inputs.forEach(input => {
                const nilai = parseFloat(input.value) || 0;
                const bobot = parseFloat(input.dataset.bobot) || 0;
                total += (nilai * bobot / 100);
            });
            
            document.getElementById(`total-${camabaId}`).textContent = total.toFixed(2);
        }
    });
</script>
@endpush
