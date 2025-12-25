# SIAKAD - Sistem Informasi Akademik

## Project Overview
Laravel 12 academic information system (SIAKAD) with integrated financial module. Uses MySQL/SQLite database with Blade templates and Vite for frontend assets.

## Architecture

### Key Modules
- **Akademik**: Mahasiswa, Dosen, KRS, Nilai, Jadwal, Kurikulum, Absensi
- **Keuangan**: Tagihan, TransaksiPembayaran, Beasiswa, Cicilan, Rekonsiliasi, Refund
- **Master Data**: Fakultas, ProgramStudi, MataKuliah, TahunAkademik, Ruangan

### User Roles
Routes are grouped by role middleware in `routes/web.php`:
- `admin` - Full system access
- `dosen` - Teaching, grading, student advisory
- `mahasiswa` - KRS, grades, payments, document requests

### Model Conventions
All models use `HashidsTrait` for URL-safe IDs (see `app/Traits/HashidsTrait.php`):
```php
use App\Traits\HashidsTrait;

class MyModel extends Model
{
    use HasFactory, HashidsTrait;
    protected $table = 'my_table'; // Always define explicitly
}
```

### Auto-Calculated Fields
`Tagihan` model auto-calculates in boot():
- `total_bayar = nominal - diskon + denda`
- `sisa_tagihan = total_bayar - jumlah_dibayar`
- Status auto-updates: 'Belum Bayar' → 'Cicilan' → 'Lunas'

`TransaksiPembayaran` auto-generates `no_transaksi` and updates parent `Tagihan.jumlah_dibayar` on creation/verification.

## Development Commands

```bash
# Setup project
composer setup

# Run development (serves app + queue + logs + vite)
composer dev

# Run tests
composer test

# Manual commands
php artisan serve
php artisan migrate
php artisan db:seed
php artisan db:seed --class=KeuanganDummySeeder
```

## Database Seeding

### Seeder Hierarchy
- `DatabaseSeeder` → calls `DummyDataSeeder` (full demo data)
- Module-specific seeders: `KeuanganDummySeeder`, `BimbinganAkademikSeeder`, etc.

### Important: Seeder Dependencies
Always check master data exists before seeding:
```php
$mahasiswas = Mahasiswa::all();
$tahunAkademik = TahunAkademik::first();
if ($mahasiswas->isEmpty() || !$tahunAkademik) {
    $this->command->warn('Master data not available!');
    return;
}
```

## File Locations

| Type | Location |
|------|----------|
| Controllers | `app/Http/Controllers/` |
| Models | `app/Models/` |
| Views | `resources/views/` |
| Migrations | `database/migrations/` |
| Seeders | `database/seeders/` |
| Helpers | `app/Helpers/helpers.php` (autoloaded) |
| Exports | `app/Exports/` (maatwebsite/excel) |
| Mail | `app/Mail/` |

## Key Packages
- `vinkla/hashids` - URL-safe model IDs
- `barryvdh/laravel-dompdf` - PDF generation
- `maatwebsite/excel` - Excel exports

## Common Patterns

### Controller Resource Pattern
```php
Route::resource('mahasiswa', MahasiswaController::class);
Route::resource('fakultas', FakultasController::class)->except(['show', 'create', 'edit']);
```

### Settings Helper
```php
// Get setting value (from app/Helpers/helpers.php)
$value = setting('key_name', 'default');
$allSettings = settings();
```

### Generating Unique Numbers
Models auto-generate numbers in `boot()`. For seeders, use counters:
```php
$counter = Model::count();
foreach ($items as $item) {
    $counter++;
    Model::create([
        'no_field' => 'PREFIX' . date('Ymd') . str_pad($counter, 4, '0', STR_PAD_LEFT),
    ]);
}
```

## Testing
```bash
php artisan test
php artisan test --filter=FeatureTestName
```

Tests located in `tests/Feature/` and `tests/Unit/`.
