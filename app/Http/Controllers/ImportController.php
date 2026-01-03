<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\User;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Imports\MahasiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    /**
     * Show import page
     */
    public function index()
    {
        return view('import.index');
    }

    /**
     * Import Mahasiswa from CSV or Excel
     */
    public function mahasiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        // Import Excel menggunakan Maatwebsite
        if (in_array($extension, ['xlsx', 'xls'])) {
            $import = new MahasiswaImport();
            Excel::import($import, $file);
            
            $success = $import->getSuccessCount();
            $failed = $import->getFailedCount();
            $errors = $import->getErrors();
            
            $message = "Import selesai. Berhasil: {$success}, Gagal: {$failed}";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= "... dan " . (count($errors) - 5) . " error lainnya";
                }
            }
            
            return back()->with($failed > 0 && $success == 0 ? 'error' : 'success', $message);
        }
        
        // Import CSV
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header
        $header = fgetcsv($handle);
        
        $success = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 7) {
                    $failed++;
                    $errors[] = "Row tidak valid (kurang kolom)";
                    continue;
                }

                $nim = trim($row[0]);
                $nama = trim($row[1]);
                $email = trim($row[2]);
                $jenisKelamin = strtolower(trim($row[3])) == 'laki-laki' ? 'L' : 'P';
                $tempatLahir = trim($row[4]);
                $tanggalLahir = trim($row[5]);
                $programStudiKode = trim($row[6]);
                $angkatan = isset($row[7]) ? trim($row[7]) : date('Y');
                $alamat = isset($row[8]) ? trim($row[8]) : null;
                $telepon = isset($row[9]) ? trim($row[9]) : null;

                // Cek apakah NIM sudah ada
                if (Mahasiswa::where('nim', $nim)->exists()) {
                    $failed++;
                    $errors[] = "NIM {$nim} sudah terdaftar";
                    continue;
                }
                
                // Cek apakah email sudah ada
                if (User::where('email', $email)->exists()) {
                    $failed++;
                    $errors[] = "Email {$email} sudah terdaftar untuk NIM {$nim}";
                    continue;
                }

                // Cari program studi
                $programStudi = ProgramStudi::where('kode', $programStudiKode)->first();
                if (!$programStudi) {
                    $failed++;
                    $errors[] = "Program Studi dengan kode {$programStudiKode} tidak ditemukan untuk NIM {$nim}";
                    continue;
                }

                // Buat user
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($nim), // Password default = NIM
                    'role' => 'mahasiswa',
                    'is_active' => true
                ]);

                // Buat mahasiswa
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'nama' => $nama,
                    'email' => $email,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $alamat,
                    'telepon' => $telepon,
                    'program_studi_id' => $programStudi->id,
                    'angkatan' => $angkatan,
                    'status' => 'Aktif'
                ]);

                $success++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        fclose($handle);

        $message = "Import selesai. Berhasil: {$success}, Gagal: {$failed}";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= "... dan " . (count($errors) - 5) . " error lainnya";
            }
        }

        return back()->with($failed > 0 && $success == 0 ? 'error' : 'success', $message);
    }

    /**
     * Import Dosen from CSV
     */
    public function dosen(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header
        $header = fgetcsv($handle);
        
        $success = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 6) {
                    $failed++;
                    $errors[] = "Row tidak valid (kurang kolom)";
                    continue;
                }

                $nidn = trim($row[0]);
                $nama = trim($row[1]);
                $email = trim($row[2]);
                $jenisKelamin = strtolower(trim($row[3])) == 'laki-laki' ? 'L' : 'P';
                $tempatLahir = trim($row[4]);
                $tanggalLahir = trim($row[5]);
                $fakultasKode = isset($row[6]) ? trim($row[6]) : null;
                $alamat = isset($row[7]) ? trim($row[7]) : null;
                $telepon = isset($row[8]) ? trim($row[8]) : null;

                // Cek apakah NIDN sudah ada
                if (Dosen::where('nidn', $nidn)->exists()) {
                    $failed++;
                    $errors[] = "NIDN {$nidn} sudah terdaftar";
                    continue;
                }

                // Cari fakultas jika ada
                $fakultasId = null;
                if ($fakultasKode) {
                    $fakultas = Fakultas::where('kode', $fakultasKode)->first();
                    if ($fakultas) {
                        $fakultasId = $fakultas->id;
                    }
                }

                // Buat user
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($nidn), // Password default = NIDN
                    'role' => 'dosen',
                    'is_active' => true
                ]);

                // Buat dosen
                Dosen::create([
                    'user_id' => $user->id,
                    'nidn' => $nidn,
                    'nama' => $nama,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $alamat,
                    'telepon' => $telepon,
                    'fakultas_id' => $fakultasId,
                    'status' => 'aktif'
                ]);

                $success++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        fclose($handle);

        $message = "Import selesai. Berhasil: {$success}, Gagal: {$failed}";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= "... dan " . (count($errors) - 5) . " error lainnya";
            }
        }

        return back()->with($failed > 0 && $success == 0 ? 'error' : 'success', $message);
    }

    /**
     * Download template CSV for Mahasiswa
     */
    public function templateMahasiswa(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Export ke Excel menggunakan Maatwebsite
        if ($format === 'excel') {
            return Excel::download(
                new \App\Exports\MahasiswaTemplateExport(), 
                'template_import_mahasiswa.xlsx'
            );
        }
        
        // Export CSV (default)
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_mahasiswa.csv"',
        ];

        $columns = ['nim', 'nama', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'kode_prodi', 'angkatan', 'alamat', 'telepon'];
        $examples = [
            ['AUTO', 'Ahmad Fauzi', 'AUTO', 'Laki-laki', 'Jakarta', '2000-01-15', 'TI', date('Y'), 'Jl. Contoh No. 1', '08123456789'],
            ['AUTO', 'Siti Rahma', 'AUTO', 'Perempuan', 'Bandung', '2000-05-20', 'SI', date('Y'), 'Jl. Contoh No. 2', '08987654321'],
        ];

        $callback = function() use ($columns, $examples) {
            $file = fopen('php://output', 'w');
            // BOM untuk UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);
            foreach ($examples as $example) {
                fputcsv($file, $example);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download template CSV for Dosen
     */
    public function templateDosen()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_dosen.csv"',
        ];

        $columns = ['nidn', 'nama', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'kode_fakultas', 'alamat', 'telepon'];
        $example = ['1234567890', 'Dr. Budi Santoso', 'budi@siakad.ac.id', 'Laki-laki', 'Jakarta', '1980-05-20', 'FTI', 'Jl. Dosen No. 1', '08123456789'];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
