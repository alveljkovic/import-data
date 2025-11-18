<?php

use App\Http\Controllers\ImportedDataController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:data-import', 'auth'])
    ->prefix('imported-data')
    ->group(function () {

        // Show all rows dataset
        Route::get('{importType}/{fileKey}', [ImportedDataController::class, 'showDataset'])
            ->name('imported-data.index');

        // Delete row
        Route::delete('{importType}/{fileKey}/{rowId}', [ImportedDataController::class, 'deleteRow'])
            ->name('imported-data.delete');

        // Export rows
        Route::get('{importType}/{fileKey}/export', [ImportedDataController::class, 'exportDataset'])
            ->name('imported-data.export');

        // Show audits for row
        Route::get('{importType}/{fileKey}/{rowId}/audits', [ImportedDataController::class, 'showAudits'])
            ->name('imported-data.audits');
    });
