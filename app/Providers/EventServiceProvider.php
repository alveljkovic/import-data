<?php

namespace App\Providers;

use App\Events\ImportCompleted;
use App\Events\ImportFailed;
use App\Listeners\UpdateImportLogOnComplete;
use App\Listeners\UpdateImportLogOnFailure;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ImportFailed::class => [
            UpdateImportLogOnFailure::class,
        ],
        ImportCompleted::class => [
            UpdateImportLogOnComplete::class,
        ],
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
