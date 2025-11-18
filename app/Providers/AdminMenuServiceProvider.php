<?php

namespace App\Providers;

use App\Helpers\AdminMenuHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AdminMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $dynamicMenu = AdminMenuHelper::getImportedDataMenu();
        if (empty($dynamicMenu)) {
            return;
        }

        $currentMenu = config('adminlte.menu', []);

        $newMenu = Arr::collapse([$currentMenu, [$dynamicMenu]]);
        config()->set('adminlte.menu', $newMenu);
    }
}
