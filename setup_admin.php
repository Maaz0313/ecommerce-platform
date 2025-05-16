<?php
// This script creates an admin user or updates an existing user to have admin privileges

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Configuration
$adminEmail = 'admin@example.com';
$adminPassword = 'admin123'; // You should change this to something more secure
$adminName = 'Admin User';

// Check if admin user exists
$adminUser = User::where('email', $adminEmail)->first();

if ($adminUser) {
    echo "Admin user already exists. Updating admin privileges...\n";
    
    // Make sure user has admin privileges
    DB::table('users')
        ->where('email', $adminEmail)
        ->update(['is_admin' => true]);
        
    // Optionally update password
    $adminUser->password = Hash::make($adminPassword);
    $adminUser->save();
} else {
    echo "Creating new admin user...\n";
    
    // Create a new admin user
    $user = new User();
    $user->name = $adminName;
    $user->email = $adminEmail;
    $user->password = Hash::make($adminPassword);
    $user->is_admin = true;
    $user->save();
}

// Verify the change
$adminUser = User::where('email', $adminEmail)->first();
echo "Admin user details:\n";
echo "ID: " . $adminUser->id . "\n";
echo "Name: " . $adminUser->name . "\n";
echo "Email: " . $adminUser->email . "\n";
echo "is_admin (raw): " . var_export($adminUser->is_admin, true) . " (type: " . gettype($adminUser->is_admin) . ")\n";
echo "isAdmin(): " . var_export($adminUser->isAdmin(), true) . "\n";

// Clear cache
echo "Clearing cache...\n";
\Illuminate\Support\Facades\Artisan::call('cache:clear');
echo "Cache cleared.\n";

echo "Admin user setup completed successfully.\n";
echo "Login with: " . $adminEmail . " / " . $adminPassword . "\n";
