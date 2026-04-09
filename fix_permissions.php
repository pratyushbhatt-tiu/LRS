<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "Fixing Shipping Permissions...\n";

// 1. Create the permission if it doesn't exist
$permissionName = 'files.ship';
$permission = Permission::where('name', $permissionName)->first();
if (!$permission) {
    $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
    echo "Created permission: {$permissionName}\n";
} else {
    echo "Permission {$permissionName} already exists.\n";
}

// 2. Assign to Operations role
$opsRole = Role::where('name', 'Operations')->first();
if ($opsRole) {
    $opsRole->givePermissionTo($permission);
    echo "Assigned to Operations.\n";
}

// 3. Assign to Admin role
$adminRole = Role::where('name', 'Admin')->first();
if ($adminRole) {
    $adminRole->givePermissionTo($permission);
    echo "Assigned to Admin.\n";
}

// 4. Clear cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

echo "Done. Shipping should be visible now.\n";
