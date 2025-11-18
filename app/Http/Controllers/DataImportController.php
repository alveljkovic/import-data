<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Models\DataImportLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\DataImportRequest;
use App\Services\Imports\BackgroundDataImporter;

class DataImportController extends Controller
{
    /**
     * Show import form
     *
     * @return View
     */
    public function index(): View
    {
        $allConfigs = Config::get('data_import');
        $allowedConfigs = collect($allConfigs)->filter(function ($config) {
            return auth()->user()->can($config['permission_required']);
        });

        $logs = DataImportLog::with('user')->latest()->take(5)->get();
        return view('data-import.index', compact('allowedConfigs', 'logs'));
    }

    /**
     * Import data request
     *
     * @param DataImportRequest $request
     * @return RedirectResponse
     */
    public function import(DataImportRequest $request): RedirectResponse
    {
        try {
            $data = (new BackgroundDataImporter(auth()->user()))->process($request);
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with(
                'error',
                'An error occurred while processing the import request: ' . $th->getMessage()
            );
        }

        return redirect()->back()->with('success', $data['message']);
    }
}
