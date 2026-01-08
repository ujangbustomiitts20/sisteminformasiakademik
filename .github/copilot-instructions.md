# SIAKAD - Sistem Informasi Akademik

Laravel 12 academic information system with integrated financial (Keuangan) and HR (Kepegawaian/SDM) modules. Uses MySQL/SQLite, Blade templates, Bootstrap 5 (CDN), Bootstrap Icons, and Vite.

## Quick Reference
| Pattern | Example |
|---------|---------|
| Get active semester | `TahunAkademik::getAktif()` |
| Model hashid | `$model->hashid`, `Model::findByHashid($hash)` |
| Decode hashid | `Model::decodeHashid($hashid)` → returns int ID |
| Format currency | `format_rupiah(1500000)` → "Rp 1.500.000" |
| Format date (ID) | `format_tanggal(now())` → "05 Januari 2026" |
| Get setting | `setting('nama_institusi', 'Default')` |
| PDF stream | `CetakService::stream('krs', 'cetak.krs', $data)` |
| Check user role | `$user->hasRole('admin')`, `$user->isKaprodi()`, `$user->canAccessDosenFeatures()` |
| Check permission | `$user->hasPermission('krs.approve')` |
| Status badge accessor | `$model->status_badge` → Bootstrap badge class (e.g., `success`, `warning`) |
| Model constants | `Model::STATUS`, `Model::KATEGORI`, `Model::TIPE` → Use in validation & views |

## Development Commands
```bash
composer setup    # Full setup: install, env, key, migrate, npm build
composer dev      # Concurrent: serve + queue + pail logs + vite (uses concurrently)
composer test     # Clear config + run tests
php artisan test --filter=FeatureTestName  # Run specific test
php artisan tinker --execute="code"       # Quick REPL for testing models
php artisan migrate:fresh --seed          # Reset DB with dummy data
```

**Note**: `composer dev` runs 4 concurrent processes with color-coded output using npx concurrently. If concurrently is missing, run `npm install` first.

## Creating New Features Checklist
1. **Model**: Add `HashidsTrait`, define `$table`, `$fillable`, `$casts`, relationships
2. **Migration**: Use date prefix `YYYY_MM_DD_HHMMSS_`, define foreign keys with `constrained()->cascadeOnDelete()`
3. **Controller**: Follow resource pattern with filters, stats, pagination (15 per page)
4. **Routes**: Add to `routes/web.php` with appropriate role middleware
5. **Views**: Extend `layouts.app`, use Bootstrap 5 components, modals for master data CRUD
6. **Seeder**: Check dependencies exist, use `firstOrCreate` for idempotency

## Migration File Naming
Use date-prefixed format: `YYYY_MM_DD_HHMMSS_description.php`. Main schema in `2024_01_01_000001_create_siakad_tables.php`. Add columns via separate migrations:
```php
// 2026_01_04_200000_add_identity_fields_to_fakultas_and_program_studi.php
Schema::table('fakultas', function (Blueprint $table) {
    $table->string('singkatan', 20)->nullable()->after('nama');
});
```

## Architecture Overview

### Module Structure (4 major domains)
- **Akademik**: Mahasiswa, Dosen, KRS, Nilai, Jadwal, Kurikulum, Absensi, TugasAkhir, Wisuda, Yudisium, KonversiNilai, PeriodeUjian, Edom
- **Keuangan**: Tagihan, TransaksiPembayaran, Beasiswa, Cicilan, Rekonsiliasi, Refund, Tarif, Potongan, VirtualAccount
- **Kepegawaian (SDM)**: Pegawai, CutiPegawai, PresensiPegawai, PenugasanMutasi, KenaikanGajiBerkala, KenaikanPangkat, Pensiun, SaldoCuti
- **PMB (Penerimaan Mahasiswa Baru)**: CalonMahasiswa, GelombangPmb, JalurSeleksi, HasilSeleksi, DaftarUlang

### User Roles & Route Middleware
**Dual Role System**: Legacy string role (`User->role`) + Dynamic roles (`user_role` pivot table).

