<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $existingAdmin = User::where('email', 'admin@company.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('Admin user already exists with email: admin@company.com');
            return;
        }

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => Hash::make('adminpassword'),
            'employee_id' => 'ADMIN001',
            'department' => 'Administration',
            'position' => 'Administrator',
            'role' => 'admin',
        ]);

        // Generate QR code for admin
        $admin->generateQRCode();

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@company.com');
        $this->command->info('Password: adminpassword');
        $this->command->info('Employee ID: ADMIN001');
    }
}
