<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset Cached Roles/Permissions (Wajib agar tidak error cache)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Role 'Superadmin' jika belum ada
        $role = Role::firstOrCreate(
            ['name' => 'Superadmin', 'guard_name' => 'web']
        );

        // 3. Buat User Superadmin
        // Password diambil dari SUPERADMIN_PASSWORD di .env; jika kosong dibuatkan acak.
        // (Jangan hardcode password di sini — repo ini publik.)
        $email    = env('SUPERADMIN_EMAIL', 'superadmin@gmail.com');
        $password = env('SUPERADMIN_PASSWORD') ?: \Illuminate\Support\Str::password(16, symbols: false);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => 'Super Administrator',
                'username'          => 'superadmin', // Pastikan username diisi
                'password'          => Hash::make($password),
                'no_wa'             => '08123456789',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );

        // 4. Assign Role Superadmin ke User tersebut
        if (!$user->hasRole('Superadmin')) {
            $user->assignRole($role);
        }

        if ($user->wasRecentlyCreated) {
            $this->command->info('User Superadmin berhasil dibuat!');
            $this->command->info("Email: {$email}");
            $this->command->info("Pass : {$password}   <- simpan, lalu ganti setelah login");
        } else {
            $this->command->info("User Superadmin ({$email}) sudah ada — password tidak diubah.");
        }
    }
}
