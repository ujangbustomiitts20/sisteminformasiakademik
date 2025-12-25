@extends('layouts.app')

@section('title', 'Ajukan Refund')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Ajukan Refund</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('keuangan.refund.index') }}">Refund</a></li>
                    <li class="breadcrumb-item active">Ajukan</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('keuangan.refund.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Cari Mahasiswa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="searchMahasiswa" class="form-control" placeholder="Ketik NIM atau nama mahasiswa..." value="{{ $mahasiswa?->nim }} - {{ $mahasiswa?->nama }}">
                                <button type="button" class="btn btn-outline-secondary" id="clearMahasiswa">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <input type="hidden" name="mahasiswa_id" id="mahasiswaId" value="{{ $mahasiswa?->id }}" required>
                            <div id="mahasiswaDropdown" class="dropdown-menu w-100" style="display: none;"></div>
                        </div>
                        
                        <div id="mahasiswaInfo" class="{{ $mahasiswa ? '' : 'd-none' }}">
                            <div class="alert alert-info mb-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">NIM</small>
                                        <div id="mahasiswaNim">{{ $mahasiswa?->nim }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Nama</small>
                                        <div id="mahasiswaNama">{{ $mahasiswa?->nama }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transaksi Terkait (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Cari Transaksi Pembayaran</label>
                            <div class="input-group">
                                <input type="text" id="searchTransaksi" class="form-control" placeholder="Ketik nomor transaksi..." value="{{ $transaksiPembayaran?->no_transaksi }}">
                                <button type="button" class="btn btn-outline-secondary" id="clearTransaksi">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <input type="hidden" name="transaksi_pembayaran_id" id="transaksiId" value="{{ $transaksiPembayaran?->id }}">
                            <input type="hidden" name="tagihan_id" id="tagihanId" value="{{ $transaksiPembayaran?->tagihan_id }}">
                            <div id="transaksiDropdown" class="dropdown-menu w-100" style="display: none;"></div>
                        </div>
                        
                        <div id="transaksiInfo" class="{{ $transaksiPembayaran ? '' : 'd-none' }}">
                            <div class="alert alert-secondary mb-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">No. Transaksi</small>
                                        <div id="transaksiNo">{{ $transaksiPembayaran?->no_transaksi }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Tanggal</small>
                                        <div id="transaksiTanggal">{{ $transaksiPembayaran?->tanggal_bayar?->format('d/m/Y') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Jumlah</small>
                                        <div id="transaksiJumlah" class="fw-bold">Rp {{ number_format($transaksiPembayaran?->jumlah ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Detail Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Refund <span class="text-danger">*</span></label>
                                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                        <option value="">Pilih Jenis</option>
                                        @foreach($jenisList as $key => $label)
                                            <option value="{{ $key }}" {{ old('jenis') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pengajuan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="jumlah_pengajuan" class="form-control @error('jumlah_pengajuan') is-invalid @enderror" 
                                            value="{{ old('jumlah_pengajuan', $transaksiPembayaran?->jumlah) }}" required min="0" step="0.01">
                                    </div>
                                    @error('jumlah_pengajuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alasan Pengajuan <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="4" required placeholder="Jelaskan alasan pengajuan refund...">{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dokumen Pendukung</label>
                            <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF. Maks: 2MB</small>
                            @error('dokumen_pendukung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Metode Pengembalian</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Metode Refund <span class="text-danger">*</span></label>
                            <select name="metode_refund" id="metodeRefund" class="form-select @error('metode_refund') is-invalid @enderror" required>
                                <option value="">Pilih Metode</option>
                                @foreach($metodeList as $key => $label)
                                    <option value="{{ $key }}" {{ old('metode_refund') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('metode_refund')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="bankFields" class="d-none">
                            <div class="mb-3">
                                <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" value="{{ old('nama_bank') }}" placeholder="Contoh: BCA, BNI, Mandiri">
                                @error('nama_bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_rekening" class="form-control @error('nomor_rekening') is-invalid @enderror" value="{{ old('nomor_rekening') }}">
                                @error('nomor_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pemilik_rekening" class="form-control @error('nama_pemilik_rekening') is-invalid @enderror" value="{{ old('nama_pemilik_rekening') }}">
                                @error('nama_pemilik_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Ajukan Refund
                            </button>
                            <a href="{{ route('keuangan.refund.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const metodeRefund = document.getElementById('metodeRefund');
    const bankFields = document.getElementById('bankFields');

    // Toggle bank fields
    metodeRefund.addEventListener('change', function() {
        if (this.value === 'transfer') {
            bankFields.classList.remove('d-none');
            bankFields.querySelectorAll('input').forEach(input => input.required = true);
        } else {
            bankFields.classList.add('d-none');
            bankFields.querySelectorAll('input').forEach(input => input.required = false);
        }
    });

    // Trigger on page load
    if (metodeRefund.value === 'transfer') {
        bankFields.classList.remove('d-none');
    }

    // Search Mahasiswa
    const searchMahasiswa = document.getElementById('searchMahasiswa');
    const mahasiswaDropdown = document.getElementById('mahasiswaDropdown');
    const mahasiswaId = document.getElementById('mahasiswaId');
    const mahasiswaInfo = document.getElementById('mahasiswaInfo');
    let searchTimeout;

    searchMahasiswa.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;
        
        if (query.length < 2) {
            mahasiswaDropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('keuangan.refund.search-mahasiswa') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        mahasiswaDropdown.innerHTML = data.map(m => `
                            <a href="#" class="dropdown-item" data-id="${m.id}" data-nim="${m.nim}" data-nama="${m.nama}">
                                <strong>${m.nim}</strong> - ${m.nama}
                                <br><small class="text-muted">${m.prodi}</small>
                            </a>
                        `).join('');
                        mahasiswaDropdown.style.display = 'block';
                        mahasiswaDropdown.classList.add('show');
                    } else {
                        mahasiswaDropdown.innerHTML = '<div class="dropdown-item text-muted">Tidak ditemukan</div>';
                        mahasiswaDropdown.style.display = 'block';
                        mahasiswaDropdown.classList.add('show');
                    }
                });
        }, 300);
    });

    mahasiswaDropdown.addEventListener('click', function(e) {
        if (e.target.closest('.dropdown-item[data-id]')) {
            e.preventDefault();
            const item = e.target.closest('.dropdown-item');
            mahasiswaId.value = item.dataset.id;
            searchMahasiswa.value = `${item.dataset.nim} - ${item.dataset.nama}`;
            document.getElementById('mahasiswaNim').textContent = item.dataset.nim;
            document.getElementById('mahasiswaNama').textContent = item.dataset.nama;
            mahasiswaInfo.classList.remove('d-none');
            mahasiswaDropdown.style.display = 'none';
        }
    });

    document.getElementById('clearMahasiswa').addEventListener('click', function() {
        searchMahasiswa.value = '';
        mahasiswaId.value = '';
        mahasiswaInfo.classList.add('d-none');
    });

    // Search Transaksi
    const searchTransaksi = document.getElementById('searchTransaksi');
    const transaksiDropdown = document.getElementById('transaksiDropdown');
    const transaksiId = document.getElementById('transaksiId');
    const tagihanId = document.getElementById('tagihanId');
    const transaksiInfo = document.getElementById('transaksiInfo');

    searchTransaksi.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;
        
        if (query.length < 2) {
            transaksiDropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('keuangan.refund.search-transaksi') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        transaksiDropdown.innerHTML = data.map(t => `
                            <a href="#" class="dropdown-item" 
                                data-id="${t.id}" 
                                data-no="${t.no_transaksi}" 
                                data-tanggal="${t.tanggal || '-'}" 
                                data-jumlah="${t.jumlah}"
                                data-jumlah-formatted="${t.jumlah_formatted}"
                                data-tagihan-id="${t.tagihan?.id || ''}"
                                data-mahasiswa-id="${t.mahasiswa?.id || ''}"
                                data-mahasiswa-nim="${t.mahasiswa?.nim || ''}"
                                data-mahasiswa-nama="${t.mahasiswa?.nama || ''}">
                                <strong>${t.no_transaksi}</strong> - ${t.jumlah_formatted}
                                <br><small class="text-muted">${t.mahasiswa?.nim || ''} - ${t.mahasiswa?.nama || ''}</small>
                            </a>
                        `).join('');
                        transaksiDropdown.style.display = 'block';
                        transaksiDropdown.classList.add('show');
                    } else {
                        transaksiDropdown.innerHTML = '<div class="dropdown-item text-muted">Tidak ditemukan</div>';
                        transaksiDropdown.style.display = 'block';
                        transaksiDropdown.classList.add('show');
                    }
                });
        }, 300);
    });

    transaksiDropdown.addEventListener('click', function(e) {
        if (e.target.closest('.dropdown-item[data-id]')) {
            e.preventDefault();
            const item = e.target.closest('.dropdown-item');
            transaksiId.value = item.dataset.id;
            tagihanId.value = item.dataset.tagihanId;
            searchTransaksi.value = item.dataset.no;
            document.getElementById('transaksiNo').textContent = item.dataset.no;
            document.getElementById('transaksiTanggal').textContent = item.dataset.tanggal;
            document.getElementById('transaksiJumlah').textContent = item.dataset.jumlahFormatted;
            transaksiInfo.classList.remove('d-none');
            transaksiDropdown.style.display = 'none';

            // Auto-fill mahasiswa if not already set
            if (!mahasiswaId.value && item.dataset.mahasiswaId) {
                mahasiswaId.value = item.dataset.mahasiswaId;
                searchMahasiswa.value = `${item.dataset.mahasiswaNim} - ${item.dataset.mahasiswaNama}`;
                document.getElementById('mahasiswaNim').textContent = item.dataset.mahasiswaNim;
                document.getElementById('mahasiswaNama').textContent = item.dataset.mahasiswaNama;
                mahasiswaInfo.classList.remove('d-none');
            }

            // Auto-fill jumlah
            document.querySelector('input[name="jumlah_pengajuan"]').value = item.dataset.jumlah;
        }
    });

    document.getElementById('clearTransaksi').addEventListener('click', function() {
        searchTransaksi.value = '';
        transaksiId.value = '';
        tagihanId.value = '';
        transaksiInfo.classList.add('d-none');
    });

    // Hide dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!searchMahasiswa.contains(e.target) && !mahasiswaDropdown.contains(e.target)) {
            mahasiswaDropdown.style.display = 'none';
        }
        if (!searchTransaksi.contains(e.target) && !transaksiDropdown.contains(e.target)) {
            transaksiDropdown.style.display = 'none';
        }
    });
});
</script>
@endpush
