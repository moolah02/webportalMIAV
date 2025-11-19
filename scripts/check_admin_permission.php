<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

$e = Employee::where('email','admin@miav.com')->first();
if (!$e) {
    echo "EMPLOYEE_NOT_FOUND\n";
    exit(1);
}

echo "FOUND: id={$e->id}, employee_id={$e->employee_id}, role_id={$e->role_id}\n";
echo "hasPermission('all'): " . ($e->hasPermission('all') ? 'yes' : 'no') . "\n";
exit(0);
