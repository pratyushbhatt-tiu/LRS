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

        // Define and create permissions
        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.assign',
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'doc-types.view', 'doc-types.create', 'doc-types.edit', 'doc-types.delete',
            'recording-purposes.view', 'recording-purposes.create', 'recording-purposes.edit', 'recording-purposes.delete',
            'states.view', 'states.create', 'states.edit', 'states.delete',
            'counties.view', 'counties.create', 'counties.edit', 'counties.delete',
            'cities.view', 'cities.create', 'cities.edit', 'cities.delete',
            'files.view', 'files.create', 'files.edit', 'files.delete', 'files.process', 'files.approve', 'files.ship',
            'audit-logs.view',
            'reports.view', 'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // --- Role Assignments ---

        // 1. Admin - Full Spectrum Access
        $admin = Role::updateOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // 2. Accounting - Financial Control
        $accounting = Role::updateOrCreate(['name' => 'Accounting', 'guard_name' => 'web']);
        $accounting->syncPermissions([
            'files.view',
            'files.approve',
            'clients.view',
            'doc-types.view',
            'recording-purposes.view',
            'states.view',
            'counties.view',
            'cities.view',
            'reports.view',
            'reports.export',
        ]);

        // 3. Operations - Lifecycle Management
        $operations = Role::updateOrCreate(['name' => 'Operations', 'guard_name' => 'web']);
        $operations->syncPermissions([
            'files.view',
            'files.create',
            'files.edit',
            'files.process',
            'files.ship',
            'clients.view',
            'doc-types.view',
            'recording-purposes.view',
            'states.view',
            'counties.view',
            'cities.view',
            'reports.view',
        ]);

        // 4. QC - Quality Assurance
        $qc = Role::updateOrCreate(['name' => 'QC', 'guard_name' => 'web']);
        $qc->syncPermissions([
            'files.view',
            'files.process',
            'reports.view',
        ]);

        // 5. Read-Only - Audit/View only
        $readOnly = Role::updateOrCreate(['name' => 'Read-Only', 'guard_name' => 'web']);
        $readOnly->syncPermissions([
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
