@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pengaturan Sistem</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
            </nav>
        </div>
        <div>
            <form action="{{ route('settings.clear-cache') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>Clear Cache
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-3">
                <!-- Navigation Tabs -->
                <div class="card mb-4">
                    <div class="card-body p-2">
                        <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                            @php $first = true; @endphp
                            @foreach($groupLabels as $groupKey => $groupLabel)
                                @if($groupKey === 'print' || isset($groupedSettings[$groupKey]))
                                <button class="nav-link text-start {{ $first ? 'active' : '' }}" 
                                        id="tab-{{ $groupKey }}" 
                                        data-bs-toggle="pill" 
                                        data-bs-target="#content-{{ $groupKey }}" 
                                        type="button" 
                                        role="tab">
                                    @switch($groupKey)
                                        @case('general')
                                            <i class="bi bi-gear me-2"></i>
                                            @break
                                        @case('institution')
                                            <i class="bi bi-building me-2"></i>
                                            @break
                                        @case('contact')
                                            <i class="bi bi-telephone me-2"></i>
                                            @break
                                        @case('email')
                                            <i class="bi bi-envelope-at me-2"></i>
                                            @break
                                        @case('academic')
                                            <i class="bi bi-mortarboard me-2"></i>
                                            @break
                                        @case('appearance')
                                            <i class="bi bi-palette me-2"></i>
                                            @break
                                        @case('print')
                                            <i class="bi bi-printer me-2"></i>
                                            @break
                                        @default
                                            <i class="bi bi-sliders me-2"></i>
                                    @endswitch
                                    {{ $groupLabel }}
                                </button>
                                @php $first = false; @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Save Button (Mobile) -->
                <div class="d-lg-none">
                    <button type="submit" class="btn btn-primary w-100 mb-4">
                        <i class="bi bi-check-lg me-1"></i>Simpan Pengaturan
                    </button>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="tab-content" id="settings-tabContent">
                    @php $first = true; @endphp
                    @foreach($groupLabels as $groupKey => $groupLabel)
                        @if($groupKey === 'print')
                        <div class="tab-pane fade {{ $first ? 'show active' : '' }}" 
                             id="content-{{ $groupKey }}" 
                             role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $groupLabel }}</h5>
                                    <div>
                                        <form action="{{ route('konfigurasi-cetak.reset-all') }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Reset semua konfigurasi ke default?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Semua
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Kelola pengaturan template cetakan untuk berbagai dokumen akademik seperti KRS, KHS, Transkrip, dll.
                                    </p>
                                    
                                    <div class="row">
                                        @foreach($konfigurasiCetak as $config)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 {{ !$config->is_active ? 'border-secondary opacity-75' : 'border-primary' }}">
                                                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>{{ $config->nama }}
                                                    </h6>
                                                    @if(!$config->is_active)
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    @else
                                                        <span class="badge bg-success">Aktif</span>
                                                    @endif
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm table-borderless mb-0">
                                                        <tr>
                                                            <td width="40%" class="text-muted">Kode</td>
                                                            <td><code class="text-primary">{{ $config->kode }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Ukuran Kertas</td>
                                                            <td>{{ strtoupper($config->ukuran_kertas) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Orientasi</td>
                                                            <td>{{ ucfirst($config->orientasi) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Margin (T/R/B/L)</td>
                                                            <td><small>{{ $config->margin_top }}/{{ $config->margin_right }}/{{ $config->margin_bottom }}/{{ $config->margin_left }} mm</small></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Kop Surat</td>
                                                            <td>
                                                                @if($config->tampilkan_kop)
                                                                    <i class="bi bi-check-circle-fill text-success"></i> Ya
                                                                @else
                                                                    <i class="bi bi-x-circle text-secondary"></i> Tidak
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Logo</td>
                                                            <td>
                                                                @if($config->tampilkan_logo)
                                                                    <i class="bi bi-check-circle-fill text-success"></i> Ya
                                                                @else
                                                                    <i class="bi bi-x-circle text-secondary"></i> Tidak
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Tanda Tangan</td>
                                                            <td>
                                                                @if($config->tampilkan_ttd)
                                                                    <i class="bi bi-check-circle-fill text-success"></i> {{ $config->jabatan_ttd ?: 'Ya' }}
                                                                @else
                                                                    <i class="bi bi-x-circle text-secondary"></i> Tidak
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="card-footer bg-transparent">
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('konfigurasi-cetak.edit', $config->hashid) }}" 
                                                           class="btn btn-primary btn-sm flex-fill">
                                                            <i class="bi bi-pencil me-1"></i>Edit
                                                        </a>
                                                        <a href="{{ route('konfigurasi-cetak.preview-pdf', $config->hashid) }}" 
                                                           class="btn btn-outline-danger btn-sm" 
                                                           target="_blank" 
                                                           title="Preview PDF">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                        <a href="{{ route('konfigurasi-cetak.preview', $config->hashid) }}" 
                                                           class="btn btn-outline-secondary btn-sm" 
                                                           target="_blank" 
                                                           title="Preview HTML">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form action="{{ route('konfigurasi-cetak.reset', $config->hashid) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Reset konfigurasi {{ $config->nama }} ke default?')">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-outline-warning btn-sm" 
                                                                    title="Reset ke Default">
                                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php $first = false; @endphp
                        @elseif(isset($groupedSettings[$groupKey]))
                        <div class="tab-pane fade {{ $first ? 'show active' : '' }}" 
                             id="content-{{ $groupKey }}" 
                             role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $groupLabel }}</h5>
                                    @if($groupKey === 'email')
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                                        <i class="bi bi-send me-1"></i>Test Email
                                    </button>
                                    @endif
                                </div>
                                <div class="card-body">
                                    {{-- Preview Card untuk Institusi --}}
                                    @if($groupKey === 'institution')
                                    <div class="alert alert-light border mb-4">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                @php
                                                    $logoSetting = collect($groupedSettings[$groupKey])->firstWhere('key', 'institution_logo');
                                                    $logoValue = $logoSetting?->value;
                                                @endphp
                                                @if($logoValue)
                                                    <img src="{{ Storage::url($logoValue) }}" alt="Logo" style="max-height: 80px; max-width: 120px;" class="rounded">
                                                @else
                                                    <div class="bg-secondary bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                        <i class="bi bi-building text-secondary" style="font-size: 2rem;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col">
                                                @php
                                                    $namaSetting = collect($groupedSettings[$groupKey])->firstWhere('key', 'institution_name');
                                                    $alamatSetting = collect($groupedSettings[$groupKey])->firstWhere('key', 'institution_address');
                                                @endphp
                                                <h5 class="mb-1">{{ $namaSetting?->value ?? 'Nama Institusi' }}</h5>
                                                <p class="text-muted mb-0 small">{{ $alamatSetting?->value ?? 'Alamat Institusi' }}</p>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-info">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Data ini digunakan di kop surat cetakan
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @foreach($groupedSettings[$groupKey] as $setting)
                                    <div class="mb-4">
                                        <label for="{{ $setting->key }}" class="form-label fw-semibold">
                                            {{ $setting->label }}
                                        </label>
                                        
                                        @switch($setting->type)
                                            @case('text')
                                            @case('email')
                                            @case('number')
                                                <input type="{{ $setting->type }}" 
                                                       name="{{ $setting->key }}" 
                                                       id="{{ $setting->key }}" 
                                                       class="form-control" 
                                                       value="{{ old($setting->key, $setting->value) }}"
                                                       @if($setting->type === 'number') min="0" @endif>
                                                @break
                                            
                                            @case('textarea')
                                                <textarea name="{{ $setting->key }}" 
                                                          id="{{ $setting->key }}" 
                                                          class="form-control" 
                                                          rows="3">{{ old($setting->key, $setting->value) }}</textarea>
                                                @break
                                            
                                            @case('color')
                                                <div class="input-group" style="max-width: 200px;">
                                                    <input type="color" 
                                                           name="{{ $setting->key }}" 
                                                           id="{{ $setting->key }}" 
                                                           class="form-control form-control-color" 
                                                           value="{{ old($setting->key, $setting->value) }}">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           value="{{ $setting->value }}" 
                                                           readonly 
                                                           id="{{ $setting->key }}_text">
                                                </div>
                                                @break
                                            
                                            @case('password')
                                                <div class="input-group">
                                                    <input type="password" 
                                                           name="{{ $setting->key }}" 
                                                           id="{{ $setting->key }}" 
                                                           class="form-control" 
                                                           value="{{ old($setting->key, $setting->value) }}"
                                                           autocomplete="new-password">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('{{ $setting->key }}')">
                                                        <i class="bi bi-eye" id="{{ $setting->key }}_icon"></i>
                                                    </button>
                                                </div>
                                                @break
                                            
                                            @case('select')
                                                <select name="{{ $setting->key }}" 
                                                        id="{{ $setting->key }}" 
                                                        class="form-select">
                                                    @if($setting->key === 'mail_driver')
                                                        <option value="smtp" {{ $setting->value == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                        <option value="sendmail" {{ $setting->value == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                                        <option value="mailgun" {{ $setting->value == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                        <option value="ses" {{ $setting->value == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                                        <option value="log" {{ $setting->value == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                                                    @elseif($setting->key === 'mail_encryption')
                                                        <option value="tls" {{ $setting->value == 'tls' ? 'selected' : '' }}>TLS</option>
                                                        <option value="ssl" {{ $setting->value == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                        <option value="" {{ $setting->value == '' ? 'selected' : '' }}>None</option>
                                                    @elseif($setting->key === 'mode_krs')
                                                        <option value="pilihan" {{ $setting->value == 'pilihan' ? 'selected' : '' }}>Pilihan (Mahasiswa memilih sendiri)</option>
                                                        <option value="paket" {{ $setting->value == 'paket' ? 'selected' : '' }}>Paket (Otomatis berdasarkan kurikulum)</option>
                                                    @endif
                                                </select>
                                                @break
                                            
                                            @case('boolean')
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" 
                                                           name="{{ $setting->key }}" 
                                                           id="{{ $setting->key }}" 
                                                           class="form-check-input" 
                                                           value="1"
                                                           {{ $setting->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $setting->key }}">
                                                        {{ $setting->value == '1' ? 'Aktif' : 'Nonaktif' }}
                                                    </label>
                                                </div>
                                                @break
                                            
                                            @case('image')
                                                <div class="row align-items-center">
                                                    @if($setting->value)
                                                    <div class="col-auto">
                                                        <div class="border rounded p-2 bg-light">
                                                            <img src="{{ Storage::url($setting->value) }}" 
                                                                 alt="{{ $setting->label }}" 
                                                                 class="img-fluid" 
                                                                 style="max-height: 80px;">
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   name="remove_{{ $setting->key }}" 
                                                                   id="remove_{{ $setting->key }}" 
                                                                   class="form-check-input" 
                                                                   value="1">
                                                            <label class="form-check-label text-danger" for="remove_{{ $setting->key }}">
                                                                Hapus gambar
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="col">
                                                        <input type="file" 
                                                               name="{{ $setting->key }}" 
                                                               id="{{ $setting->key }}" 
                                                               class="form-control" 
                                                               accept="image/*">
                                                    </div>
                                                </div>
                                                @break
                                        @endswitch
                                        
                                        @if($setting->description)
                                        <small class="text-muted">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @php $first = false; @endphp
                        @endif
                    @endforeach
                </div>

                <!-- Save Button (Desktop) -->
                <div class="d-none d-lg-block mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-1"></i>Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.test-email') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-envelope-check me-2"></i>Test Pengiriman Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Pastikan Anda sudah menyimpan pengaturan email sebelum melakukan test.
                    </div>
                    <div class="mb-3">
                        <label for="test_email" class="form-label">Alamat Email Tujuan</label>
                        <input type="email" name="test_email" id="test_email" class="form-control" 
                               placeholder="contoh@email.com" required>
                        <small class="text-muted">Email test akan dikirim ke alamat ini.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Kirim Test Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sync color input with text display
document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
    colorInput.addEventListener('input', function() {
        const textInput = document.getElementById(this.id + '_text');
        if (textInput) {
            textInput.value = this.value;
        }
    });
});

// Toggle boolean label
document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const label = this.nextElementSibling;
        if (label) {
            label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
        }
    });
});

// Toggle password visibility
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
