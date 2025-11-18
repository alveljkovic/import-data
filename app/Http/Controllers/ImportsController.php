<?php

namespace App\Http\Controllers;

use App\Models\DataImportLog;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ImportsController extends Controller
{
    /**
     * Show all imports
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $imports = DataImportLog::with('user')
                    ->orderByDesc('started_at')
                    ->paginate(20);

        return view('imports.index', compact('imports'));
    }

    /**
     * Fetch validation logs for modal
     *
     * @param DataImportLog $import
     * @return JsonResponse
     */
    public function showLogs(DataImportLog $import): JsonResponse
    {
        $errors = $import->errors;
        $errorsMap = ($errors) ? $import->errors->map(fn ($e) => [
                'number' => $e->row_number,
                'column' => $e->column_name,
                'value' => ($e->value) ? $e->value : 'N/A',
                'message' => $e->message,
                'created' => $e->created_at
            ])->toArray() :
            [];

        return response()->json([
            'title' => "Logs for import #{$import->id}",
            'logs'  => $errorsMap
        ]);
    }
}
