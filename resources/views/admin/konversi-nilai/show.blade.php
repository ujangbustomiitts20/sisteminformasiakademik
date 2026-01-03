@extends('layouts.app')

@section('title', 'Detail Konversi Nilai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Konversi Nilai Mahasiswa Pindahan</h1>
        <div>
            @if(in_array($pengajuanKonversi->status, ['draft']))
                <a href="{{ route('admin.konversi-nilai.edit', $pengajuanKonversi->hashid) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('admin.konversi-nilai.destroy', $pengajuanKonversi->hashid) }}" method="POST" class="d-inline" 
                      onsubmit="return confirm('Yakin ingin menghapus data konversi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.konversi-nilai.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $badgeClass = match($pengajuanKonversi->status) {
                                        'draft' => 'secondary',
                                        'menunggu_kaprodi' => 'warning',
                                        'diproses_kaprodi' => 'info',
                                        'disetujui_kaprodi' => 'primary',
                                        'ditolak_kaprodi' => 'danger',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'menunggu_kaprodi' => 'Menunggu Kaprodi',
                                        'diproses_kaprodi' => 'Diproses Kaprodi',
                                        'disetujui_kaprodi' => 'Disetujui Kaprodi',
                                        'ditolak_kaprodi' => 'Ditolak Kaprodi',
                                        'disetujui' => 'Final - Disetujui',
                                        'ditolak' => 'Final - Ditolak',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $statusLabels[$pengajuanKonversi->status] ?? ucfirst(str_replace('_', ' ', $pengajuanKonversi->status)) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Pengajuan</td>
                            <td>{{ $pengajuanKonversi->nomor_pengajuan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Dibuat</td>
                            <td>{{ $pengajuanKonversi->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Calon Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td><strong>{{ $pengajuanKonversi->nama_calon_mahasiswa ?? $pengajuanKonversi->mahasiswa->nama ?? '-' }}</strong></td>
                        </tr>
                        @if($pengajuanKonversi->email_calon)
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $pengajuanKonversi->email_calon }}</td>
                        </tr>
                        @endif
                        @if($pengajuanKonversi->no_hp_calon)
                        <tr>
                            <td class="text-muted">No. HP</td>
                            <td>{{ $pengajuanKonversi->no_hp_calon }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Prodi Tujuan</td>
                            <td>{{ $pengajuanKonversi->programStudiTujuan->nama ?? '-' }}</td>
                        </tr>
                        @if($pengajuanKonversi->mahasiswa)
                        <tr>
                            <td class="text-muted">NIM (Terdaftar)</td>
                            <td><strong class="text-success">{{ $pengajuanKonversi->mahasiswa->nim }}</strong></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asal Universitas</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Universitas</td>
                            <td><strong>{{ $pengajuanKonversi->universitas_asal }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td>{{ $pengajuanKonversi->program_studi_asal }}</td>
                        </tr>
                        @if($pengajuanKonversi->nim_asal)
                        <tr>
                            <td class="text-muted">NIM Asal</td>
                            <td>{{ $pengajuanKonversi->nim_asal }}</td>
                        </tr>
                        @endif
                        @if($pengajuanKonversi->tahun_masuk_asal)
                        <tr>
                            <td class="text-muted">Tahun Masuk</td>
                            <td>{{ $pengajuanKonversi->tahun_masuk_asal }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($pengajuanKonversi->dokumen_transkrip)
                    <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_transkrip) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="bi bi-file-pdf"></i> Lihat Transkrip
                    </a>
                    @endif
                    @if($pengajuanKonversi->dokumen_silabus)
                    <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_silabus) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="bi bi-file-pdf"></i> Lihat Silabus
                    </a>
                    @endif
                    @if($pengajuanKonversi->dokumen_pendukung)
                    <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="bi bi-file-pdf"></i> Lihat Dok. Pendukung
                    </a>
                    @endif
                </div>
            </div>

            <!-- Riwayat Kaprodi -->
            @if($pengajuanKonversi->catatan_kaprodi || $pengajuanKonversi->prosesKaprodiOleh)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Proses Kaprodi</h6>
                </div>
                <div class="card-body">
                    @if($pengajuanKonversi->prosesKaprodiOleh)
                    <p class="mb-2">
                        <small class="text-muted">Diproses oleh:</small><br>
                        {{ $pengajuanKonversi->prosesKaprodiOleh->name }}
                    </p>
                    @endif
                    @if($pengajuanKonversi->tanggal_diproses_kaprodi)
                    <p class="mb-2">
                        <small class="text-muted">Tanggal proses:</small><br>
                        {{ \Carbon\Carbon::parse($pengajuanKonversi->tanggal_diproses_kaprodi)->format('d M Y H:i') }}
                    </p>
                    @endif
                    @if($pengajuanKonversi->catatan_kaprodi)
                    <p class="mb-0">
                        <small class="text-muted">Catatan:</small><br>
                        {{ $pengajuanKonversi->catatan_kaprodi }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Aksi Berdasarkan Status -->
            @if($pengajuanKonversi->status == 'draft')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Mata Kuliah</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.konversi-nilai.tambah-detail', $pengajuanKonversi->hashid) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="kode_mk_asal" class="form-control form-control-sm" placeholder="Kode MK Asal" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="nama_mk_asal" class="form-control form-control-sm" placeholder="Nama MK Asal" required>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <input type="number" name="sks_asal" class="form-control form-control-sm" placeholder="SKS" min="1" max="8" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="nilai_asal" class="form-control form-control-sm" placeholder="Nilai (A/B/C)" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus"></i> Tambah MK
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.konversi-nilai.ajukan-kaprodi', $pengajuanKonversi->hashid) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Yakin ingin mengajukan ke Kaprodi? Data mata kuliah harus sudah lengkap.')">
                            <i class="bi bi-send"></i> Ajukan ke Kaprodi
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Aksi Finalisasi (setelah disetujui Kaprodi) -->
            @if($pengajuanKonversi->status == 'disetujui_kaprodi')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Finalisasi</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <small><i class="bi bi-info-circle"></i> Pilih mahasiswa yang sudah terdaftar di sistem untuk menghubungkan data konversi nilai.</small>
                    </div>
                    <form action="{{ route('admin.konversi-nilai.finalisasi', $pengajuanKonversi->hashid) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Mahasiswa</label>
                            <select name="mahasiswa_id" class="form-select" required>
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($mahasiswas ?? [] as $mhs)
                                <option value="{{ $mhs->id }}" {{ $pengajuanKonversi->mahasiswa_id == $mhs->id ? 'selected' : '' }}>
                                    {{ $mhs->nim }} - {{ $mhs->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Finalisasi</label>
                            <select name="status" class="form-select" required>
                                <option value="disetujui">Setujui & Finalisasi</option>
                                <option value="ditolak">Tolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan (opsional)...">{{ $pengajuanKonversi->catatan }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Finalisasi akan memasukkan nilai konversi ke transkrip mahasiswa. Lanjutkan?')">
                            <i class="bi bi-check-circle"></i> Proses Finalisasi
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Mata Kuliah yang Dikonversi</h6>
                    <span class="badge bg-primary">{{ $pengajuanKonversi->detailKonversi->count() }} MK</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="4" class="text-center bg-warning-subtle">Mata Kuliah Asal</th>
                                    <th colspan="4" class="text-center bg-success-subtle">Hasil Konversi</th>
                                    @if($pengajuanKonversi->status == 'draft')
                                    <th rowspan="2">Aksi</th>
                                    @endif
                                </tr>
                                <tr>
                                    <th>Kode MK</th>
                                    <th>Nama MK</th>
                                    <th>SKS</th>
                                    <th>Nilai</th>
                                    <th>MK Tujuan</th>
                                    <th>SKS</th>
                                    <th>Nilai Konversi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuanKonversi->detailKonversi as $detail)
                                <tr>
                                    <td>{{ $detail->kode_mk_asal }}</td>
                                    <td>{{ $detail->nama_mk_asal }}</td>
                                    <td class="text-center">{{ $detail->sks_asal }}</td>
                                    <td class="text-center">{{ $detail->nilai_asal }}</td>
                                    <td>
                                        @if($detail->mataKuliah)
                                        {{ $detail->mataKuliah->kode }} - {{ $detail->mataKuliah->nama }}
                                        @else
                                        <span class="text-muted">Belum dipetakan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->mataKuliah->sks ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->nilai_konversi ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($detail->status ?? 'pending') {
                                                'pending' => 'secondary',
                                                'disetujui' => 'success',
                                                'ditolak' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($detail->status ?? 'Pending') }}</span>
                                    </td>
                                    @if($pengajuanKonversi->status == 'draft')
                                    <td>
                                        <form action="{{ route('admin.konversi-nilai.hapus-detail', $detail->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $pengajuanKonversi->status == 'draft' ? 9 : 8 }}" class="text-center text-muted">Belum ada mata kuliah yang ditambahkan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($pengajuanKonversi->detailKonversi->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-center">{{ $pengajuanKonversi->detailKonversi->sum('sks_asal') }}</th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-center">{{ $pengajuanKonversi->detailKonversi->whereNotNull('mata_kuliah_id')->sum(function($d) { return $d->mataKuliah->sks ?? 0; }) }}</th>
                                    <th colspan="{{ $pengajuanKonversi->status == 'draft' ? 3 : 2 }}"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    @if($pengajuanKonversi->catatan)
                    <div class="alert alert-info mt-3">
                        <strong>Catatan Admin:</strong><br>
                        {{ $pengajuanKonversi->catatan }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
