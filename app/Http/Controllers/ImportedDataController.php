<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Datasets\ImportedDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Excel;
use App\Services\Export\ExportData;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportedDataController extends Controller
{
    /**
     * Shows file's dataset
     *
     * @param Request $request
     * @param array $importType
     * @param array $fileKey
     * @return View
     */
    public function showDataset(Request $request, array $importType, array $fileKey): View
    {
        $service = new ImportedDataService($importType, $fileKey, auth()->user());
        $data = $service->getPaginatedData($request);

        return view('imported-data.index', [
            'data' => $data,
            'importType' => $importType['config_key'],
            'fileKey' => $fileKey['config_key'],
            'displayHeaders' => $service->getDisplayHeaders(),
            'headersConfig' => $service->getHeadersConfig(),
            'canDelete' => $service->getCanDelete(),
            'searchQuery' => $request->input('search'),
            'pageTitle' => $importType['label'] . ' - ' . $fileKey['label']
        ]);
    }


    /**
     * Delete row action
     *
     * @param array $importType
     * @param array $fileKey
     * @param object $row
     * @return RedirectResponse
     */
    public function deleteRow(array $importType, array $fileKey, object $row): RedirectResponse
    {
        $service = new ImportedDataService($importType, $fileKey, auth()->user());

        if ($service->getCanDelete()) {
            $deletedCount = $service->deleteRowById($row->id);
            if ($deletedCount > 0) {
                return back()->with('success', "Row ID: {$row->id} has been delete successfully.");
            }
        }

        return back()->with('error', "Row ID: {$row->id} is not found.");
    }

    /**
     * Export dataset
     *
     * @param array $importType
     * @param array $fileKey
     * @param Excel $excel
     * @return BinaryFileResponse
     */
    public function exportDataset(array $importType, array $fileKey, Excel $excel): BinaryFileResponse
    {
        $search = request()->query('search', null);
        $fileName = "export_{$fileKey['config_key']}.xlsx";

        return $excel->download(new ExportData($fileKey, $search), $fileName);
    }

    /**
     * Fetch audits by row_id
     *
     * @param array $importType
     * @param array $fileKey
     * @param object $row
     * @return JsonResponse
     */
    public function showAudits(array $importType, array $fileKey, object $row): JsonResponse
    {
        $service = new ImportedDataService($importType, $fileKey, auth()->user());
        try {
            $audits = $service->getAuditsForRow($row->id);
            $auditData = [
                'title' => "Audit History for Row ID {$row->id}",
                'audits' => $audits
            ];

            return response()->json($auditData);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}