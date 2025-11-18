<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/user-management.php';
require __DIR__ . '/data-import.php';
require __DIR__ . '/imported-data.php';
require __DIR__ . '/imports.php';
require __DIR__ . '/auth.php';
