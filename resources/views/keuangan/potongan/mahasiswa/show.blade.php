@extends('layouts.app')

@section('title', 'Detail Potongan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-tag me-2"></i>Detail Potongan</h5>
                    @if(in_array($potonganMahasiswa->status, ['pending', 'disetujui']))
                        <a href="{{ route('potongan-mahasiswa.edit', $potonganMahasiswa) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kode</th>
                            <td><code>{{ $potonganMahasiswa->kode }}</code></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge bg-{{ $potonganMahasiswa->status_badge }} fs-6">{{ $potonganMahasiswa->status_label }}</span></td>
                        </tr>
                    </table>

                    <hr>
                    <h6 class="text-muted mb-3">Data Mahasiswa</h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">NIM</th>
                            <td>{{ $potonganMahasiswa->mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><strong>{{ $potonganMahasiswa->mahasiswa->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>{{ $potonganMahasiswa->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                    </table>

                    <hr>
                    <h6 class="text-muted mb-3">Detail Potongan</h6>

                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Jenis Potongan</th>
                            <td><span class="badge bg-info">{{ $potonganMahasiswa->jenisPotongan->nama ?? '-' }}</span></td>
                        </tr>
                        @if($potonganMahasiswa->periodeDiskon)
                        <tr>
                            <th>Periode Diskon</th>
                            <td>{{ $potonganMahasiswa->periodeDiskon->nama }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Tipe Nilai</th>
                            <td>{{ $potonganMahasiswa->tipe_nilai_label }}</td>
                        </tr>
                        <tr>
                            <th>Nilai</th>
                            <td><strong class="text-success fs-5">{{ $potonganMahasiswa->nilai_label }}</strong></td>
                        </tr>
                        @if($potonganMahasiswa->nilai_max)
                        <tr>
                            <th>Nilai Maksimal</th>
                            <td>Rp {{ number_format($potonganMahasiswa->nilai_max, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Periode Berlaku</th>
                            <td>
                                @if($potonganMahasiswa->tanggal_mulai || $potonganMahasiswa->tanggal_selesai)
                                    {{ $potonganMahasiswa->tanggal_mulai?->format('d M Y') ?? 'Awal' }} - {{ $potonganMahasiswa->tanggal_selesai?->format('d M Y') ?? 'Selamanya' }}
                                @else
                                    <span class="text-muted">Selamanya</span>
                                @endif
                            </td>
                        </tr>
                        @if($potonganMahasiswa->alasan)
                        <tr>
                            <th>Alasan</th>
                            <td>{{ $potonganMahasiswa->alasan }}</td>
                        </tr>
                        @endif
                        @if($potonganMahasiswa->catatan)
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $potonganMahasiswa->catatan }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($potonganMahasiswa->status === 'ditolak' && $potonganMahasiswa->alasan_penolakan)
                        <div class="alert alert-danger">
                            <strong>Alasan Penolakan:</strong><br>
                            {{ $potonganMahasiswa->alasan_penolakan }}
                        </div>
                    @endif

                    <hr>
                    <h6 class="text-muted mb-3">Info Persetujuan</h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Dibuat Oleh</th>
                            <td>{{ $potonganMahasiswa->createdBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $potonganMahasiswa->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @if($potonganMahasiswa->disetujui_oleh)
                        <tr>
                            <th>{{ $potonganMahasiswa->status === 'disetujui' ? 'Disetujui' : 'Diproses' }} Oleh</th>
                            <td>{{ $potonganMahasiswa->disetujuiOleh->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $potonganMahasiswa->disetujui_at?->format('d M Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('potongan-mahasiswa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        @if($potonganMahasiswa->status === 'pending')
                            <form action="{{ route('potongan-mahasiswa.approve', $potonganMahasiswa) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i> Setujui
                                </button>
                            </form>
                        @endif
                        @if(in_array($potonganMahasiswa->status, ['pending', 'disetujui']))
                            <form action="{{ route('potongan-mahasiswa.cancel', $potonganMahasiswa) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan potongan ini?')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <!-- Riwayat Penggunaan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Penggunaan Potongan</h6>
                </div>
                <div class="card-body">
                    @if($potonganMahasiswa->riwayatPotongan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Tagihan</th>
                                        <th class="text-end">Nominal Tagihan</th>
                                        <th class="text-end">Potongan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($potonganMahasiswa->riwayatPotongan as $riwayat)
                                        <tr>
                                            <td>{{ $riwayat->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <code>{{ $riwayat->tagihan->no_tagihan ?? '-' }}</code><br>
                                                <small class="text-muted">{{ $riwayat->tagihan->jenis_tagihan ?? '' }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($riwayat->nominal_tagihan, 0, ',', '.') }}</td>
                                            <td class="text-end text-success">-Rp {{ number_format($riwayat->nominal_potongan, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total Potongan:</th>
                                        <th class="text-end text-success">Rp {{ number_format($potonganMahasiswa->riwayatPotongan->sum('nominal_potongan'), 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada penggunaan potongan</p>
                    @endif
                </div>
            </div>

            <!-- Apply to Tagihan (if approved) -->
            @if($potonganMahasiswa->status === 'disetujui' && $potonganMahasiswa->is_valid)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Terapkan ke Tagihan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('potongan-mahasiswa.apply-to-tagihan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="potongan_mahasiswa_id" value="{{ $potonganMahasiswa->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Tagihan</label>
                            <select name="tagihan_id" id="tagihan_id" class="form-select" required>
                                <option value="">Pilih tagihan...</option>
                            </select>
                            <small class="text-muted">Hanya tagihan yang belum lunas</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i> Terapkan Potongan
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($potonganMahasiswa->status === 'disetujui' && $potonganMahasiswa->is_valid)
@push('scripts')
<script>
// Load tagihan for this mahasiswa
fetch('{{ route("potongan-mahasiswa.search-tagihan") }}?mahasiswa_id={{ $potonganMahasiswa->mahasiswa_id }}')
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('tagihan_id');
        data.results.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.text;
            select.appendChild(option);
        });
    });
</script>
@endpush
@endif
@endsection
