<?php

namespace App\Interfaces;

use App\Http\Requests\DataImportRequest;

interface DataImportInterface
{
    /**
     * Process the data import request
     *
     * @param DataImportRequest $request
     * @return array
     */
    public function process(DataImportRequest $request): array;
}
