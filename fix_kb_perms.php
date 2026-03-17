<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$perms = [
    'knowledgebase.view',
    'knowledgebase.create',
    'knowledgebase.edit',
    'knowledgebase.delete'
];

foreach ($perms as $p) {
    Permission::firstOrCreate(['name' => $p]);
    echo "Ensured: $p\n";
}

$admin = Role::where('name', 'Admin')->first();
if ($admin) {
    $admin->givePermissionTo($perms);
    echo "Assigned to Admin Role.\n";
}

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "Cache cleared.\n";