Routes in `routes/web.php` (~1500 lines) use `CheckRole` middleware:
```php
Route::middleware(['role:admin'])->group(function () { ... });
Route::middleware(['role:admin,dosen'])->group(function () { ... });  // Multiple roles
```
- `admin` - Full system access (route prefix: various)
- `dosen` - Teaching, grading, advising (route prefix: `portal-dosen/`)
- `mahasiswa` - Self-service academic portal (route prefix: `portal-mahasiswa/`)
- `kaprodi` - Program study head, KRS approval (route prefix: `kaprodi/`)
- `dekan` - Faculty dean oversight (route prefix: `dekan/`)

**Role/Permission Models** (new dynamic system in `app/Models/`):
- `Role` - Many-to-many with `Permission`, `Menu`, `User`
- `Permission` - Grouped by `grup` field (dashboard, akademik, keuangan, etc.)
- `Menu` - Hierarchical with `parent_id`, supports dynamic sidebar

**User Role Methods**:
```php
$user->hasRole('admin')           // Check legacy OR dynamic role
$user->hasAnyRole(['admin', 'dosen'])
$user->assignRole('kaprodi', isPrimary: true)
$user->hasPermission('krs.approve')
$user->isKaprodi()                // Check role OR dosen assignment in ProgramStudi
$user->canAccessDosenFeatures()   // Has dosen data attached
$user->getMenus()                 // Get dynamic menus for sidebar
```

**Note**: Dosen users can also have kaprodi/dekan privileges checked via `$user->isKaprodi()` and `$user->isDekan()`. The `CheckRole` middleware handles this automatically.

### Dynamic Menu System
The sidebar uses a dynamic menu system with database-driven menus. Menus are stored in `menus` table and assigned to roles via `role_menu` pivot table.

**Menu Model** (`app/Models/Menu.php`):
```php
// Menu fields: parent_id, nama, icon, route_name, url, permission_slug, urutan, is_active
// URL accessor auto-generates from route_name if url is empty
public function getUrlAttribute(): ?string {
    $rawUrl = $this->getRawOriginal('url');
    if (!empty($rawUrl)) return $rawUrl;
    if ($this->route_name && \Route::has($this->route_name)) {
        return route($this->route_name);
    }
    return '#';
}
```

**Adding New Menu Items** via `RolePermissionSeeder`:
```php
// In database/seeders/RolePermissionSeeder.php createMenus() method
$menus = [
    ['nama' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route_name' => 'dashboard', 'urutan' => 1],
    [
        'nama' => 'Akademik', 'icon' => 'bi-book', 'urutan' => 2,
        'children' => [
            ['nama' => 'Mahasiswa', 'icon' => 'bi-people', 'route_name' => 'mahasiswa.index', 'permission_slug' => 'mahasiswa.lihat', 'urutan' => 1],
        ],
    ],
];
// After adding: php artisan db:seed --class=RolePermissionSeeder
```

**CRITICAL**: Always verify `route_name` matches actual Laravel route names (check with `php artisan route:list --name=xxx`). Common mistake: using `users.index` when route is `user.index`.

**Sidebar Logic** in `layouts/app.blade.php`:
```blade
@php
    $userMenus = auth()->user()->getMenus();
    $useDynamicMenu = $userMenus->count() > 0;
@endphp
@if($useDynamicMenu)
    @foreach($userMenus as $menu)
        <a href="{{ $menu->url }}" class="nav-link {{ $menu->isActive() ? 'active' : '' }}">
            <i class="{{ $menu->icon }}"></i>{{ $menu->nama }}
        </a>
    @endforeach
@else
    {{-- Static fallback menu --}}
@endif
```

