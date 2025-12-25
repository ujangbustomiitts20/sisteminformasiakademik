<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gunakan salah satu:
        // SiakadSeeder - Data minimal untuk testing
        // DummyDataSeeder - Data lengkap untuk demo
        
        $this->call([
            DummyDataSeeder::class,
        ]);
    }
}
