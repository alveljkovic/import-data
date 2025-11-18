<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportsController;

Route::middleware(['auth', 'permission:data-import'])
    ->group(function () {

        Route::get('imports', [ImportsController::class, 'index'])
            ->name('imports.index');

        Route::get('imports/{import}/logs', [ImportsController::class, 'showLogs'])
            ->name('imports.logs');
    });
