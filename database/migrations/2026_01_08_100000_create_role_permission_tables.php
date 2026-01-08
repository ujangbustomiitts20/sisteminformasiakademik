<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Roles (menggantikan role string di users)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->string('deskripsi')->nullable();
            $table->string('warna', 20)->default('primary'); // Bootstrap badge color
            $table->boolean('is_system')->default(false); // Role bawaan sistem tidak bisa dihapus
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tabel Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 100)->unique();
            $table->string('grup', 50); // Grup permission: mahasiswa, keuangan, kepegawaian, dll
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });

        // Pivot Role-Permission
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Tabel Menu (untuk sidebar dinamis)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('nama', 100);
            $table->string('icon', 50)->nullable(); // Bootstrap icon class
            $table->string('route_name')->nullable(); // Named route
            $table->string('url')->nullable(); // URL manual jika tidak pakai route
            $table->string('permission_slug')->nullable(); // Permission yang dibutuhkan
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_divider')->default(false); // Untuk separator menu
            $table->string('badge_text')->nullable(); // Teks badge (misal: "New")
            $table->string('badge_color', 20)->nullable(); // Warna badge
            $table->timestamps();
        });

        // Pivot Role-Menu (menu mana saja yang bisa diakses role)
        Schema::create('role_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'menu_id']);
        });

        // Pivot User-Role (user bisa punya multiple roles)
        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false); // Role utama
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_menu');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
