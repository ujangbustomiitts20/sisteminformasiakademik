@extends('layouts.app')

@section('title', 'Detail Konversi Nilai')

@section('content')
<div class="page-title">
    <h4>Detail Konversi Nilai Mahasiswa Pindahan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.konversi-nilai.index') }}">Konversi Nilai</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Info Pengajuan -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Pengajuan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="40%">No. Pengajuan</td>
                        <td><strong>{{ $pengajuan->nomor_pengajuan ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'menunggu_kaprodi' => 'warning',
                                    'diproses_kaprodi' => 'info',
                                    'disetujui_kaprodi' => 'primary',
                                    'ditolak_kaprodi' => 'danger',
                                    'disetujui' => 'success',
                                    'ditolak' => 'danger',
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'menunggu_kaprodi' => 'Menunggu Proses',
                                    'diproses_kaprodi' => 'Sedang Diproses',
                                    'disetujui_kaprodi' => 'Disetujui Kaprodi',
                                    'ditolak_kaprodi' => 'Ditolak Kaprodi',
                                    'disetujui' => 'Final - Disetujui',
                                    'ditolak' => 'Final - Ditolak',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$pengajuan->status] ?? 'secondary' }}">
                                {{ $statusLabels[$pengajuan->status] ?? ucfirst($pengajuan->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Dibuat</td>
                        <td>{{ $pengajuan->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Data Calon Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="40%">Nama</td>
                        <td><strong>{{ $pengajuan->nama_calon_mahasiswa ?? $pengajuan->mahasiswa->nama ?? '-' }}</strong></td>
                    </tr>
                    @if($pengajuan->email_calon)
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $pengajuan->email_calon }}</td>
                    </tr>
                    @endif
                    @if($pengajuan->no_hp_calon)
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $pengajuan->no_hp_calon }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Prodi Tujuan</td>
                        <td>{{ $pengajuan->programStudiTujuan->nama ?? '-' }}</td>
                    </tr>
                    @if($pengajuan->mahasiswa)
                    <tr>
                        <td class="text-muted">NIM (Terdaftar)</td>
                        <td><strong class="text-success">{{ $pengajuan->mahasiswa->nim }}</strong></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Asal Universitas</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="40%">Universitas</td>
                        <td><strong>{{ $pengajuan->universitas_asal }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $pengajuan->program_studi_asal }}</td>
                    </tr>
                    @if($pengajuan->tahun_masuk_asal)
                    <tr>
                        <td class="text-muted">Tahun Masuk</td>
                        <td>{{ $pengajuan->tahun_masuk_asal }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        @if($pengajuan->catatan_kaprodi || $pengajuan->prosesKaprodiOleh)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Riwayat Proses Kaprodi</h6>
            </div>
            <div class="card-body">
                @if($pengajuan->prosesKaprodiOleh)
                <p class="mb-2">
                    <small class="text-muted">Diproses oleh:</small><br>
                    {{ $pengajuan->prosesKaprodiOleh->name }}
                </p>
                @endif
                @if($pengajuan->tanggal_diproses_kaprodi)
                <p class="mb-2">
                    <small class="text-muted">Tanggal proses:</small><br>
                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_diproses_kaprodi)->format('d M Y H:i') }}
                </p>
                @endif
                @if($pengajuan->catatan_kaprodi)
                <p class="mb-0">
                    <small class="text-muted">Catatan:</small><br>
                    {{ $pengajuan->catatan_kaprodi }}
                </p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Detail Mata Kuliah -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Mata Kuliah Konversi</h6>
                <span class="badge bg-primary">{{ $pengajuan->detailKonversi->count() }} MK</span>
            </div>
            <div class="card-body">
                @if(in_array($pengajuan->status, ['menunggu_kaprodi', 'diproses_kaprodi']))
                    <!-- Form Proses -->
                    <form action="{{ route('kaprodi.konversi-nilai.proses', $pengajuan) }}" method="POST" id="formProses">
                        @csrf
                        
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i> 
                            Pilih kurikulum terlebih dahulu, kemudian petakan mata kuliah dari universitas asal ke mata kuliah di program studi tujuan.
                        </div>

                        <!-- Pilihan Kurikulum -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Kurikulum <span class="text-danger">*</span></label>
                                <select id="kurikulumSelect" class="form-select" required>
                                    <option value="">-- Pilih Kurikulum --</option>
                                    @foreach($kurikulums as $kurikulum)
                                    <option value="{{ $kurikulum->id }}">
                                        {{ $kurikulum->kode }} - {{ $kurikulum->nama }} ({{ $kurikulum->tahun_mulai }}-{{ $kurikulum->tahun_selesai ?? 'sekarang' }})
                                        @if($kurikulum->is_aktif) ✓ Aktif @endif
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih kurikulum untuk menampilkan daftar mata kuliah tujuan</small>
                            </div>
                            <div class="col-md-6">
                                <div id="kurikulumInfo" class="alert alert-secondary d-none">
                                    <small>
                                        <strong>Mata Kuliah Tersedia:</strong> <span id="mkCount">0</span> MK
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-center bg-warning-subtle">Mata Kuliah Asal</th>
                                        <th colspan="3" class="text-center bg-success-subtle">Pemetaan Tujuan</th>
                                    </tr>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode MK</th>
                                        <th>Nama MK</th>
                                        <th width="8%">SKS</th>
                                        <th>MK Tujuan (dari Kurikulum)</th>
                                        <th width="10%">Nilai Konversi</th>
                                        <th width="8%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengajuan->detailKonversi as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $detail->kode_mk_asal }}</td>
                                        <td>{{ $detail->nama_mk_asal }}</td>
                                        <td class="text-center">{{ $detail->sks_asal }}</td>
                                        <td>
                                            <input type="hidden" name="details[{{ $detail->id }}][id]" value="{{ $detail->id }}">
                                            <select name="details[{{ $detail->id }}][mata_kuliah_id]" 
                                                    class="form-select form-select-sm mk-tujuan-select" 
                                                    data-current="{{ $detail->mata_kuliah_id }}"
                                                    disabled>
                                                <option value="">-- Pilih Kurikulum Dulu --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="details[{{ $detail->id }}][nilai_konversi]" class="form-select form-select-sm">
                                                <option value="">-</option>
                                                @foreach(['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D', 'E'] as $nilai)
                                                <option value="{{ $nilai }}" {{ $detail->nilai_konversi == $nilai ? 'selected' : '' }}>
                                                    {{ $nilai }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="details[{{ $detail->id }}][status_konversi]" class="form-select form-select-sm">
                                                <option value="pending" {{ ($detail->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="disetujui" {{ ($detail->status ?? '') == 'disetujui' ? 'selected' : '' }}>Setujui</option>
                                                <option value="ditolak" {{ ($detail->status ?? '') == 'ditolak' ? 'selected' : '' }}>Tolak</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label">Catatan Kaprodi</label>
                            <textarea name="catatan_kaprodi" class="form-control" rows="3" 
                                placeholder="Tambahkan catatan jika diperlukan...">{{ $pengajuan->catatan_kaprodi }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kaprodi.konversi-nilai.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" name="action" value="simpan" class="btn btn-outline-primary">
                                    <i class="bi bi-save"></i> Simpan Draft
                                </button>
                                <button type="submit" name="action" value="tolak" class="btn btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menolak konversi nilai ini?')">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                                <button type="submit" name="action" value="setujui" class="btn btn-success"
                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui konversi nilai ini?')">
                                    <i class="bi bi-check-circle"></i> Setujui
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <!-- Read Only View -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="4" class="text-center bg-warning-subtle">Mata Kuliah Asal</th>
                                    <th colspan="4" class="text-center bg-success-subtle">Hasil Konversi</th>
                                </tr>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode MK</th>
                                    <th>Nama MK</th>
                                    <th width="8%">SKS</th>
                                    <th>MK Tujuan</th>
                                    <th width="8%">SKS</th>
                                    <th width="10%">Nilai</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuan->detailKonversi as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detail->kode_mk_asal }}</td>
                                    <td>{{ $detail->nama_mk_asal }}</td>
                                    <td class="text-center">{{ $detail->sks_asal }}</td>
                                    <td>
                                        @if($detail->mataKuliah)
                                            {{ $detail->mataKuliah->kode }} - {{ $detail->mataKuliah->nama }}
                                        @else
                                            <span class="text-muted">Tidak dikonversi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->mataKuliah->sks ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($detail->nilai_konversi)
                                            <strong>{{ $detail->nilai_konversi }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusKonversiColors = [
                                                'pending' => 'secondary',
                                                'disetujui' => 'success',
                                                'ditolak' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusKonversiColors[$detail->status ?? 'pending'] ?? 'secondary' }}">
                                            {{ ucfirst($detail->status ?? 'Pending') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="text-center">{{ $pengajuan->detailKonversi->sum('sks_asal') }}</th>
                                    <th></th>
                                    <th class="text-center">{{ $pengajuan->detailKonversi->whereNotNull('mata_kuliah_id')->sum(function($d) { return $d->mataKuliah->sks ?? 0; }) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('kaprodi.konversi-nilai.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kurikulumSelect = document.getElementById('kurikulumSelect');
    const mkSelects = document.querySelectorAll('.mk-tujuan-select');
    const kurikulumInfo = document.getElementById('kurikulumInfo');
    const mkCountSpan = document.getElementById('mkCount');
    
    let mataKuliahData = [];

    if (kurikulumSelect) {
        kurikulumSelect.addEventListener('change', function() {
            const kurikulumId = this.value;
            
            if (!kurikulumId) {
                // Reset semua select
                mkSelects.forEach(select => {
                    select.innerHTML = '<option value="">-- Pilih Kurikulum Dulu --</option>';
                    select.disabled = true;
                });
                kurikulumInfo.classList.add('d-none');
                return;
            }

            // Fetch mata kuliah dari API
            fetch(`{{ route('kaprodi.konversi-nilai.api.mata-kuliah') }}?kurikulum_id=${kurikulumId}`)
                .then(response => response.json())
                .then(data => {
                    mataKuliahData = data;
                    
                    // Update info
                    mkCountSpan.textContent = data.length;
                    kurikulumInfo.classList.remove('d-none');
                    
                    // Update semua select
                    mkSelects.forEach(select => {
                        const currentValue = select.dataset.current;
                        select.innerHTML = '<option value="">-- Tidak Dikonversi --</option>';
                        
                        // Group by semester
                        let currentSemester = null;
                        data.forEach(mk => {
                            if (mk.semester !== currentSemester) {
                                if (currentSemester !== null) {
                                    // Close previous optgroup
                                }
                                currentSemester = mk.semester;
                            }
                            
                            const option = document.createElement('option');
                            option.value = mk.id;
                            option.textContent = mk.label;
                            option.dataset.sks = mk.sks;
                            
                            if (currentValue && parseInt(currentValue) === mk.id) {
                                option.selected = true;
                            }
                            
                            select.appendChild(option);
                        });
                        
                        select.disabled = false;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil data mata kuliah. Silakan coba lagi.');
                });
        });
    }
});
</script>
@endpush
@endsection