### File Locations
| Type | Location |
|------|----------|
| Controllers | `app/Http/Controllers/` (subdirs: `Admin/`, `Mahasiswa/`, `Api/`) |
| Models | `app/Models/` (115+ models) |
| Views | `resources/views/` (role-based subdirs: `admin/`, `mahasiswa/`, `kaprodi/`, `dekan/`, `dosen/`, `kepegawaian/`, `portal-pmb/`) |
| Layout | `resources/views/layouts/app.blade.php` (single unified layout with role-based sidebar) |
| Print Templates | `resources/views/cetak/` (see `resources/views/cetak/README.md` for components) |
| Helpers | `app/Helpers/helpers.php` (autoloaded via composer.json) |
| Exports | `app/Exports/` (Maatwebsite Excel exports with filters) |
| Services | `app/Services/` (e.g., `CetakService.php` for PDF generation) |
| Seeders | `database/seeders/` (40+ specialized seeders with dependency checks) |
| Traits | `app/Traits/` (`HashidsTrait` - required for all models) |
| Factories | `database/factories/` (only `UserFactory.php` - create models inline in tests) |

## Model Conventions (CRITICAL)

### HashidsTrait Required
**All models MUST use `HashidsTrait`** for URL-safe IDs. Route model binding auto-decodes hashids:
```php
use App\Traits\HashidsTrait;

class MyModel extends Model {
    use HasFactory, HashidsTrait;
    protected $table = 'my_table';  // Always define explicitly
}
```
Usage: `$model->hashid` | `Model::decodeHashid($hashid)` | `Model::findByHashid($hashid)`

### Constants for Enums/Options
Define options as `public const` arrays in models for validation and views (see `TemplateDokumen.php`, `Tagihan.php`):
```php
public const STATUS = ['diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];
public const KATEGORI = ['akademik' => 'Akademik', 'keuangan' => 'Keuangan'];
// Usage in validation:
Rule::in(array_keys(Model::STATUS))
// Usage in views:
@foreach(Model::STATUS as $key => $label) <option value="{{ $key }}">{{ $label }}</option> @endforeach
```

### Status Badge Accessor Pattern
Models with status fields should have a `status_badge` accessor for consistent Bootstrap styling:
```php
public function getStatusBadgeAttribute(): string {
    return match($this->status) {
        'diajukan' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger', default => 'secondary'
    };
}
// In views: <span class="badge bg-{{ $model->status_badge }}">{{ $model->status }}</span>
```

### Auto-Calculated Fields in boot()
Models use `creating`/`updating` hooks for auto-calculations (see `Tagihan.php`, `SaldoCuti.php`):
```php
protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
        if (empty($model->no_tagihan)) {
            $model->no_tagihan = self::generateNoTagihan();
        }
        $model->total_bayar = $model->nominal - $model->diskon + $model->denda;
    });
    static::updating(function ($model) {
        $model->total_bayar = $model->nominal - $model->diskon + $model->denda;
        // Auto update status based on payment
        if ($model->sisa_tagihan <= 0) $model->status = 'Lunas';
    });
}
```
Number pattern: `PREFIX + date('Ym') + str_pad(counter, 5, '0', STR_PAD_LEFT)` → `INV20260100001`

### Static Helper Methods Pattern
Use `getOrCreate()` for lookup-or-initialize (see `SaldoCuti.php`, `RekapKehadiran.php`):
```php
public static function getOrCreate($dosenId, $pegawaiId, $tahun) {
    return self::firstOrCreate(
        ['dosen_id' => $dosenId, 'tahun' => $tahun],
        ['sisa_cuti' => 12, 'jatah_cuti' => 12]  // defaults
    );
}
```

### TahunAkademik Pattern
Use `TahunAkademik::getAktif()` for current semester context. Always provide fallback:
```php
$tahunAkademikAktif = TahunAkademik::getAktif();
// Check if user has data in active semester, fallback to last available
$hasData = Model::where('tahun_akademik_id', $tahunAkademikAktif?->id)->exists();
$tahunAkademik = $hasData ? $tahunAkademikAktif : $userTahunList->first();
```
Semester values: `Ganjil`, `Genap`, `Pendek`

## View & Frontend Patterns

