# SIAKAD - Sistem Informasi Akademik

Laravel 12 academic information system with integrated financial and HR modules. Uses MySQL/SQLite, Blade templates, Bootstrap 5, and Vite.

## Development Commands
```bash
composer setup    # Full setup: install, env, key, migrate, npm build
composer dev      # Concurrent: serve + queue + pail logs + vite (uses concurrently)
composer test     # Clear config + run tests
php artisan test --filter=FeatureTestName  # Run specific test
```

## Architecture Overview

### Module Structure (4 major domains)
- **Akademik**: Mahasiswa, Dosen, KRS, Nilai, Jadwal, Kurikulum, Absensi, TugasAkhir, Wisuda, Yudisium, KonversiNilai, PeriodeUjian, Edom
- **Keuangan**: Tagihan, TransaksiPembayaran, Beasiswa, Cicilan, Rekonsiliasi, Refund, Tarif, Potongan, VirtualAccount
- **Kepegawaian (SDM)**: Pegawai, CutiPegawai, PresensiPegawai, PenugasanMutasi, KenaikanGajiBerkala, KenaikanPangkat, Pensiun, SaldoCuti
- **PMB (Penerimaan Mahasiswa Baru)**: CalonMahasiswa, GelombangPmb, JalurSeleksi, HasilSeleksi, DaftarUlang

### User Roles & Route Middleware
Five roles managed via `app/Http/Middleware/CheckRole.php`. Routes in `routes/web.php` (~1500 lines):
```php
Route::middleware(['role:admin'])->group(function () { ... });
Route::middleware(['role:admin,dosen'])->group(function () { ... });  // Multiple roles
```
- `admin` - Full system access
- `dosen` - Teaching, grading, advising (portal at `/portal-dosen/`)
- `mahasiswa` - Self-service academic portal
- `kaprodi` - Program study head, KRS approval
- `dekan` - Faculty dean oversight

### File Locations
| Type | Location |
|------|----------|
| Controllers | `app/Http/Controllers/` (subdirs: `Admin/`, `Mahasiswa/`, `Api/`) |
| Models | `app/Models/` (110+ models) |
| Views | `resources/views/` (by role: `admin/`, `mahasiswa/`, `kaprodi/`, `dekan/`, `dosen/`, `kepegawaian/`) |
| Print Templates | `resources/views/cetak/` (see `resources/views/cetak/README.md`) |
| Helpers | `app/Helpers/helpers.php` (autoloaded) |
| Exports | `app/Exports/` (Maatwebsite Excel) |
| Services | `app/Services/` (e.g., `CetakService.php`) |
| Seeders | `database/seeders/` (35+ specialized seeders) |

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

### Auto-Calculated Fields in boot()
Models use `creating`/`updating` hooks for auto-calculations (see `Tagihan.php`):
```php
protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
        if (empty($model->no_tagihan)) {
            $model->no_tagihan = self::generateNoTagihan();
        }
        $model->total_bayar = $model->nominal - $model->diskon + $model->denda;
    });
}
```
Number pattern: `PREFIX + date('Ym') + str_pad(counter, 5, '0', STR_PAD_LEFT)` → `INV20260100001`

### Static Helper Methods Pattern
Use `getOrCreate()` for lookup-or-initialize (see `SaldoCuti`, `RekapKehadiran`):
```php
public static function getOrCreate($dosenId, $pegawaiId, $tahun) {
    return self::firstOrCreate(
        ['dosen_id' => $dosenId, 'tahun' => $tahun],
        ['sisa_cuti' => 12, ...]  // defaults
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
`DatabaseSeeder` → `DummyDataSeeder` (full demo) or `SiakadSeeder` (minimal). 35+ specialized seeders.

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

## Testing
Tests use `RefreshDatabase` trait. Create required fixtures in `setUp()`:
```php
protected function setUp(): void {
    parent::setUp();
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = ProgramStudi::factory()->create();
}
public function test_admin_can_access_dashboard() {
    $response = $this->actingAs($this->admin)->get(route('pmb.dashboard'));
    $response->assertStatus(200)->assertViewIs('pmb.dashboard');
}
```

## Indonesian Language Context
This is an Indonesian academic system. Common terms:
- Mahasiswa (student), Dosen (lecturer), Prodi (study program), Fakultas (faculty)
- KRS (course registration), KHS (grade report), Transkrip (transcript)
- Cuti (leave), Presensi (attendance), Tagihan (billing), Beasiswa (scholarship)
- Status values: `diajukan`, `disetujui`, `ditolak`, `draft`, `lunas`, `aktif`
