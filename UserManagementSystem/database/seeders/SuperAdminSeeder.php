<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { User::create([
        'name' => 'SuperAdmin2',
        'email' => 'superadmin2@example.com',
        'password' => Hash::make('Super@1234'), // Use a strong password
        'role' => 'SuperAdmin',
    ]);

    // Create Admin
    User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('Admin@1234'), // Use a strong password
        'role' => 'Admin',
    ]);
    
    }
}
