<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;



// require __DIR__.'/auth.php';
// routes/web.php
use Illuminate\Support\Facades\Artisan;


// Route::get('/run-seeder', function () {
//     Artisan::call('db:seed', [
//         '--class' => 'RoleSeeder',
//         '--force' => true, // force in production
//     ]);
//     return 'Seeder has been run!';
// });

// // routes/web.php

// Route::get('/check-roles', function () {
//     $roles = Role::all(); 
//     return $roles; // returns JSON list of all roles
// });

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

Route::get('/create-users', function () {
    // Admin user
    $admin = User::firstOrCreate(
        ['email' => 'admin@gmail.com'], // check if exists
        [
            'name' => 'Admin User',
            'password' => Hash::make('87654321')
        ]
    );
    $admin->assignRole('admin');

    // Seller user
    $seller = User::firstOrCreate(
        ['email' => 'seller@gmail.com'], // check if exists
        [
            'name' => 'Seller User',
            'password' => Hash::make('87654321')
        ]
    );
    $seller->assignRole('seller');

    return "Admin and Seller created successfully!";
});

