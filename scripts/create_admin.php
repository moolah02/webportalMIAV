<?php
/**
 * Tiny script to create an admin user and assign the super_admin role and all permissions.
 * Run: php scripts/create_admin.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the framework
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Configurable values
$password = 'Rv@Adm1n2025!#';
$email = 'admin@miav.com';
$name = 'admin';

try {
    if (User::where('email', $email)->exists()) {
        echo "USER_EXISTS\n";
        exit(0);
    }

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt($password),
        'email_verified_at' => now(),
    ]);

    // Ensure the role exists and assign it
    $role = Role::firstOrCreate(['name' => 'super_admin']);
    $user->assignRole($role);

    // Give all existing permissions (if any)
    $perms = Permission::all();
    if ($perms->count()) {
        $user->givePermissionTo($perms);
    }

    echo "CREATED\n";
    // Echo credentials so caller can capture them
    echo "EMAIL={$email}\n";
    echo "PASSWORD={$password}\n";
    exit(0);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
