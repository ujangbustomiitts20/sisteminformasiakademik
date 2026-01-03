<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\CalonMahasiswa;
use App\Models\HasilSeleksi;
use App\Models\DaftarUlang;
use App\Models\ProgramStudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmbModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $periode;
    protected $gelombang;
    protected $jalur;
    protected $prodi;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create required data
        $this->prodi = ProgramStudi::factory()->create([
            'nama' => 'Teknik Informatika',
            'kode' => 'TI'
        ]);

        $this->periode = PeriodePmb::factory()->create([
            'nama' => 'PMB 2024/2025',
            'tahun_akademik' => '2024/2025',
            'is_active' => true,
        ]);

        $this->gelombang = GelombangPmb::factory()->create([
            'periode_pmb_id' => $this->periode->id,
            'nama' => 'Gelombang 1',
            'is_active' => true,
        ]);

        $this->jalur = JalurSeleksi::factory()->create([
            'nama' => 'Reguler',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_access_pmb_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.dashboard');
    }

    /** @test */
    public function admin_can_access_calon_mahasiswa_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.calon-mahasiswa.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.calon-mahasiswa.index');
    }

    /** @test */
    public function admin_can_create_calon_mahasiswa()
    {
        $data = [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'no_hp' => '081234567890',
            'nik' => '1234567890123456',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'alamat' => 'Jl. Test No. 1',
            'program_studi_id' => $this->prodi->id,
            'jalur_seleksi_id' => $this->jalur->id,
            'gelombang_pmb_id' => $this->gelombang->id,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('pmb.calon-mahasiswa.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('calon_mahasiswa', [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function admin_can_access_seleksi_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.seleksi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.seleksi.index');
    }

    /** @test */
    public function admin_can_access_input_nilai()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.seleksi.input-nilai'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.seleksi.input-nilai');
    }

    /** @test */
    public function admin_can_access_proses_seleksi()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.seleksi.proses'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.seleksi.proses');
    }

    /** @test */
    public function admin_can_access_hasil_seleksi()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.seleksi.hasil'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.seleksi.hasil');
    }

    /** @test */
    public function admin_can_access_pembayaran_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.pembayaran.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.pembayaran.index');
    }

    /** @test */
    public function admin_can_access_daftar_ulang_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.daftar-ulang.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.daftar-ulang.index');
    }

    /** @test */
    public function admin_can_access_generate_daftar_ulang()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('pmb.daftar-ulang.generate'));

        $response->assertStatus(200);
        $response->assertViewIs('pmb.daftar-ulang.generate');
    }

    /** @test */
    public function admin_can_generate_daftar_ulang_for_lulus_peserta()
    {
        // Create calon mahasiswa
        $camaba = CalonMahasiswa::factory()->create([
            'gelombang_pmb_id' => $this->gelombang->id,
            'program_studi_id' => $this->prodi->id,
            'jalur_seleksi_id' => $this->jalur->id,
            'status' => 'lulus',
        ]);

        // Create hasil seleksi lulus
        $hasil = HasilSeleksi::factory()->create([
            'calon_mahasiswa_id' => $camaba->id,
            'gelombang_pmb_id' => $this->gelombang->id,
            'program_studi_diterima_id' => $this->prodi->id,
            'status' => 'lulus',
            'nilai_total' => 85.5,
            'ranking' => 1,
        ]);

        $data = [
            'gelombang_id' => $this->gelombang->id,
            'biaya' => 5000000,
            'tanggal_expired' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('pmb.daftar-ulang.store-generate'), $data);

        $response->assertRedirect(route('pmb.daftar-ulang.index'));
        $this->assertDatabaseHas('daftar_ulang', [
            'calon_mahasiswa_id' => $camaba->id,
            'status' => 'menunggu_bayar',
        ]);
    }
}
