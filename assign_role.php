<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the first user
    $user = App\Models\User::first();
    
    if ($user) {
        echo "User found: {$user->name} ({$user->email})\n";
        
        // Assign customer role
        $user->assignRole('customer');
        
        echo "Customer role assigned successfully!\n";
        echo "User roles: " . $user->getRoleNames()->implode(', ') . "\n";
    } else {
        echo "No users found in database\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
