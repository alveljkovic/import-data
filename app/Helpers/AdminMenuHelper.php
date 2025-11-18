<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class AdminMenuHelper
{
    /**
     * Generate structure for Imported Data menu based on config/data_import.php
     *
     * @return array
     */
    public static function getImportedDataMenu(): array
    {
        $menuItems = [];
        $importConfigs = config('data_import', []);

        foreach ($importConfigs as $importKey => $importConfig) {
            if (!isset($importConfig['files']) || empty($importConfig['files'])) {
                continue;
            }

            foreach ($importConfig['files'] as $fileKey => $fileConfig) {
                $url = '/imported-data/' . $importKey . '/' . $fileKey;
                $menuItems[] = [
                    'text' => $importConfig['label'] . ' - ' . $fileConfig['label'],
                    // 'url'  => route('imported-data.index', ['importType' => $importKey, 'fileKey' => $fileKey]),
                    'url'  => $url,
                    'icon' => 'fas fa-fw fa-file-alt',
                ];
            }
        }


        return [
            'text'    => 'Imported Data',
            'icon'    => 'fas fa-fw fa-chart-line',
            'submenu' => $menuItems,
            'can'     => !empty($menuItems) ? true : false,
        ];
    }
}
