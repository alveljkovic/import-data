<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:user-management', 'auth'])->group(function () {
    // Users CRUD
    Route::resource('users', UserController::class);

    // Permissions & roles management
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');

    // Form for assigning permissions/roles to a user
    Route::get('permissions/{user}/assign', [PermissionController::class, 'editAssignment'])
        ->name('permissions.assign.edit');
    Route::put('permissions/{user}/assign', [PermissionController::class, 'updateAssignment'])
        ->name('permissions.assign.update');

    // Edit & Delete individual permissions
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
});
