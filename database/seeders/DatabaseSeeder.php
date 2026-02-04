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
        // Seed in order: roles/permissions first, then users, then master data
        $this->call([
            RolesAndPermissionsSeeder::class,
            DemoUsersSeeder::class,
            MasterDataSeeder::class,
        ]);
    }
}
