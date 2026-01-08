<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPermissions();
        $this->createRoles();
        $this->createMenus();
        $this->assignRolesToExistingUsers();
    }

    /**
     * Create default permissions.
     */
    protected function createPermissions(): void
    {
        $permissions = [
            // Dashboard
            ['nama' => 'Dashboard Admin', 'slug' => 'dashboard.admin', 'grup' => 'dashboard'],
            ['nama' => 'Dashboard Dosen', 'slug' => 'dashboard.dosen', 'grup' => 'dashboard'],
            ['nama' => 'Dashboard Mahasiswa', 'slug' => 'dashboard.mahasiswa', 'grup' => 'dashboard'],
            ['nama' => 'Dashboard Kaprodi', 'slug' => 'dashboard.kaprodi', 'grup' => 'dashboard'],
            ['nama' => 'Dashboard Dekan', 'slug' => 'dashboard.dekan', 'grup' => 'dashboard'],

            // Mahasiswa
            ['nama' => 'Lihat Mahasiswa', 'slug' => 'mahasiswa.lihat', 'grup' => 'mahasiswa'],
            ['nama' => 'Tambah Mahasiswa', 'slug' => 'mahasiswa.tambah', 'grup' => 'mahasiswa'],
            ['nama' => 'Edit Mahasiswa', 'slug' => 'mahasiswa.edit', 'grup' => 'mahasiswa'],
            ['nama' => 'Hapus Mahasiswa', 'slug' => 'mahasiswa.hapus', 'grup' => 'mahasiswa'],
            ['nama' => 'Export Mahasiswa', 'slug' => 'mahasiswa.export', 'grup' => 'mahasiswa'],

            // Dosen
            ['nama' => 'Lihat Dosen', 'slug' => 'dosen.lihat', 'grup' => 'dosen'],
            ['nama' => 'Tambah Dosen', 'slug' => 'dosen.tambah', 'grup' => 'dosen'],
            ['nama' => 'Edit Dosen', 'slug' => 'dosen.edit', 'grup' => 'dosen'],
            ['nama' => 'Hapus Dosen', 'slug' => 'dosen.hapus', 'grup' => 'dosen'],
            ['nama' => 'Export Dosen', 'slug' => 'dosen.export', 'grup' => 'dosen'],

            // KRS
            ['nama' => 'Lihat KRS', 'slug' => 'krs.lihat', 'grup' => 'akademik'],
            ['nama' => 'Kelola KRS', 'slug' => 'krs.kelola', 'grup' => 'akademik'],
            ['nama' => 'Approve KRS', 'slug' => 'krs.approve', 'grup' => 'akademik'],

            // Nilai
            ['nama' => 'Lihat Nilai', 'slug' => 'nilai.lihat', 'grup' => 'akademik'],
            ['nama' => 'Input Nilai', 'slug' => 'nilai.input', 'grup' => 'akademik'],
            ['nama' => 'Export Nilai', 'slug' => 'nilai.export', 'grup' => 'akademik'],

            // Jadwal
            ['nama' => 'Lihat Jadwal', 'slug' => 'jadwal.lihat', 'grup' => 'akademik'],
            ['nama' => 'Kelola Jadwal', 'slug' => 'jadwal.kelola', 'grup' => 'akademik'],

            // Absensi
            ['nama' => 'Lihat Absensi', 'slug' => 'absensi.lihat', 'grup' => 'akademik'],
            ['nama' => 'Input Absensi', 'slug' => 'absensi.input', 'grup' => 'akademik'],

            // Keuangan
            ['nama' => 'Dashboard Keuangan', 'slug' => 'keuangan.dashboard', 'grup' => 'keuangan'],
            ['nama' => 'Lihat Tagihan', 'slug' => 'keuangan.tagihan.lihat', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Tagihan', 'slug' => 'keuangan.tagihan.kelola', 'grup' => 'keuangan'],
            ['nama' => 'Lihat Pembayaran', 'slug' => 'keuangan.pembayaran.lihat', 'grup' => 'keuangan'],
            ['nama' => 'Input Pembayaran', 'slug' => 'keuangan.pembayaran.input', 'grup' => 'keuangan'],
            ['nama' => 'Lihat Beasiswa', 'slug' => 'keuangan.beasiswa.lihat', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Beasiswa', 'slug' => 'keuangan.beasiswa.kelola', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Tarif', 'slug' => 'keuangan.tarif.lihat', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Denda', 'slug' => 'keuangan.denda', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Cicilan', 'slug' => 'keuangan.cicilan', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Notifikasi', 'slug' => 'keuangan.notifikasi', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Bank', 'slug' => 'keuangan.bank', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Rekonsiliasi', 'slug' => 'keuangan.rekonsiliasi', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Refund', 'slug' => 'keuangan.refund', 'grup' => 'keuangan'],
            ['nama' => 'Kelola Potongan', 'slug' => 'keuangan.potongan', 'grup' => 'keuangan'],
            ['nama' => 'Laporan Keuangan', 'slug' => 'laporan.keuangan', 'grup' => 'keuangan'],

            // Kepegawaian
            ['nama' => 'Dashboard Kepegawaian', 'slug' => 'kepegawaian.dashboard', 'grup' => 'kepegawaian'],
            ['nama' => 'Lihat Pegawai', 'slug' => 'kepegawaian.pegawai.lihat', 'grup' => 'kepegawaian'],
            ['nama' => 'Kelola Pegawai', 'slug' => 'kepegawaian.pegawai.kelola', 'grup' => 'kepegawaian'],
            ['nama' => 'Lihat Cuti', 'slug' => 'kepegawaian.cuti.lihat', 'grup' => 'kepegawaian'],
            ['nama' => 'Kelola Cuti', 'slug' => 'kepegawaian.cuti.kelola', 'grup' => 'kepegawaian'],
            ['nama' => 'Lihat Presensi', 'slug' => 'kepegawaian.presensi.lihat', 'grup' => 'kepegawaian'],
            ['nama' => 'Kelola Presensi', 'slug' => 'kepegawaian.presensi.kelola', 'grup' => 'kepegawaian'],
            ['nama' => 'Kelola Izin', 'slug' => 'kepegawaian.izin', 'grup' => 'kepegawaian'],
            ['nama' => 'Kelola Lembur', 'slug' => 'kepegawaian.lembur', 'grup' => 'kepegawaian'],
            ['nama' => 'Kelola SKP', 'slug' => 'kepegawaian.skp', 'grup' => 'kepegawaian'],

            // PMB
            ['nama' => 'Dashboard PMB', 'slug' => 'pmb.dashboard', 'grup' => 'pmb'],
            ['nama' => 'Lihat Pendaftar', 'slug' => 'pmb.pendaftar.lihat', 'grup' => 'pmb'],
            ['nama' => 'Kelola Pendaftar', 'slug' => 'pmb.pendaftar.kelola', 'grup' => 'pmb'],
            ['nama' => 'Lihat Seleksi', 'slug' => 'pmb.seleksi.lihat', 'grup' => 'pmb'],
            ['nama' => 'Kelola Seleksi', 'slug' => 'pmb.seleksi.kelola', 'grup' => 'pmb'],
            ['nama' => 'Kelola Gelombang', 'slug' => 'pmb.gelombang', 'grup' => 'pmb'],
            ['nama' => 'Kelola Jalur', 'slug' => 'pmb.jalur', 'grup' => 'pmb'],
            ['nama' => 'Kelola Daftar Ulang', 'slug' => 'pmb.daftar_ulang', 'grup' => 'pmb'],

            // Akademik Lanjutan
            ['nama' => 'Kelola Kurikulum', 'slug' => 'akademik.kurikulum', 'grup' => 'akademik'],
            ['nama' => 'Kelola Tugas Akhir', 'slug' => 'akademik.ta', 'grup' => 'akademik'],
            ['nama' => 'Kelola Wisuda', 'slug' => 'akademik.wisuda', 'grup' => 'akademik'],
            ['nama' => 'Kelola Yudisium', 'slug' => 'akademik.yudisium', 'grup' => 'akademik'],
            ['nama' => 'Kelola EDOM', 'slug' => 'akademik.edom', 'grup' => 'akademik'],

            // Master Data
            ['nama' => 'Lihat Master Data', 'slug' => 'master.lihat', 'grup' => 'master'],
            ['nama' => 'Kelola Master Data', 'slug' => 'master.kelola', 'grup' => 'master'],
            ['nama' => 'Kelola Fakultas', 'slug' => 'master.fakultas', 'grup' => 'master'],
            ['nama' => 'Kelola Program Studi', 'slug' => 'master.prodi', 'grup' => 'master'],
            ['nama' => 'Kelola Mata Kuliah', 'slug' => 'master.matakuliah', 'grup' => 'master'],
            ['nama' => 'Kelola Ruangan', 'slug' => 'master.ruangan', 'grup' => 'master'],
            ['nama' => 'Kelola Tahun Akademik', 'slug' => 'master.tahun_akademik', 'grup' => 'master'],
            ['nama' => 'Kelola Gedung', 'slug' => 'master.gedung', 'grup' => 'master'],
            ['nama' => 'Kelola Jam Kuliah', 'slug' => 'master.jam_kuliah', 'grup' => 'master'],

            // Pengaturan
            ['nama' => 'Lihat Pengaturan', 'slug' => 'pengaturan.lihat', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Pengaturan', 'slug' => 'pengaturan.kelola', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola User', 'slug' => 'pengaturan.user', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Role', 'slug' => 'pengaturan.role', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Menu', 'slug' => 'pengaturan.menu', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Permission', 'slug' => 'pengaturan.permission', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Pejabat', 'slug' => 'pengaturan.pejabat', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Jabatan', 'slug' => 'pengaturan.jabatan', 'grup' => 'pengaturan'],
            ['nama' => 'Kelola Template', 'slug' => 'pengaturan.template', 'grup' => 'pengaturan'],
            ['nama' => 'Import Data', 'slug' => 'pengaturan.import', 'grup' => 'pengaturan'],
            ['nama' => 'Activity Log', 'slug' => 'pengaturan.log', 'grup' => 'pengaturan'],
            ['nama' => 'Backup Database', 'slug' => 'pengaturan.backup', 'grup' => 'pengaturan'],
            ['nama' => 'Konfigurasi Cetak', 'slug' => 'pengaturan.cetak', 'grup' => 'pengaturan'],

            // Laporan
            ['nama' => 'Lihat Laporan', 'slug' => 'laporan.lihat', 'grup' => 'laporan'],
            ['nama' => 'Export Laporan', 'slug' => 'laporan.export', 'grup' => 'laporan'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $this->command->info('Permissions created successfully.');
    }

    /**
     * Create default roles.
     */
    protected function createRoles(): void
    {
        $roles = [
            [
                'nama' => 'Administrator',
                'slug' => 'admin',
                'deskripsi' => 'Akses penuh ke seluruh sistem',
                'warna' => 'danger',
                'is_system' => true,
                'urutan' => 1,
            ],
            [
                'nama' => 'Dosen',
                'slug' => 'dosen',
                'deskripsi' => 'Dosen pengajar',
                'warna' => 'primary',
                'is_system' => true,
                'urutan' => 2,
            ],
            [
                'nama' => 'Mahasiswa',
                'slug' => 'mahasiswa',
                'deskripsi' => 'Mahasiswa aktif',
                'warna' => 'success',
                'is_system' => true,
                'urutan' => 3,
            ],
            [
                'nama' => 'Kepala Program Studi',
                'slug' => 'kaprodi',
                'deskripsi' => 'Kepala Program Studi',
                'warna' => 'warning',
                'is_system' => true,
                'urutan' => 4,
            ],
            [
                'nama' => 'Dekan',
                'slug' => 'dekan',
                'deskripsi' => 'Dekan Fakultas',
                'warna' => 'info',
                'is_system' => true,
                'urutan' => 5,
            ],
            [
                'nama' => 'Staff Akademik',
                'slug' => 'staff_akademik',
                'deskripsi' => 'Staff bagian akademik',
                'warna' => 'secondary',
                'is_system' => false,
                'urutan' => 6,
            ],
            [
                'nama' => 'Staff Keuangan',
                'slug' => 'staff_keuangan',
                'deskripsi' => 'Staff bagian keuangan',
                'warna' => 'secondary',
                'is_system' => false,
                'urutan' => 7,
            ],
            [
                'nama' => 'Staff SDM',
                'slug' => 'staff_sdm',
                'deskripsi' => 'Staff bagian SDM/Kepegawaian',
                'warna' => 'secondary',
                'is_system' => false,
                'urutan' => 8,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            // Assign all permissions to admin
            if ($roleData['slug'] === 'admin') {
                $role->permissions()->sync(Permission::pluck('id'));
            }
        }

        // Assign specific permissions to other roles
        $this->assignPermissionsToRoles();

        $this->command->info('Roles created successfully.');
    }

    /**
     * Assign permissions to roles.
     */
    protected function assignPermissionsToRoles(): void
    {
        // Dosen permissions
        $dosenPermissions = Permission::whereIn('slug', [
            'dashboard.dosen',
            'jadwal.lihat',
            'absensi.lihat', 'absensi.input',
            'nilai.lihat', 'nilai.input',
            'mahasiswa.lihat',
        ])->pluck('id');
        Role::where('slug', 'dosen')->first()?->permissions()->syncWithoutDetaching($dosenPermissions);

        // Mahasiswa permissions
        $mahasiswaPermissions = Permission::whereIn('slug', [
            'dashboard.mahasiswa',
            'krs.lihat', 'krs.kelola',
            'nilai.lihat',
            'jadwal.lihat',
            'absensi.lihat',
        ])->pluck('id');
        Role::where('slug', 'mahasiswa')->first()?->permissions()->syncWithoutDetaching($mahasiswaPermissions);

        // Kaprodi permissions
        $kaprodiPermissions = Permission::whereIn('slug', [
            'dashboard.kaprodi', 'dashboard.dosen',
            'mahasiswa.lihat', 'mahasiswa.export',
            'dosen.lihat',
            'krs.lihat', 'krs.approve',
            'nilai.lihat', 'nilai.export',
            'jadwal.lihat',
        ])->pluck('id');
        Role::where('slug', 'kaprodi')->first()?->permissions()->syncWithoutDetaching($kaprodiPermissions);

        // Dekan permissions
        $dekanPermissions = Permission::whereIn('slug', [
            'dashboard.dekan', 'dashboard.kaprodi',
            'mahasiswa.lihat', 'mahasiswa.export',
            'dosen.lihat', 'dosen.export',
            'krs.lihat',
            'nilai.lihat', 'nilai.export',
            'laporan.lihat', 'laporan.export',
        ])->pluck('id');
        Role::where('slug', 'dekan')->first()?->permissions()->syncWithoutDetaching($dekanPermissions);

        // Staff Akademik permissions
        $staffAkademikPermissions = Permission::whereIn('slug', [
            'dashboard.admin',
            'mahasiswa.lihat', 'mahasiswa.tambah', 'mahasiswa.edit', 'mahasiswa.export',
            'dosen.lihat',
            'jadwal.lihat', 'jadwal.kelola',
            'krs.lihat',
            'nilai.lihat', 'nilai.export',
            'master.lihat', 'master.matakuliah', 'master.ruangan',
        ])->pluck('id');
        Role::where('slug', 'staff_akademik')->first()?->permissions()->syncWithoutDetaching($staffAkademikPermissions);

        // Staff Keuangan permissions
        $staffKeuanganPermissions = Permission::whereIn('slug', [
            'keuangan.dashboard',
            'keuangan.tagihan.lihat', 'keuangan.tagihan.kelola',
            'keuangan.pembayaran.lihat', 'keuangan.pembayaran.input',
            'keuangan.beasiswa.lihat', 'keuangan.beasiswa.kelola',
            'mahasiswa.lihat',
            'laporan.lihat', 'laporan.export',
        ])->pluck('id');
        Role::where('slug', 'staff_keuangan')->first()?->permissions()->syncWithoutDetaching($staffKeuanganPermissions);

        // Staff SDM permissions
        $staffSdmPermissions = Permission::whereIn('slug', [
            'kepegawaian.dashboard',
            'kepegawaian.pegawai.lihat', 'kepegawaian.pegawai.kelola',
            'kepegawaian.cuti.lihat', 'kepegawaian.cuti.kelola',
            'kepegawaian.presensi.lihat', 'kepegawaian.presensi.kelola',
            'dosen.lihat',
            'laporan.lihat', 'laporan.export',
        ])->pluck('id');
        Role::where('slug', 'staff_sdm')->first()?->permissions()->syncWithoutDetaching($staffSdmPermissions);
    }

    /**
     * Create default menus.
     */
    protected function createMenus(): void
    {
        // Complete menus based on existing sidebar structure

        $menus = [
            // Dashboard
            ['nama' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route_name' => 'dashboard', 'urutan' => 1],
            
            // Akademik
            [
                'nama' => 'Akademik', 'icon' => 'bi-book', 'urutan' => 2,
                'children' => [
                    ['nama' => 'Mahasiswa', 'icon' => 'bi-people', 'route_name' => 'mahasiswa.index', 'permission_slug' => 'mahasiswa.lihat', 'urutan' => 1],
                    ['nama' => 'Dosen', 'icon' => 'bi-person-badge', 'route_name' => 'dosen.index', 'permission_slug' => 'dosen.lihat', 'urutan' => 2],
                    ['nama' => 'Jadwal Kuliah', 'icon' => 'bi-calendar', 'route_name' => 'jadwal-kuliah.index', 'permission_slug' => 'jadwal.lihat', 'urutan' => 3],
                    ['nama' => 'KRS', 'icon' => 'bi-journal-text', 'route_name' => 'krs.index', 'permission_slug' => 'krs.lihat', 'urutan' => 4],
                    ['nama' => 'Persetujuan KRS', 'icon' => 'bi-check2-square', 'route_name' => 'krs.persetujuan', 'permission_slug' => 'krs.approve', 'urutan' => 5],
                    ['nama' => 'Nilai', 'icon' => 'bi-clipboard-data', 'route_name' => 'nilai.index', 'permission_slug' => 'nilai.lihat', 'urutan' => 6],
                    ['nama' => 'Absensi', 'icon' => 'bi-card-checklist', 'route_name' => 'absensi.index', 'permission_slug' => 'absensi.lihat', 'urutan' => 7],
                    ['nama' => 'Kurikulum', 'icon' => 'bi-list-columns', 'route_name' => 'kurikulum.index', 'permission_slug' => 'akademik.kurikulum', 'urutan' => 8],
                    ['nama' => 'Tugas Akhir', 'icon' => 'bi-journal-bookmark', 'route_name' => 'admin.tugas-akhir.index', 'permission_slug' => 'akademik.ta', 'urutan' => 9],
                    ['nama' => 'Wisuda', 'icon' => 'bi-mortarboard', 'route_name' => 'wisuda.index', 'permission_slug' => 'akademik.wisuda', 'urutan' => 10],
                    ['nama' => 'Yudisium', 'icon' => 'bi-award', 'route_name' => 'yudisium.index', 'permission_slug' => 'akademik.yudisium', 'urutan' => 11],
                    ['nama' => 'EDOM', 'icon' => 'bi-bar-chart-line', 'route_name' => 'admin.edom.index', 'permission_slug' => 'akademik.edom', 'urutan' => 12],
                ],
            ],
            
            // Keuangan
            [
                'nama' => 'Keuangan', 'icon' => 'bi-wallet2', 'urutan' => 3,
                'children' => [
                    ['nama' => 'Dashboard Keuangan', 'icon' => 'bi-graph-up-arrow', 'route_name' => 'keuangan.dashboard', 'permission_slug' => 'keuangan.dashboard', 'urutan' => 1],
                    ['nama' => 'Pembayaran PMB', 'icon' => 'bi-credit-card-2-front', 'route_name' => 'pmb.pembayaran.index', 'permission_slug' => 'keuangan.pembayaran.lihat', 'urutan' => 2],
                    ['nama' => 'Tarif', 'icon' => 'bi-tags', 'route_name' => 'tarif.index', 'permission_slug' => 'keuangan.tarif.lihat', 'urutan' => 3],
                    ['nama' => 'Tagihan', 'icon' => 'bi-receipt', 'route_name' => 'tagihan.index', 'permission_slug' => 'keuangan.tagihan.lihat', 'urutan' => 4],
                    ['nama' => 'Transaksi', 'icon' => 'bi-cash-stack', 'route_name' => 'transaksi-pembayaran.index', 'permission_slug' => 'keuangan.pembayaran.lihat', 'urutan' => 5],
                    ['nama' => 'Beasiswa', 'icon' => 'bi-award', 'route_name' => 'beasiswa.index', 'permission_slug' => 'keuangan.beasiswa.lihat', 'urutan' => 6],
                    ['nama' => 'Penerima Beasiswa', 'icon' => 'bi-people', 'route_name' => 'beasiswa.penerima.index', 'permission_slug' => 'keuangan.beasiswa.lihat', 'urutan' => 7],
                    ['nama' => 'Pengaturan Denda', 'icon' => 'bi-exclamation-triangle', 'route_name' => 'pengaturan-denda.index', 'permission_slug' => 'keuangan.denda', 'urutan' => 8],
                    ['nama' => 'Skema Cicilan', 'icon' => 'bi-calendar3-range', 'route_name' => 'skema-cicilan.index', 'permission_slug' => 'keuangan.cicilan', 'urutan' => 9],
                    ['nama' => 'Manajemen Cicilan', 'icon' => 'bi-list-check', 'route_name' => 'cicilan.index', 'permission_slug' => 'keuangan.cicilan', 'urutan' => 10],
                    ['nama' => 'Notifikasi Keuangan', 'icon' => 'bi-bell', 'route_name' => 'notifikasi.index', 'permission_slug' => 'keuangan.notifikasi', 'urutan' => 11],
                ],
            ],

            // Rekonsiliasi Bank
            [
                'nama' => 'Rekonsiliasi Bank', 'icon' => 'bi-bank', 'urutan' => 4,
                'children' => [
                    ['nama' => 'Akun Bank', 'icon' => 'bi-bank', 'route_name' => 'akun-bank.index', 'permission_slug' => 'keuangan.bank', 'urutan' => 1],
                    ['nama' => 'Mutasi Bank', 'icon' => 'bi-arrow-left-right', 'route_name' => 'mutasi-bank.index', 'permission_slug' => 'keuangan.bank', 'urutan' => 2],
                    ['nama' => 'Rekonsiliasi', 'icon' => 'bi-clipboard-check', 'route_name' => 'rekonsiliasi.index', 'permission_slug' => 'keuangan.rekonsiliasi', 'urutan' => 3],
                    ['nama' => 'Refund', 'icon' => 'bi-arrow-return-left', 'route_name' => 'keuangan.refund.index', 'permission_slug' => 'keuangan.refund', 'urutan' => 4],
                ],
            ],

            // Potongan & Diskon
            [
                'nama' => 'Potongan & Diskon', 'icon' => 'bi-percent', 'urutan' => 5,
                'children' => [
                    ['nama' => 'Jenis Potongan', 'icon' => 'bi-tags', 'route_name' => 'jenis-potongan.index', 'permission_slug' => 'keuangan.potongan', 'urutan' => 1],
                    ['nama' => 'Periode Diskon', 'icon' => 'bi-calendar-event', 'route_name' => 'periode-diskon.index', 'permission_slug' => 'keuangan.potongan', 'urutan' => 2],
                    ['nama' => 'Potongan Mahasiswa', 'icon' => 'bi-person-badge', 'route_name' => 'potongan-mahasiswa.index', 'permission_slug' => 'keuangan.potongan', 'urutan' => 3],
                ],
            ],

            // Laporan Keuangan
            [
                'nama' => 'Laporan Keuangan', 'icon' => 'bi-file-earmark-bar-graph', 'urutan' => 6,
                'children' => [
                    ['nama' => 'Lap. Pendapatan', 'icon' => 'bi-graph-up-arrow', 'route_name' => 'laporan.pendapatan', 'permission_slug' => 'laporan.keuangan', 'urutan' => 1],
                    ['nama' => 'Lap. Tunggakan', 'icon' => 'bi-exclamation-circle', 'route_name' => 'laporan.tunggakan', 'permission_slug' => 'laporan.keuangan', 'urutan' => 2],
                    ['nama' => 'Lap. Beasiswa', 'icon' => 'bi-mortarboard', 'route_name' => 'laporan.beasiswa', 'permission_slug' => 'laporan.keuangan', 'urutan' => 3],
                ],
            ],
            
            // Kepegawaian
            [
                'nama' => 'Kepegawaian', 'icon' => 'bi-briefcase', 'urutan' => 7,
                'children' => [
                    ['nama' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route_name' => 'kepegawaian.dashboard', 'permission_slug' => 'kepegawaian.dashboard', 'urutan' => 1],
                    ['nama' => 'Pegawai', 'icon' => 'bi-person-badge', 'route_name' => 'kepegawaian.pegawai.index', 'permission_slug' => 'kepegawaian.pegawai.lihat', 'urutan' => 2],
                    ['nama' => 'Presensi', 'icon' => 'bi-fingerprint', 'route_name' => 'kepegawaian.presensi.index', 'permission_slug' => 'kepegawaian.presensi.lihat', 'urutan' => 3],
                    ['nama' => 'Cuti', 'icon' => 'bi-calendar-x', 'route_name' => 'kepegawaian.cuti.index', 'permission_slug' => 'kepegawaian.cuti.lihat', 'urutan' => 4],
                    ['nama' => 'Izin Keluar', 'icon' => 'bi-door-open', 'route_name' => 'kepegawaian.izin-keluar.index', 'permission_slug' => 'kepegawaian.izin', 'urutan' => 5],
                    ['nama' => 'Lembur', 'icon' => 'bi-clock-history', 'route_name' => 'kepegawaian.lembur.index', 'permission_slug' => 'kepegawaian.lembur', 'urutan' => 6],
                    ['nama' => 'Saldo Cuti', 'icon' => 'bi-calendar-check', 'route_name' => 'kepegawaian.cuti.saldo', 'permission_slug' => 'kepegawaian.cuti.lihat', 'urutan' => 7],
                    ['nama' => 'SKP', 'icon' => 'bi-file-earmark-check', 'route_name' => 'kepegawaian.skp.index', 'permission_slug' => 'kepegawaian.skp', 'urutan' => 8],
                ],
            ],
            
            // PMB
            [
                'nama' => 'PMB', 'icon' => 'bi-mortarboard', 'urutan' => 8,
                'children' => [
                    ['nama' => 'Dashboard PMB', 'icon' => 'bi-speedometer2', 'route_name' => 'pmb.dashboard', 'permission_slug' => 'pmb.dashboard', 'urutan' => 1],
                    ['nama' => 'Gelombang', 'icon' => 'bi-calendar-range', 'route_name' => 'pmb.gelombang.index', 'permission_slug' => 'pmb.gelombang', 'urutan' => 2],
                    ['nama' => 'Jalur Seleksi', 'icon' => 'bi-signpost', 'route_name' => 'pmb.jalur-seleksi.index', 'permission_slug' => 'pmb.jalur', 'urutan' => 3],
                    ['nama' => 'Pendaftar', 'icon' => 'bi-people', 'route_name' => 'pmb.calon-mahasiswa.index', 'permission_slug' => 'pmb.pendaftar.lihat', 'urutan' => 4],
                    ['nama' => 'Seleksi', 'icon' => 'bi-check-circle', 'route_name' => 'pmb.seleksi.index', 'permission_slug' => 'pmb.seleksi.lihat', 'urutan' => 5],
                    ['nama' => 'Daftar Ulang', 'icon' => 'bi-clipboard-check', 'route_name' => 'pmb.daftar-ulang.index', 'permission_slug' => 'pmb.daftar_ulang', 'urutan' => 6],
                ],
            ],
            
            // Master Data
            [
                'nama' => 'Master Data', 'icon' => 'bi-grid', 'urutan' => 9,
                'children' => [
                    ['nama' => 'Fakultas', 'icon' => 'bi-building', 'route_name' => 'fakultas.index', 'permission_slug' => 'master.fakultas', 'urutan' => 1],
                    ['nama' => 'Program Studi', 'icon' => 'bi-diagram-3', 'route_name' => 'program-studi.index', 'permission_slug' => 'master.prodi', 'urutan' => 2],
                    ['nama' => 'Mata Kuliah', 'icon' => 'bi-book', 'route_name' => 'mata-kuliah.index', 'permission_slug' => 'master.matakuliah', 'urutan' => 3],
                    ['nama' => 'Ruangan', 'icon' => 'bi-door-open', 'route_name' => 'ruangan.index', 'permission_slug' => 'master.ruangan', 'urutan' => 4],
                    ['nama' => 'Tahun Akademik', 'icon' => 'bi-calendar-date', 'route_name' => 'tahun-akademik.index', 'permission_slug' => 'master.tahun_akademik', 'urutan' => 5],
                    // Gedung dan Jam Kuliah tidak aktif (route tidak tersedia)
                ],
            ],

            // Laporan Akademik
            [
                'nama' => 'Laporan & Statistik', 'icon' => 'bi-file-earmark-bar-graph', 'route_name' => 'laporan.index', 'urutan' => 10, 'permission_slug' => 'laporan.lihat',
            ],

            // Tools
            [
                'nama' => 'Tools & Pengaturan', 'icon' => 'bi-gear', 'urutan' => 11,
                'children' => [
                    ['nama' => 'Import Data', 'icon' => 'bi-upload', 'route_name' => 'import.index', 'permission_slug' => 'pengaturan.import', 'urutan' => 1],
                    ['nama' => 'Activity Log', 'icon' => 'bi-journal-text', 'route_name' => 'activity-log.index', 'permission_slug' => 'pengaturan.log', 'urutan' => 2],
                    ['nama' => 'Backup Database', 'icon' => 'bi-database-down', 'route_name' => 'backup.index', 'permission_slug' => 'pengaturan.backup', 'urutan' => 3],
                    ['nama' => 'Konfigurasi Cetak', 'icon' => 'bi-printer', 'route_name' => 'konfigurasi-cetak.index', 'permission_slug' => 'pengaturan.cetak', 'urutan' => 4],
                ],
            ],
            
            // Pengaturan
            [
                'nama' => 'Pengaturan', 'icon' => 'bi-gear-fill', 'urutan' => 99,
                'children' => [
                    ['nama' => 'Umum', 'icon' => 'bi-sliders', 'route_name' => 'settings.index', 'permission_slug' => 'pengaturan.lihat', 'urutan' => 1],
                    ['nama' => 'User', 'icon' => 'bi-people', 'route_name' => 'user.index', 'permission_slug' => 'pengaturan.user', 'urutan' => 2],
                    ['nama' => 'Role', 'icon' => 'bi-shield-check', 'route_name' => 'admin.roles.index', 'permission_slug' => 'pengaturan.role', 'urutan' => 3],
                    ['nama' => 'Menu', 'icon' => 'bi-list', 'route_name' => 'admin.menus.index', 'permission_slug' => 'pengaturan.menu', 'urutan' => 4],
                    ['nama' => 'Permission', 'icon' => 'bi-key', 'route_name' => 'admin.permissions.index', 'permission_slug' => 'pengaturan.permission', 'urutan' => 5],
                    ['nama' => 'Pejabat Akademik', 'icon' => 'bi-person-badge', 'route_name' => 'admin.pejabat-akademik.index', 'permission_slug' => 'pengaturan.pejabat', 'urutan' => 6],
                    ['nama' => 'Nama Jabatan', 'icon' => 'bi-briefcase', 'route_name' => 'admin.nama-jabatan.index', 'permission_slug' => 'pengaturan.jabatan', 'urutan' => 7],
                    ['nama' => 'Template Dokumen', 'icon' => 'bi-file-earmark-text', 'route_name' => 'admin.template-dokumen.index', 'permission_slug' => 'pengaturan.template', 'urutan' => 8],
                ],
            ],

            // Informasi
            [
                'nama' => 'Pengumuman', 'icon' => 'bi-megaphone', 'route_name' => 'pengumuman.index', 'urutan' => 96,
            ],
            [
                'nama' => 'Kalender Akademik', 'icon' => 'bi-calendar-event', 'route_name' => 'kalender.index', 'urutan' => 97,
            ],
        ];

        $adminRole = Role::where('slug', 'admin')->first();

        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $menu = Menu::firstOrCreate(
                ['nama' => $menuData['nama'], 'parent_id' => null],
                $menuData
            );

            // Assign to admin role
            if ($adminRole) {
                $menu->roles()->syncWithoutDetaching([$adminRole->id]);
            }

            // Create children
            foreach ($children as $childData) {
                $childData['parent_id'] = $menu->id;
                $child = Menu::firstOrCreate(
                    ['nama' => $childData['nama'], 'parent_id' => $menu->id],
                    $childData
                );

                // Assign to admin role
                if ($adminRole) {
                    $child->roles()->syncWithoutDetaching([$adminRole->id]);
                }
            }
        }

        $this->command->info('Menus created successfully.');
    }

    /**
     * Assign roles to existing users based on legacy role field.
     */
    protected function assignRolesToExistingUsers(): void
    {
        $users = User::whereNotNull('role')->get();

        foreach ($users as $user) {
            $role = Role::where('slug', $user->role)->first();
            
            if ($role) {
                $user->roles()->syncWithoutDetaching([
                    $role->id => ['is_primary' => true]
                ]);
            }
        }

        $this->command->info('Roles assigned to existing users.');
    }
}
