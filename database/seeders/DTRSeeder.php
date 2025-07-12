<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DTR;
use Carbon\Carbon;

class DTRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@company.com',
                'password' => bcrypt('adminpassword'),
                'employee_id' => 'ADMIN001',
                'department' => 'Administration',
                'position' => 'Administrator',
                'role' => 'admin',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@company.com',
                'password' => bcrypt('password'),
                'employee_id' => 'EMP001',
                'department' => 'Engineering',
                'position' => 'Software Engineer',
                'role' => 'employee',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'password' => bcrypt('password'),
                'employee_id' => 'EMP002',
                'department' => 'HR',
                'position' => 'HR Manager',
                'role' => 'employee',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@company.com',
                'password' => bcrypt('password'),
                'employee_id' => 'EMP003',
                'department' => 'Marketing',
                'position' => 'Marketing Specialist',
                'role' => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->generateQRCode(); // This will create the QR code
        }

        // Create some DTR records for the past week
        $users = User::all();
        $today = Carbon::today();

        foreach ($users as $user) {
            // Create DTR records for the past 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                $timeIn = $date->copy()->setTime(8, 0, 0); // 8:00 AM
                $breakStart = $date->copy()->setTime(12, 0, 0); // 12:00 PM
                $breakEnd = $date->copy()->setTime(13, 0, 0); // 1:00 PM
                $timeOut = $date->copy()->setTime(17, 0, 0); // 5:00 PM

                // For today, only clock in if it's before 5 PM
                if ($date->isToday() && now()->hour < 17) {
                    $timeOut = null;
                    $breakEnd = null;
                }

                DTR::create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'break_start' => $breakStart,
                    'break_end' => $breakEnd,
                    'break_hours' => $breakEnd ? 1.0 : 0,
                    'total_hours' => $timeOut ? 8.0 : 0,
                    'status' => 'present',
                ]);
            }
        }
    }
}
