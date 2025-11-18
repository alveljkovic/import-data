<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait DataImportConfigurationTrait
{
    /**
     * Get files keys by import type
     *
     * @param string $importTypeKey
     * @return array
     */
    public function getFileKeysByImportType(string $importTypeKey): array
    {
        $allConfigs = config('data_import');

        if (!isset($allConfigs[$importTypeKey])) {
            return [];
        }

        return array_keys($allConfigs[$importTypeKey]['files'] ?? []);
    }

    /**
     * Get required headers for a given import type and file key
     *
     * @param array $fileConfig
     * @return array
     */
    public function getRequiredHeadersForFileConfig(array $fileConfig): array
    {
        $requiredHeaders = [];
        foreach ($fileConfig['headers_to_db'] as $excelHeader => $map) {
            // Checking if the rules contain 'required'
            if (in_array('required', Arr::wrap($map['validation'] ?? []))) {
                $requiredHeaders[] = $excelHeader;
            }
        }

        return $requiredHeaders;
    }

    /**
     * Get import type configuration
     *
     * @param string $importTypeKey
     * @return array|null
     */
    public function getImportTypeConfiguration(string $importTypeKey): ?array
    {
        $allConfigs = config('data_import');
        return $allConfigs[$importTypeKey] ?? null;
    }

    /**
     * Extracting file key form input field
     *
     * @param string $inputName
     * @return string|null
     */
    public function getFileKeyFromInput(string $inputName): ?string
    {
        $parts = explode('_', $inputName);
        return end($parts) ?: null;
    }

    /**
     * Get file configuration for a given import type and file key
     *
     * @param string $importTypeKey
     * @param string $fileKey
     * @return array
     */
    public function getFileConfig(string $importTypeKey, string $fileKey): array
    {
        $importConfig = $this->getImportTypeConfiguration($importTypeKey);
        if (!$importConfig) {
            return [];
        }

        return $importConfig['files'][$fileKey] ?? [];
    }
}
