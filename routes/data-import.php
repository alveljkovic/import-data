<?php

use App\Http\Controllers\DataImportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:data-import', 'auth'])->group(function () {
    // Import data form
    Route::get('data-import', [DataImportController::class, 'index'])->name('data.import.index');

    // Import data submission and job initiation
    Route::post('data-import', [DataImportController::class, 'import'])->name('data.import.import');
});
