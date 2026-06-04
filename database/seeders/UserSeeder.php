<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'fauziahmadzaki7@student.ub.ac.id',
            'password' => Hash::make('P@ssw0rd123'),
            'role' => 'admin',
            'is_profile_complete' => true,
            'is_active' => true,
        ]);

        $admin->profile()->create([
            'is_mahasiswa' => false,
            'bio' => 'Platform Administrator',
            'phone' => '08123456789',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=admin',
        ]);

        // Create Regular Test User
        $user = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_profile_complete' => true,
            'is_active' => true,
        ]);

        $user->profile()->create([
            'is_mahasiswa' => true,
            'bio' => 'Mahasiswa Teknik Informatika',
            'phone' => '08122334455',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=budi',
        ]);

        // Create some random users
        User::factory(5)->create(['is_profile_complete' => true])->each(function ($u) {
            $u->profile()->create([
                'is_mahasiswa' => fake()->boolean(),
                'bio' => fake()->sentence(),
                'phone' => fake()->phoneNumber(),
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $u->username,
            ]);
        });
    }
}
