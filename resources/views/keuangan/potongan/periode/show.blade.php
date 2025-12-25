@extends('layouts.app')

@section('title', 'Detail Periode Diskon')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Detail Periode Diskon</h5>
                    <div>
                        <a href="{{ route('periode-diskon.edit', $periodeDiskon) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kode</th>
                            <td><code>{{ $periodeDiskon->kode }}</code></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $periodeDiskon->nama }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Potongan</th>
                            <td><span class="badge bg-info">{{ $periodeDiskon->jenisPotongan->nama ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <th>Tipe Nilai</th>
                            <td>{{ $periodeDiskon->tipe_nilai_label }}</td>
                        </tr>
                        <tr>
                            <th>Nilai</th>
                            <td><strong class="text-success fs-5">{{ $periodeDiskon->nilai_label }}</strong></td>
                        </tr>
                        @if($periodeDiskon->nilai_max)
                        <tr>
                            <th>Nilai Maksimal</th>
                            <td>Rp {{ number_format($periodeDiskon->nilai_max, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($periodeDiskon->min_transaksi)
                        <tr>
                            <th>Min. Transaksi</th>
                            <td>Rp {{ number_format($periodeDiskon->min_transaksi, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Periode</th>
                            <td>
                                {{ $periodeDiskon->tanggal_mulai->format('d M Y') }} - {{ $periodeDiskon->tanggal_selesai->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Kuota</th>
                            <td>
                                @if($periodeDiskon->kuota)
                                    <span class="badge bg-{{ $periodeDiskon->sisa_kuota > 0 ? 'info' : 'danger' }} fs-6">
                                        {{ $periodeDiskon->kuota_terpakai }} / {{ $periodeDiskon->kuota }}
                                    </span>
                                    <small class="text-muted ms-2">(Sisa: {{ $periodeDiskon->sisa_kuota }})</small>
                                @else
                                    <span class="badge bg-secondary">Unlimited</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge bg-{{ $periodeDiskon->status_badge }} fs-6">{{ $periodeDiskon->status_label }}</span></td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $periodeDiskon->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($periodeDiskon->berlaku_untuk)
                        <hr>
                        <h6 class="text-muted">Berlaku Untuk:</h6>
                        <ul class="small">
                            @if(isset($periodeDiskon->berlaku_untuk['program_studi_id']))
                                <li>Program Studi: {{ implode(', ', $periodeDiskon->berlaku_untuk['program_studi_id']) }}</li>
                            @endif
                            @if(isset($periodeDiskon->berlaku_untuk['angkatan']))
                                <li>Angkatan: {{ implode(', ', $periodeDiskon->berlaku_untuk['angkatan']) }}</li>
                            @endif
                            @if(isset($periodeDiskon->berlaku_untuk['jenis_tagihan']))
                                <li>Jenis Tagihan: {{ implode(', ', $periodeDiskon->berlaku_untuk['jenis_tagihan']) }}</li>
                            @endif
                        </ul>
                    @endif

                    @if($periodeDiskon->syarat_ketentuan)
                        <hr>
                        <h6 class="text-muted">Syarat & Ketentuan:</h6>
                        <p class="small">{{ $periodeDiskon->syarat_ketentuan }}</p>
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('periode-diskon.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <form action="{{ route('periode-diskon.toggle-status', $periodeDiskon) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $periodeDiskon->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $periodeDiskon->is_active ? 'ban' : 'check' }} me-1"></i>
                                {{ $periodeDiskon->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <!-- Riwayat Penggunaan -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Penggunaan Diskon</h6>
                </div>
                <div class="card-body">
                    @if($periodeDiskon->riwayatPotongan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Mahasiswa</th>
                                        <th>No. Tagihan</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-end">Potongan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periodeDiskon->riwayatPotongan as $riwayat)
                                        <tr>
                                            <td>{{ $riwayat->created_at->format('d M Y') }}</td>
                                            <td>{{ $riwayat->mahasiswa->nama ?? '-' }}</td>
                                            <td><code>{{ $riwayat->tagihan->no_tagihan ?? '-' }}</code></td>
                                            <td class="text-end">Rp {{ number_format($riwayat->nominal_tagihan, 0, ',', '.') }}</td>
                                            <td class="text-end text-success">-Rp {{ number_format($riwayat->nominal_potongan, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total Potongan:</th>
                                        <th class="text-end text-success">Rp {{ number_format($periodeDiskon->riwayatPotongan->sum('nominal_potongan'), 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada penggunaan diskon</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
