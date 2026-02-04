<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define explicit permissions (no wildcards)
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role management
            'roles.view',
            'roles.assign',

            // Master data management
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',

            'doc-types.view',
            'doc-types.create',
            'doc-types.edit',
            'doc-types.delete',

            'recording-purposes.view',
            'recording-purposes.create',
            'recording-purposes.edit',
            'recording-purposes.delete',

            'states.view',
            'states.create',
            'states.edit',
            'states.delete',

            'counties.view',
            'counties.create',
            'counties.edit',
            'counties.delete',

            'cities.view',
            'cities.create',
            'cities.edit',
            'cities.delete',

            // File management
            'files.view',
            'files.create',
            'files.edit',
            'files.delete',
            'files.process',
            'files.approve',

            // Audit logs
            'audit-logs.view',

            // Reports
            'reports.view',
            'reports.export',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin - Full access
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        // Operations - File processing and master data management
        $operations = Role::create(['name' => 'Operations']);
        $operations->givePermissionTo([
            'files.view',
            'files.create',
            'files.edit',
            'files.process',
            'clients.view',
            'doc-types.view',
            'recording-purposes.view',
            'states.view',
            'counties.view',
            'cities.view',
            'reports.view',
        ]);

        // QC - Quality control and approval
        $qc = Role::create(['name' => 'QC']);
        $qc->givePermissionTo([
            'files.view',
            'files.edit',
            'files.approve',
            'clients.view',
            'doc-types.view',
            'recording-purposes.view',
            'states.view',
            'counties.view',
            'cities.view',
            'reports.view',
        ]);

        // Accounting - View and reporting
        $accounting = Role::create(['name' => 'Accounting']);
        $accounting->givePermissionTo([
            'files.view',
            'clients.view',
            'doc-types.view',
            'recording-purposes.view',
            'states.view',
            'counties.view',
            'cities.view',
            'reports.view',
            'reports.export',
        ]);

        // Read-Only - View only access
        $readOnly = Role::create(['name' => 'Read-Only']);
        $readOnly->givePermissionTo([
            'files.view',
            'clients.view',
            'doc-types.view',
            'recording-purposes.view',
            'states.view',
            'counties.view',
            'cities.view',
        ]);
    }
}