### Single Layout Architecture
All views extend `@extends('layouts.app')` - a unified Bootstrap 5 layout with role-based sidebar:
```blade
@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
    {{-- Content here --}}
@endsection
```
Frontend stack: Bootstrap 5 (CDN), Bootstrap Icons, Inter font, Vite for asset bundling. No TailwindCSS in views (despite package.json entry).

### Bootstrap 5 Components
Use Bootstrap utilities and components consistently:
- Cards with `.card`, `.card-header`, `.card-body`
- Badges for status: `<span class="badge bg-{{ status_color }}">`
- Tables with `.table`, `.table-striped`, `.table-hover`
- Modals for create/edit forms (no dedicated create/edit pages for master data)
- Alert messages: `@if(session('success'))` with `.alert-success`

## Controller Patterns

### Standard Resource with Filters
Controllers typically have index with filters, stats, and pagination:
```php
public function index(Request $request) {
    $query = Model::with(['relation1', 'relation2']);
    if ($request->filled('status')) $query->where('status', $request->status);
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nama', 'like', "%{$request->search}%")
              ->orWhereHas('relation', fn($q) => $q->where('nama', 'like', "%{$request->search}%"));
        });
    }
    $items = $query->orderBy('created_at', 'desc')->paginate(15);
    $stats = ['total' => Model::count(), 'pending' => Model::where('status', 'diajukan')->count()];
    return view('module.index', compact('items', 'stats'));
}
```

### Approval Workflow Pattern
Status-based workflows (cuti, tagihan, KRS) follow this pattern:
```php
public function approve(Request $request, Model $model) {
    if ($model->status != 'diajukan') {
        return redirect()->back()->with('error', 'Status tidak valid');
    }
    $model->update([
        'status' => 'disetujui',
        'disetujui_oleh' => auth()->id(),
        'tanggal_disetujui' => now()
    ]);
    return redirect()->back()->with('success', 'Berhasil disetujui');
}
```

### Route Naming Conventions
```php
// Standard resource
Route::resource('mahasiswa', MahasiswaController::class);

// Module prefix with named routes
Route::prefix('kepegawaian/cuti')->name('kepegawaian.cuti.')->group(function () {
    Route::get('/', [CutiPegawaiController::class, 'index'])->name('index');
    Route::post('/{cuti}/approve', [..., 'approve'])->name('approve');
});

// Modal-only (no dedicated create/edit pages)
Route::resource('fakultas', FakultasController::class)->except(['show', 'create', 'edit']);
```

## Database Seeding

### Seeder Hierarchy
`DatabaseSeeder` → `DummyDataSeeder` (full demo) or `SiakadSeeder` (minimal). 40+ specialized seeders.

### Always Check Dependencies
```php
public function run(): void {
    $dosens = Dosen::all();
    $tahunAkademik = TahunAkademik::first();
    if ($dosens->isEmpty() || !$tahunAkademik) {
        $this->command->warn('Master data not available!');
        return;
    }
    // Use firstOrCreate for idempotent seeding
    foreach ($dosens->take(5) as $dosen) {
        CutiPegawai::firstOrCreate(
            ['dosen_id' => $dosen->id, 'tanggal_mulai' => now()->subDays(5)->toDateString()],
            ['jenis_cuti' => 'tahunan', 'status' => 'diajukan', ...]
        );
    }
}
```

## Data Migration (Legacy Database)
Migration scripts in project root connect to source database `itts_sikad` at `192.168.120.121`:
```php
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');
// Map source fields (UPPERCASE) to local snake_case
$tahunAkademikMap = [...]; // Build lookup maps first
// Use chunked queries for large tables (5000 per chunk)
// Match by unique keys: ['mata_kuliah_id', 'tahun_akademik_id', 'kelas']
```
Key tables mapping: `makul` → `mata_kuliah`, `kelaskuliah_jadwal` → `jadwal_kuliah`

## Helper Functions (autoloaded from `app/Helpers/helpers.php`)
```php
setting('key_name', 'default')     // Get single setting
settings()                         // Get all settings array
format_rupiah(1500000)             // "Rp 1.500.000"
format_tanggal(now())              // "02 Januari 2026"
konfigurasi_cetak('krs')           // Get print config for KRS
```

