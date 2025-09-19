<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;



// require __DIR__.'/auth.php';
// routes/web.php
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;


Route::get('/run-seeder', function () {
    Artisan::call('db:seed', [
        '--class' => 'RoleSeeder',
        '--force' => true, // force in production
    ]);
    return 'Seeder has been run!';
});

// routes/web.php

Route::get('/check-roles', function () {
    $roles = Role::all(); 
    return $roles; // returns JSON list of all roles
});
