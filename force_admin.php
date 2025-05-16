<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Update using raw SQL to make sure the admin flag is set correctly
$affected = DB::statement("UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com'");

echo "Updated admin user. Rows affected: " . ($affected ? 'Yes' : 'No') . "\n";

// Verify the change
$adminUser = DB::table('users')->where('email', 'admin@example.com')->first();
echo "Raw database value after update:\n";
print_r($adminUser);

// Clear cache
echo "Clearing cache...\n";
\Illuminate\Support\Facades\Artisan::call('cache:clear');
echo "Cache cleared.\n";

// Now check through Eloquent
$user = \App\Models\User::where('email', 'admin@example.com')->first();
echo "Eloquent model after update:\n";
echo "ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "is_admin (raw): " . var_export($user->is_admin, true) . " (type: " . gettype($user->is_admin) . ")\n";
echo "isAdmin(): " . var_export($user->isAdmin(), true) . "\n";