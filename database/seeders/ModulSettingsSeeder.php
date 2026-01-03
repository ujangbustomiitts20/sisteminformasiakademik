<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // PKL / Magang Settings
            [
                'key' => 'pkl_min_semester',
                'value' => '6',
                'type' => 'number',
                'group' => 'pkl',
                'label' => 'Semester Minimal PKL',
                'description' => 'Semester minimal untuk mengikuti PKL',
                'order' => 1
            ],
            [
                'key' => 'pkl_min_sks',
                'value' => '100',
                'type' => 'number',
                'group' => 'pkl',
                'label' => 'SKS Minimal PKL',
                'description' => 'SKS minimal untuk mengikuti PKL',
                'order' => 2
            ],
            [
                'key' => 'pkl_min_log_entries',
                'value' => '20',
                'type' => 'number',
                'group' => 'pkl',
                'label' => 'Minimal Log Kegiatan',
                'description' => 'Jumlah minimal log kegiatan PKL',
                'order' => 3
            ],
            
            // Tugas Akhir Settings
            [
                'key' => 'ta_min_semester',
                'value' => '7',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'Semester Minimal TA',
                'description' => 'Semester minimal untuk mengajukan Tugas Akhir',
                'order' => 1
            ],
            [
                'key' => 'ta_min_sks',
                'value' => '120',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'SKS Minimal TA',
                'description' => 'SKS minimal untuk mengajukan Tugas Akhir',
                'order' => 2
            ],
            [
                'key' => 'ta_min_bimbingan_seminar',
                'value' => '3',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'Min Bimbingan Seminar',
                'description' => 'Jumlah minimal bimbingan sebelum seminar proposal',
                'order' => 3
            ],
            [
                'key' => 'ta_min_bimbingan_sidang',
                'value' => '8',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'Min Bimbingan Sidang',
                'description' => 'Jumlah minimal bimbingan sebelum sidang TA',
                'order' => 4
            ],
            [
                'key' => 'ta_passing_grade_seminar',
                'value' => '70',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'Nilai Lulus Seminar',
                'description' => 'Nilai minimal lulus seminar proposal',
                'order' => 5
            ],
            [
                'key' => 'ta_passing_grade_sidang',
                'value' => '70',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'Nilai Lulus Sidang',
                'description' => 'Nilai minimal lulus sidang TA',
                'order' => 6
            ],
            [
                'key' => 'ta_max_revisi_days',
                'value' => '14',
                'type' => 'number',
                'group' => 'tugas_akhir',
                'label' => 'Batas Hari Revisi',
                'description' => 'Batas hari untuk revisi setelah sidang',
                'order' => 7
            ],
            
            // Konversi Nilai Settings
            [
                'key' => 'konversi_min_nilai',
                'value' => 'C',
                'type' => 'text',
                'group' => 'konversi',
                'label' => 'Nilai Minimal Konversi',
                'description' => 'Nilai minimal yang dapat dikonversi',
                'order' => 1
            ],
            [
                'key' => 'konversi_max_sks',
                'value' => '60',
                'type' => 'number',
                'group' => 'konversi',
                'label' => 'Maks SKS Konversi',
                'description' => 'Maksimal SKS yang dapat dikonversi',
                'order' => 2
            ],
            [
                'key' => 'konversi_similarity_threshold',
                'value' => '70',
                'type' => 'number',
                'group' => 'konversi',
                'label' => 'Threshold Kemiripan',
                'description' => 'Persentase kemiripan silabus minimal untuk konversi',
                'order' => 3
            ],
            
            // Notifikasi Settings
            [
                'key' => 'notif_email_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Email Notifikasi',
                'description' => 'Aktifkan notifikasi email',
                'order' => 1
            ],
            [
                'key' => 'notif_reminder_days',
                'value' => '3',
                'type' => 'number',
                'group' => 'notification',
                'label' => 'Hari Reminder',
                'description' => 'Hari sebelum deadline untuk kirim reminder',
                'order' => 2
            ],
            
            // Upload Settings
            [
                'key' => 'upload_max_size_mb',
                'value' => '10',
                'type' => 'number',
                'group' => 'upload',
                'label' => 'Maks Ukuran Upload',
                'description' => 'Maksimal ukuran file upload (MB)',
                'order' => 1
            ],
            [
                'key' => 'upload_allowed_types',
                'value' => 'pdf,doc,docx,jpg,jpeg,png,zip',
                'type' => 'text',
                'group' => 'upload',
                'label' => 'Tipe File Diizinkan',
                'description' => 'Tipe file yang diizinkan untuk upload',
                'order' => 2
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'label' => $setting['label'],
                    'description' => $setting['description'] ?? null,
                    'order' => $setting['order'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Module settings seeded successfully!');
    }
}
