<?php

namespace App\Validations;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exceptions\InvalidFileHeaderException;
use App\Validations\ImportedDataValidator;

class FileHeaderValidator
{
    /**
     * Validate that Excel headers match config keys
     */
    public static function validate(string $filePath, array $config): void
    {
        $headers = Excel::toArray((new HeadingRowImport()), $filePath)[0][0] ?? [];

        if (!$headers) {
            throw new \Exception("Excel fajl nema header red.");
        }

        $normalizedHeaders = self::formatHeaders($headers);
        $expectedHeaders = self::formatHeaders(array_keys($config['headers_to_db']));

        $missingHeaders = [];
        foreach ($expectedHeaders as $expected) {
            if (!in_array($expected, $normalizedHeaders)) {
                $missingHeaders[] = $expected;
            }
        }

        if (!empty($missingHeaders)) {
            throw new InvalidFileHeaderException("Excel header columns are missing: " . implode(', ', $missingHeaders));
        }
    }

    /**
     * Format header columns for validations
     *
     * @param array $headers
     * @return array
     */
    public static function formatHeaders(array $headers): array
    {
        return collect($headers)->map(function ($header) {
            return strtolower(str_replace([' ', '#'], ['_', ''], trim($header)));
        })->toArray();
    }
}
