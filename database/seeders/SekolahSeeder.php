<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sekolah;
use App\Models\Provinsi;
use App\Models\Kabupaten;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Catatan: Data sekolah lengkap bisa diimport dari:
     * - Data Pokok Pendidikan (Dapodik): https://dapo.kemdikbud.go.id/
     * - Referensi Sekolah: https://referensi.data.kemdikbud.go.id/
     * 
     * Seeder ini berisi sample data untuk testing.
     */
    public function run(): void
    {
        $this->command->info('===========================================');
        $this->command->info('Seeding Sample Data Sekolah');
        $this->command->info('===========================================');

        // Cek apakah sudah ada data wilayah
        $provinsiCount = Provinsi::count();
        if ($provinsiCount == 0) {
            $this->command->warn('⚠️  Data wilayah belum tersedia.');
            $this->command->warn('   Jalankan: php artisan db:seed --class=WilayahSeederFast');
            $this->command->info('');
            $this->command->info('Membuat data sekolah dengan wilayah null...');
            $this->seedWithoutWilayah();
            return;
        }

        // Seed dengan data wilayah
        $this->seedWithWilayah();
    }

    /**
     * Seed sekolah tanpa relasi wilayah (untuk testing)
     */
    protected function seedWithoutWilayah(): void
    {
        $sekolahData = $this->getSampleSekolahData();
        
        $bar = $this->command->getOutput()->createProgressBar(count($sekolahData));
        $bar->start();

        foreach ($sekolahData as $data) {
            Sekolah::create([
                'npsn' => $data['npsn'],
                'nama' => $data['nama'],
                'jenjang' => $data['jenjang'],
                'status' => $data['status'],
                'provinsi_id' => null,
                'kabupaten_id' => null,
                'kecamatan_id' => null,
                'alamat' => $data['alamat'],
                'is_active' => true,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info("✅ Berhasil menambahkan " . count($sekolahData) . " sekolah (sample)");
    }

    /**
     * Seed sekolah dengan relasi wilayah
     */
    protected function seedWithWilayah(): void
    {
        // Data sekolah populer dari berbagai provinsi
        $sekolahPerProvinsi = [
            'DKI Jakarta' => [
                ['npsn' => '20100174', 'nama' => 'SMAN 1 Jakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Budi Utomo No.7'],
                ['npsn' => '20100175', 'nama' => 'SMAN 3 Jakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Setiabudi No.3'],
                ['npsn' => '20100176', 'nama' => 'SMAN 8 Jakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Taman Bukit Duri'],
                ['npsn' => '20100177', 'nama' => 'SMAN 68 Jakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Salemba Raya No.18'],
                ['npsn' => '20100178', 'nama' => 'SMKN 4 Jakarta', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. Rorotan'],
                ['npsn' => '20200101', 'nama' => 'SMA Labschool Jakarta', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Komplek UNJ Rawamangun'],
                ['npsn' => '20200102', 'nama' => 'SMA Al Azhar Kelapa Gading', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jl. Bukit Gading Raya'],
            ],
            'Jawa Barat' => [
                ['npsn' => '20210001', 'nama' => 'SMAN 3 Bandung', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Belitung No.8'],
                ['npsn' => '20210002', 'nama' => 'SMAN 5 Bandung', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Belitung No.6'],
                ['npsn' => '20210003', 'nama' => 'SMAN 1 Bogor', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Ir. H. Juanda No.16'],
                ['npsn' => '20210004', 'nama' => 'SMAN 1 Cirebon', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Dr. Wahidin No.1'],
                ['npsn' => '20210005', 'nama' => 'SMKN 1 Bandung', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. Wastukancana No.3'],
                ['npsn' => '20210006', 'nama' => 'SMA BPK Penabur Bandung', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jl. Dago'],
                ['npsn' => '20210007', 'nama' => 'MA Darul Ulum Jombang', 'jenjang' => 'MA', 'status' => 'Swasta', 'alamat' => 'Jl. Pesantren'],
            ],
            'Jawa Tengah' => [
                ['npsn' => '20310001', 'nama' => 'SMAN 1 Semarang', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Taman Menteri Supeno'],
                ['npsn' => '20310002', 'nama' => 'SMAN 3 Semarang', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Pemuda No.149'],
                ['npsn' => '20310003', 'nama' => 'SMAN 1 Surakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Monginsidi No.40'],
                ['npsn' => '20310004', 'nama' => 'SMKN 2 Semarang', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. Dr. Cipto No.121A'],
                ['npsn' => '20310005', 'nama' => 'MAN 1 Surakarta', 'jenjang' => 'MA', 'status' => 'Negeri', 'alamat' => 'Jl. Sumpah Pemuda'],
                ['npsn' => '20310006', 'nama' => 'SMA Semesta Semarang', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jl. Semarang-Boja Km.14'],
            ],
            'Jawa Timur' => [
                ['npsn' => '20510001', 'nama' => 'SMAN 1 Surabaya', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Wijaya Kusuma No.48'],
                ['npsn' => '20510002', 'nama' => 'SMAN 5 Surabaya', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Kusuma Bangsa No.21'],
                ['npsn' => '20510003', 'nama' => 'SMAN 1 Malang', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Tugu Utara'],
                ['npsn' => '20510004', 'nama' => 'SMAN 3 Malang', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Sultan Agung Utara No.7'],
                ['npsn' => '20510005', 'nama' => 'SMKN 1 Surabaya', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. SMEA No.4'],
                ['npsn' => '20510006', 'nama' => 'SMA Darul Ulum Jombang', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jl. PP Darul Ulum'],
                ['npsn' => '20510007', 'nama' => 'MAK Amanatul Ummah', 'jenjang' => 'MAK', 'status' => 'Swasta', 'alamat' => 'Jl. Siwalankerto Utara'],
            ],
            'Daerah Istimewa Yogyakarta' => [
                ['npsn' => '20400001', 'nama' => 'SMAN 1 Yogyakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. HOS Cokroaminoto No.10'],
                ['npsn' => '20400002', 'nama' => 'SMAN 3 Yogyakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Yos Sudarso No.7'],
                ['npsn' => '20400003', 'nama' => 'SMAN 8 Yogyakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Sidobali No.1'],
                ['npsn' => '20400004', 'nama' => 'SMKN 2 Yogyakarta', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. AM Sangaji No.47'],
                ['npsn' => '20400005', 'nama' => 'MAN 1 Yogyakarta', 'jenjang' => 'MA', 'status' => 'Negeri', 'alamat' => 'Jl. C. Simanjuntak No.60'],
                ['npsn' => '20400006', 'nama' => 'SMA De Britto Yogyakarta', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jl. Laksda Adisucipto'],
            ],
            'Bali' => [
                ['npsn' => '50110001', 'nama' => 'SMAN 1 Denpasar', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Kamboja No.4'],
                ['npsn' => '50110002', 'nama' => 'SMAN 3 Denpasar', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Imam Bonjol No.14'],
                ['npsn' => '50110003', 'nama' => 'SMKN 1 Denpasar', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. P. Batanta No.22'],
            ],
            'Sumatera Utara' => [
                ['npsn' => '10210001', 'nama' => 'SMAN 1 Medan', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Teuku Cik Ditiro No.1'],
                ['npsn' => '10210002', 'nama' => 'SMAN 2 Medan', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Karangsari No.435'],
                ['npsn' => '10210003', 'nama' => 'SMKN 1 Medan', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. Sindoro No.1'],
                ['npsn' => '10210004', 'nama' => 'SMA Sultan Iskandar Muda', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jl. Pancing'],
            ],
            'Sulawesi Selatan' => [
                ['npsn' => '40310001', 'nama' => 'SMAN 1 Makassar', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Gunung Bawakaraeng No.53'],
                ['npsn' => '40310002', 'nama' => 'SMAN 17 Makassar', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jl. Sunu No.8'],
                ['npsn' => '40310003', 'nama' => 'SMKN 2 Makassar', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Jl. Pancasila No.15'],
            ],
        ];

        $totalSekolah = 0;
        
        foreach ($sekolahPerProvinsi as $namaProvinsi => $sekolahList) {
            $provinsi = Provinsi::where('nama', $namaProvinsi)->first();
            
            if (!$provinsi) {
                $this->command->warn("⚠️  Provinsi '{$namaProvinsi}' tidak ditemukan, skip...");
                continue;
            }

            // Ambil kabupaten pertama di provinsi ini
            $kabupaten = Kabupaten::where('provinsi_id', $provinsi->id)->first();

            $this->command->info("📍 Menambahkan sekolah di {$namaProvinsi}...");

            foreach ($sekolahList as $data) {
                Sekolah::create([
                    'npsn' => $data['npsn'],
                    'nama' => $data['nama'],
                    'jenjang' => $data['jenjang'],
                    'status' => $data['status'],
                    'provinsi_id' => $provinsi->id,
                    'kabupaten_id' => $kabupaten?->id,
                    'kecamatan_id' => null,
                    'alamat' => $data['alamat'],
                    'is_active' => true,
                ]);
                $totalSekolah++;
            }
        }

        $this->command->info('');
        $this->command->info("✅ Berhasil menambahkan {$totalSekolah} sekolah");
    }

    /**
     * Get sample sekolah data untuk testing tanpa wilayah
     */
    protected function getSampleSekolahData(): array
    {
        return [
            ['npsn' => '20100001', 'nama' => 'SMAN 1 Jakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Jakarta'],
            ['npsn' => '20100002', 'nama' => 'SMAN 3 Bandung', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Bandung'],
            ['npsn' => '20100003', 'nama' => 'SMAN 1 Surabaya', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Surabaya'],
            ['npsn' => '20100004', 'nama' => 'SMAN 1 Yogyakarta', 'jenjang' => 'SMA', 'status' => 'Negeri', 'alamat' => 'Yogyakarta'],
            ['npsn' => '20100005', 'nama' => 'SMKN 1 Semarang', 'jenjang' => 'SMK', 'status' => 'Negeri', 'alamat' => 'Semarang'],
            ['npsn' => '20100006', 'nama' => 'MAN 1 Malang', 'jenjang' => 'MA', 'status' => 'Negeri', 'alamat' => 'Malang'],
            ['npsn' => '20100007', 'nama' => 'SMA Al Azhar Jakarta', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jakarta'],
            ['npsn' => '20100008', 'nama' => 'SMA Labschool Jakarta', 'jenjang' => 'SMA', 'status' => 'Swasta', 'alamat' => 'Jakarta'],
            ['npsn' => '20100009', 'nama' => 'SMK Telkom Bandung', 'jenjang' => 'SMK', 'status' => 'Swasta', 'alamat' => 'Bandung'],
            ['npsn' => '20100010', 'nama' => 'MAK Darul Ulum Jombang', 'jenjang' => 'MAK', 'status' => 'Swasta', 'alamat' => 'Jombang'],
        ];
    }
}