## PDF/Print Generation
Use `CetakService` with configurable templates (see `resources/views/cetak/README.md`):
```php
use App\Services\CetakService;
return CetakService::stream('krs', 'cetak.krs', $data, 'krs.pdf');
return CetakService::download('transkrip', 'cetak.transkrip', $data);
```
Print views extend `@extends('cetak.layouts.master')` with components: `kop-surat`, `signature`, `signature-double`.

## Excel Export Pattern
Exports in `app/Exports/` implement Maatwebsite interfaces:
```php
class MahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize {
    protected $request;
    public function __construct(Request $request) { $this->request = $request; }
    public function collection() { return Mahasiswa::with([...])->get(); }
}
```

## Key Packages
- `vinkla/hashids` - URL-safe model IDs via `HashidsTrait`
- `barryvdh/laravel-dompdf` - PDF generation via `CetakService`
- `maatwebsite/excel` - Excel exports/imports
- `laravel/pail` - Real-time log viewing (included in `composer dev`)

## Testing
Tests use `RefreshDatabase` trait. Only `UserFactory.php` exists - other models use inline creation or have factories generated on demand:
```php
protected function setUp(): void {
    parent::setUp();
    $this->admin = User::factory()->create(['role' => 'admin']);
    // Other models: use factory if available, otherwise create inline
    $this->prodi = ProgramStudi::factory()->create(['nama' => 'Teknik Informatika', 'kode' => 'TI']);
}
public function test_admin_can_access_dashboard() {
    $response = $this->actingAs($this->admin)->get(route('pmb.dashboard'));
    $response->assertStatus(200)->assertViewIs('pmb.dashboard');
}
```

**Testing Tips**:
- Always create dependencies (User, ProgramStudi, TahunAkademik) before testing models that reference them
- Use `actingAs($user)` for authenticated routes
- Test files in `tests/Feature/` - see `PmbModuleTest.php` for comprehensive examples
- Create factories for models as needed (see existing `UserFactory.php` pattern)
- Run specific tests: `php artisan test --filter=FeatureTestName`
- For tests without `RefreshDatabase`, query existing data: `User::where('role', 'admin')->first()`
- Skip tests gracefully when data is unavailable: `$this->markTestSkipped('No data available')`

## Common Gotchas
- **HashidsTrait**: All models MUST use it - forgetting causes 404s on route model binding
- **TahunAkademik**: Always check `TahunAkademik::getAktif()` isn't null before queries
- **Modal forms**: Master data (Fakultas, Ruangan) use modals, not separate create/edit views
- **Route parameters**: Laravel auto-pluralizes - `fakultas` becomes `$fakulta` in controller methods
- **Semester context**: Most queries need `tahun_akademik_id` filter; users should see their data even in inactive semesters
- **Status values**: Use Indonesian lowercase: `diajukan`, `disetujui`, `ditolak`, `lunas`, `aktif`
- **User role checks**: Use `$user->canAccessDosenFeatures()` not just `$user->isDosen()` for dosen route access
- **Foreign keys**: Always use `constrained()->cascadeOnDelete()` or `constrained()->nullOnDelete()` in migrations
- **Checkbox handling**: In controllers, use `$request->has('field')` for boolean checkbox values
- **Dynamic Menu route_name**: Must match exact Laravel route name (e.g., `user.index` not `users.index`). Verify with `php artisan route:list --name=xxx`
- **Menu URL accessor**: Use `$menu->getRawOriginal('url')` to get raw database value, `$menu->url` auto-generates from route_name

## Indonesian Language Context
This is an Indonesian academic system. Common terms:
- Mahasiswa (student), Dosen (lecturer), Prodi (study program), Fakultas (faculty)
- KRS (course registration), KHS (grade report), Transkrip (transcript)
- Cuti (leave), Presensi (attendance), Tagihan (billing), Beasiswa (scholarship)
- Status values: `diajukan`, `disetujui`, `ditolak`, `draft`, `lunas`, `aktif`
