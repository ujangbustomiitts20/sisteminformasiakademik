<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    protected $successCount = 0;
    protected $failedCount = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-index
            
            // Skip empty rows
            if (empty($row['nama']) && empty($row['kode_prodi'])) {
                continue;
            }

            // Cari program studi
            $programStudi = ProgramStudi::where('kode', trim($row['kode_prodi'] ?? ''))->first();
            if (!$programStudi) {
                $this->failedCount++;
                $this->errors[] = "Baris {$rowNumber}: Kode prodi '{$row['kode_prodi']}' tidak ditemukan";
                continue;
            }

            // Generate NIM otomatis jika kosong atau "AUTO"
            $nim = trim($row['nim'] ?? '');
            $isAutoNim = empty($nim) || strtoupper($nim) === 'AUTO';
            
            if ($isAutoNim) {
                $nim = Mahasiswa::generateNim($programStudi->id);
                if (!$nim) {
                    $this->failedCount++;
                    $this->errors[] = "Baris {$rowNumber}: Gagal generate NIM otomatis";
                    continue;
                }
            }

            // Generate email otomatis jika kosong atau "AUTO"
            $email = trim($row['email'] ?? '');
            $isAutoEmail = empty($email) || strtoupper($email) === 'AUTO';
            
            if ($isAutoEmail) {
                $email = Mahasiswa::generateEmail($nim);
            }

            // Validate row
            $validationRules = [
                'nama' => 'required|string|max:255',
                'jenis_kelamin' => 'required|string',
            ];
            
            // Validasi NIM jika bukan auto
            if (!$isAutoNim) {
                $validationRules['nim'] = 'required|string|unique:mahasiswa,nim';
            }
            
            // Validasi email jika bukan auto
            if (!$isAutoEmail) {
                $validationRules['email'] = 'required|email|unique:users,email';
            }

            $dataToValidate = $row->toArray();
            $dataToValidate['nim'] = $nim;
            $dataToValidate['email'] = $email;

            $validator = Validator::make($dataToValidate, $validationRules, [
                'nim.unique' => "Baris {$rowNumber}: NIM {$nim} sudah terdaftar",
                'nama.required' => "Baris {$rowNumber}: Nama wajib diisi",
                'email.email' => "Baris {$rowNumber}: Format email tidak valid",
                'email.unique' => "Baris {$rowNumber}: Email {$email} sudah terdaftar",
                'jenis_kelamin.required' => "Baris {$rowNumber}: Jenis kelamin wajib diisi",
            ]);

            if ($validator->fails()) {
                $this->failedCount++;
                $this->errors[] = $validator->errors()->first();
                continue;
            }

            // Cek duplikat NIM (untuk auto-generated)
            if (Mahasiswa::where('nim', $nim)->exists()) {
                $this->failedCount++;
                $this->errors[] = "Baris {$rowNumber}: NIM {$nim} sudah terdaftar";
                continue;
            }

            // Cek duplikat email
            if (User::where('email', $email)->exists()) {
                $this->failedCount++;
                $this->errors[] = "Baris {$rowNumber}: Email {$email} sudah terdaftar";
                continue;
            }

            try {
                DB::beginTransaction();

                // Tentukan jenis kelamin
                $jenisKelamin = strtolower(trim($row['jenis_kelamin']));
                $jenisKelamin = ($jenisKelamin == 'laki-laki' || $jenisKelamin == 'l' || $jenisKelamin == 'm') ? 'L' : 'P';

                // Buat user
                $user = User::create([
                    'name' => trim($row['nama']),
                    'email' => $email,
                    'password' => Hash::make($nim), // Password default = NIM
                    'role' => 'mahasiswa',
                    'is_active' => true,
                ]);

                // Buat mahasiswa
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'nama' => trim($row['nama']),
                    'email' => $email,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => !empty($row['tempat_lahir']) ? trim($row['tempat_lahir']) : null,
                    'tanggal_lahir' => !empty($row['tanggal_lahir']) ? $row['tanggal_lahir'] : null,
                    'alamat' => !empty($row['alamat']) ? trim($row['alamat']) : null,
                    'telepon' => !empty($row['telepon']) ? trim($row['telepon']) : null,
                    'no_hp' => !empty($row['no_hp']) ? trim($row['no_hp']) : null,
                    'program_studi_id' => $programStudi->id,
                    'angkatan' => !empty($row['angkatan']) ? (int) $row['angkatan'] : date('Y'),
                    'status' => 'Aktif',
                    // Data Kependudukan
                    'nik' => !empty($row['nik']) ? trim($row['nik']) : null,
                    'no_kk' => !empty($row['no_kk']) ? trim($row['no_kk']) : null,
                    'agama' => !empty($row['agama']) ? trim($row['agama']) : null,
                    'kewarganegaraan' => !empty($row['kewarganegaraan']) ? trim($row['kewarganegaraan']) : 'WNI',
                    'golongan_darah' => !empty($row['golongan_darah']) ? strtoupper(trim($row['golongan_darah'])) : null,
                    // Data Akademik Tambahan
                    'jalur_masuk' => !empty($row['jalur_masuk']) ? trim($row['jalur_masuk']) : null,
                    'asal_sekolah' => !empty($row['asal_sekolah']) ? trim($row['asal_sekolah']) : null,
                    'jurusan_asal' => !empty($row['jurusan_asal']) ? trim($row['jurusan_asal']) : null,
                    'tahun_lulus_sekolah' => !empty($row['tahun_lulus_sekolah']) ? (int) $row['tahun_lulus_sekolah'] : null,
                    'nilai_un' => !empty($row['nilai_un']) ? (float) $row['nilai_un'] : null,
                    'no_ijazah_sma' => !empty($row['no_ijazah_sma']) ? trim($row['no_ijazah_sma']) : null,
                    // Data Orang Tua
                    'nama_ayah' => !empty($row['nama_ayah']) ? trim($row['nama_ayah']) : null,
                    'nik_ayah' => !empty($row['nik_ayah']) ? trim($row['nik_ayah']) : null,
                    'pekerjaan_ayah' => !empty($row['pekerjaan_ayah']) ? trim($row['pekerjaan_ayah']) : null,
                    'pendidikan_ayah' => !empty($row['pendidikan_ayah']) ? trim($row['pendidikan_ayah']) : null,
                    'nama_ibu' => !empty($row['nama_ibu']) ? trim($row['nama_ibu']) : null,
                    'nik_ibu' => !empty($row['nik_ibu']) ? trim($row['nik_ibu']) : null,
                    'pekerjaan_ibu' => !empty($row['pekerjaan_ibu']) ? trim($row['pekerjaan_ibu']) : null,
                    'pendidikan_ibu' => !empty($row['pendidikan_ibu']) ? trim($row['pendidikan_ibu']) : null,
                    'no_hp_ortu' => !empty($row['no_hp_ortu']) ? trim($row['no_hp_ortu']) : null,
                    'email_ortu' => !empty($row['email_ortu']) ? trim($row['email_ortu']) : null,
                    'penghasilan_ortu' => !empty($row['penghasilan_ortu']) ? trim($row['penghasilan_ortu']) : null,
                    'alamat_ortu' => !empty($row['alamat_ortu']) ? trim($row['alamat_ortu']) : null,
                    // Data Wali
                    'nama_wali' => !empty($row['nama_wali']) ? trim($row['nama_wali']) : null,
                    'hubungan_wali' => !empty($row['hubungan_wali']) ? trim($row['hubungan_wali']) : null,
                    'pekerjaan_wali' => !empty($row['pekerjaan_wali']) ? trim($row['pekerjaan_wali']) : null,
                    'no_hp_wali' => !empty($row['no_hp_wali']) ? trim($row['no_hp_wali']) : null,
                    'alamat_wali' => !empty($row['alamat_wali']) ? trim($row['alamat_wali']) : null,
                    // Data Finansial
                    'no_rekening' => !empty($row['no_rekening']) ? trim($row['no_rekening']) : null,
                    'nama_bank' => !empty($row['nama_bank']) ? trim($row['nama_bank']) : null,
                    'atas_nama_rekening' => !empty($row['atas_nama_rekening']) ? trim($row['atas_nama_rekening']) : null,
                    'penerima_kip' => !empty($row['penerima_kip']) && (strtolower(trim($row['penerima_kip'])) == 'ya' || $row['penerima_kip'] == '1'),
                    'no_kip' => !empty($row['no_kip']) ? trim($row['no_kip']) : null,
                ]);

                DB::commit();
                $this->successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->failedCount++;
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
            }
        }
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
