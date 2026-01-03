<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TugasAkhir;
use App\Models\BimbinganTA;
use App\Models\SeminarProposal;
use App\Models\SidangTA;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TugasAkhirAllRolesTest extends TestCase
{
    // ================= ADMIN TESTS =================
    
    public function test_admin_can_access_tugas_akhir_index()
    {
        $admin = User::where('role', 'admin')->first();
        
        $response = $this->actingAs($admin)->get(route('admin.tugas-akhir.index'));
        
        $response->assertStatus(200);
    }

    public function test_admin_can_access_tugas_akhir_show()
    {
        $admin = User::where('role', 'admin')->first();
        $ta = TugasAkhir::first();
        
        if (!$ta) {
            $this->markTestSkipped('No TugasAkhir data available');
        }
        
        $response = $this->actingAs($admin)->get(route('admin.tugas-akhir.show', $ta->hashid));
        
        $response->assertStatus(200);
    }

    public function test_admin_can_access_seminar_index()
    {
        $admin = User::where('role', 'admin')->first();
        
        $response = $this->actingAs($admin)->get(route('admin.tugas-akhir.seminar.index'));
        
        $response->assertStatus(200);
    }

    public function test_admin_can_access_sidang_index()
    {
        $admin = User::where('role', 'admin')->first();
        
        $response = $this->actingAs($admin)->get(route('admin.tugas-akhir.sidang.index'));
        
        $response->assertStatus(200);
    }

    // ================= DOSEN TESTS =================
    
    public function test_dosen_can_access_tugas_akhir_index()
    {
        $dosen = User::where('role', 'dosen')->whereHas('dosen')->first();
        
        if (!$dosen) {
            $this->markTestSkipped('No Dosen user available');
        }
        
        $response = $this->actingAs($dosen)->get(route('dosen.tugas-akhir.index'));
        
        $response->assertStatus(200);
    }

    public function test_dosen_can_access_jadwal_bimbingan()
    {
        $dosen = User::where('role', 'dosen')->whereHas('dosen')->first();
        
        if (!$dosen) {
            $this->markTestSkipped('No Dosen user available');
        }
        
        $response = $this->actingAs($dosen)->get(route('dosen.tugas-akhir.jadwal-bimbingan'));
        
        $response->assertStatus(200);
    }

    public function test_dosen_can_access_riwayat_bimbingan()
    {
        $dosen = User::where('role', 'dosen')->whereHas('dosen')->first();
        
        if (!$dosen) {
            $this->markTestSkipped('No Dosen user available');
        }
        
        $response = $this->actingAs($dosen)->get(route('dosen.tugas-akhir.riwayat-bimbingan'));
        
        $response->assertStatus(200);
    }

    public function test_dosen_can_access_seminar_penguji()
    {
        $dosen = User::where('role', 'dosen')->whereHas('dosen')->first();
        
        if (!$dosen) {
            $this->markTestSkipped('No Dosen user available');
        }
        
        $response = $this->actingAs($dosen)->get(route('dosen.tugas-akhir.seminar-penguji'));
        
        $response->assertStatus(200);
    }

    public function test_dosen_can_access_sidang_penguji()
    {
        $dosen = User::where('role', 'dosen')->whereHas('dosen')->first();
        
        if (!$dosen) {
            $this->markTestSkipped('No Dosen user available');
        }
        
        $response = $this->actingAs($dosen)->get(route('dosen.tugas-akhir.sidang-penguji'));
        
        $response->assertStatus(200);
    }

    public function test_dosen_can_access_tugas_akhir_show_as_pembimbing()
    {
        $ta = TugasAkhir::whereNotNull('pembimbing_1_id')->with('pembimbing1.user')->first();
        
        if (!$ta || !$ta->pembimbing1 || !$ta->pembimbing1->user) {
            $this->markTestSkipped('No TugasAkhir with pembimbing available');
        }
        
        $dosen = $ta->pembimbing1->user;
        
        $response = $this->actingAs($dosen)->get(route('dosen.tugas-akhir.show', $ta->hashid));
        
        $response->assertStatus(200);
    }

    // ================= MAHASISWA TESTS =================
    
    public function test_mahasiswa_can_access_tugas_akhir_index()
    {
        $mahasiswa = User::where('role', 'mahasiswa')->whereHas('mahasiswa')->first();
        
        if (!$mahasiswa) {
            $this->markTestSkipped('No Mahasiswa user available');
        }
        
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tugas-akhir.index'));
        
        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_access_tugas_akhir_create()
    {
        // Find mahasiswa without TA
        $mahasiswa = User::where('role', 'mahasiswa')
            ->whereHas('mahasiswa', function($q) {
                $q->whereDoesntHave('tugasAkhir');
            })
            ->first();
        
        if (!$mahasiswa) {
            $this->markTestSkipped('No Mahasiswa without TA available');
        }
        
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tugas-akhir.create'));
        
        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_access_bimbingan()
    {
        $ta = TugasAkhir::whereNotNull('pembimbing_1_id')
            ->with('mahasiswa.user')
            ->first();
        
        if (!$ta || !$ta->mahasiswa || !$ta->mahasiswa->user) {
            $this->markTestSkipped('No TugasAkhir with mahasiswa available');
        }
        
        $mahasiswa = $ta->mahasiswa->user;
        
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tugas-akhir.bimbingan', $ta->hashid));
        
        $response->assertStatus(200);
    }
}
