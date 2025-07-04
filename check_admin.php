<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Query directly for admin users
$adminUsers = DB::table('users')->where('is_admin', 1)->get();
echo "Admin users in database:\n";
print_r($adminUsers);

// Also check what's stored in the 'is_admin' column for a specific user
$adminUser = DB::table('users')->where('email', 'admin@example.com')->first();
echo "\nRaw data for admin@example.com:\n";
print_r($adminUser);

// Check through Eloquent model
echo "\nEloquent model for admin@example.com:\n";
$user = \App\Models\User::where('email', 'admin@example.com')->first();
echo "ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "is_admin (raw): " . var_export($user->is_admin, true) . " (type: " . gettype($user->is_admin) . ")\n";
echo "isAdmin(): " . var_export($user->isAdmin(), true) . "\n";

