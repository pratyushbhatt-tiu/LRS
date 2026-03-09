<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder class for creating standardized demonstration users.
 * Automatically assigns specific roles to each user for testing workflows and access control.
 */
class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds to populate test users.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lrs.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Operations User
        $operations = User::create([
            'name' => 'Operations User',
            'email' => 'operations@lrs.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $operations->assignRole('Operations');

        // QC User
        $qc = User::create([
            'name' => 'QC User',
            'email' => 'qc@lrs.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $qc->assignRole('QC');

        // Accounting User
        $accounting = User::create([
            'name' => 'Accounting User',
            'email' => 'accounting@lrs.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $accounting->assignRole('Accounting');

        // Read-Only User
        $readOnly = User::create([
            'name' => 'Read-Only User',
            'email' => 'readonly@lrs.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $readOnly->assignRole('Read-Only');
    }
}
