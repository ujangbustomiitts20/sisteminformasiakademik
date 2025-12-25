@extends('layouts.app')

@section('title', 'Buat Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Buat Cicilan Baru</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cicilan.index') }}">Cicilan</a></li>
                    <li class="breadcrumb-item active">Buat</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Cicilan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cicilan.store') }}" method="POST" id="formCicilan">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Tagihan <span class="text-danger">*</span></label>
                            <select name="tagihan_id" id="tagihan_id" class="form-select @error('tagihan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tagihan --</option>
                                @foreach($tagihanList as $t)
                                <option value="{{ $t->id }}" 
                                    data-sisa="{{ $t->sisa_tagihan }}"
                                    data-mahasiswa="{{ $t->mahasiswa->nama ?? '-' }}"
                                    data-nim="{{ $t->mahasiswa->nim ?? '-' }}"
                                    data-jenis="{{ $t->jenis_tagihan }}"
                                    {{ (old('tagihan_id') == $t->id || ($tagihan && $tagihan->id == $t->id)) ? 'selected' : '' }}>
                                    {{ $t->mahasiswa->nim ?? '-' }} - {{ $t->mahasiswa->nama ?? '-' }} | {{ $t->jenis_tagihan }} (Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }})
                                </option>
                                @endforeach
                            </select>
                            @error('tagihan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Skema Cicilan <span class="text-danger">*</span></label>
                            <select name="skema_cicilan_id" id="skema_cicilan_id" class="form-select @error('skema_cicilan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Skema --</option>
                                @foreach($skemaCicilan as $skema)
                                <option value="{{ $skema->id }}"
                                    data-jumlah="{{ $skema->jumlah_cicilan }}"
                                    data-admin="{{ $skema->biaya_admin }}"
                                    data-bunga="{{ $skema->persentase_bunga }}"
                                    data-minimal="{{ $skema->minimal_tagihan }}"
                                    data-interval="{{ $skema->interval_hari }}"
                                    {{ old('skema_cicilan_id') == $skema->id ? 'selected' : '' }}>
                                    {{ $skema->nama }} ({{ $skema->jumlah_cicilan }}x, Admin: Rp {{ number_format($skema->biaya_admin, 0, ',', '.') }}, Bunga: {{ $skema->persentase_bunga }}%)
                                </option>
                                @endforeach
                            </select>
                            @error('skema_cicilan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai Cicilan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                value="{{ old('tanggal_mulai', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tanggal jatuh tempo cicilan pertama</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Buat Cicilan
                            </button>
                            <a href="{{ route('cicilan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Info Tagihan -->
            <div class="card mb-3" id="infoTagihan" style="display: none;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Info Tagihan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Mahasiswa</td>
                            <td id="infoMahasiswa">-</td>
                        </tr>
                        <tr>
                            <td>NIM</td>
                            <td id="infoNim">-</td>
                        </tr>
                        <tr>
                            <td>Jenis</td>
                            <td id="infoJenis">-</td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>Sisa Tagihan</strong></td>
                            <td id="infoSisa"><strong>-</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Simulasi Cicilan -->
            <div class="card" id="simulasiCard" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Simulasi Cicilan</h6>
                </div>
                <div class="card-body">
                    <div id="simulasiContent">
                        <p class="text-muted">Pilih tagihan dan skema untuk melihat simulasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const formatRp = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val);

function updateInfo() {
    const tagihanSelect = document.getElementById('tagihan_id');
    const skemaSelect = document.getElementById('skema_cicilan_id');
    const tagihanOption = tagihanSelect.options[tagihanSelect.selectedIndex];
    const skemaOption = skemaSelect.options[skemaSelect.selectedIndex];

    // Update info tagihan
    if (tagihanSelect.value) {
        document.getElementById('infoTagihan').style.display = 'block';
        document.getElementById('infoMahasiswa').textContent = tagihanOption.dataset.mahasiswa;
        document.getElementById('infoNim').textContent = tagihanOption.dataset.nim;
        document.getElementById('infoJenis').textContent = tagihanOption.dataset.jenis;
        document.getElementById('infoSisa').innerHTML = '<strong>' + formatRp(parseFloat(tagihanOption.dataset.sisa)) + '</strong>';
    } else {
        document.getElementById('infoTagihan').style.display = 'none';
    }

    // Update simulasi
    if (tagihanSelect.value && skemaSelect.value) {
        document.getElementById('simulasiCard').style.display = 'block';
        
        const sisa = parseFloat(tagihanOption.dataset.sisa);
        const jumlah = parseInt(skemaOption.dataset.jumlah);
        const admin = parseFloat(skemaOption.dataset.admin);
        const bunga = parseFloat(skemaOption.dataset.bunga);
        const interval = parseInt(skemaOption.dataset.interval);

        const totalBunga = sisa * (bunga / 100) * jumlah;
        const totalBayar = sisa + admin + totalBunga;
        const perCicilan = Math.ceil(totalBayar / jumlah);

        // Generate jadwal
        let jadwalHtml = '';
        const tanggalMulai = new Date(document.querySelector('[name="tanggal_mulai"]').value || new Date());
        for (let i = 1; i <= jumlah; i++) {
            const tgl = new Date(tanggalMulai);
            tgl.setDate(tgl.getDate() + (i - 1) * interval);
            jadwalHtml += `<tr><td>Cicilan ${i}</td><td>${tgl.toLocaleDateString('id-ID')}</td><td class="text-end">${formatRp(perCicilan)}</td></tr>`;
        }

        document.getElementById('simulasiContent').innerHTML = `
            <table class="table table-sm">
                <tr><td>Sisa Tagihan</td><td class="text-end">${formatRp(sisa)}</td></tr>
                <tr><td>Biaya Admin</td><td class="text-end">${formatRp(admin)}</td></tr>
                <tr><td>Total Bunga (${bunga}% x ${jumlah})</td><td class="text-end">${formatRp(totalBunga)}</td></tr>
                <tr class="table-warning"><td><strong>Total Bayar</strong></td><td class="text-end"><strong>${formatRp(totalBayar)}</strong></td></tr>
                <tr class="table-success"><td><strong>Per Cicilan</strong></td><td class="text-end"><strong>${formatRp(perCicilan)}</strong></td></tr>
            </table>
            <hr>
            <h6 class="mb-2">Jadwal Cicilan:</h6>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-bordered">
                    <thead class="table-light"><tr><th>Cicilan</th><th>Jatuh Tempo</th><th class="text-end">Nominal</th></tr></thead>
                    <tbody>${jadwalHtml}</tbody>
                </table>
            </div>
        `;
    } else {
        document.getElementById('simulasiCard').style.display = 'none';
    }
}

document.getElementById('tagihan_id').addEventListener('change', updateInfo);
document.getElementById('skema_cicilan_id').addEventListener('change', updateInfo);
document.querySelector('[name="tanggal_mulai"]').addEventListener('change', updateInfo);

// Initial
updateInfo();
</script>
@endpush
@endsection
