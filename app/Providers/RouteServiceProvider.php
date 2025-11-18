<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Models\DataImportAudit;
use Illuminate\Support\Facades\DB;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        parent::boot();

        /**
         * Bind {importType} — vrednost iz config/imports.php
         */
        Route::bind('importType', function ($value) {
            $config = config("data_import.{$value}");
            if (!$config) {
                abort(404, "Unknown import type: $value");
            }

            return array_merge($config, ['config_key' => $value]);
        });

        /**
         * Bind {fileKey} — zavisi od izabranog importType
         */
        Route::bind('fileKey', function ($value, $route) {
            // Prethodni binding već vraća celu sekciju iz configa
            $importType = $route->parameter('importType');

            // $importType je sada array iz configa
            $files = $importType['files'] ?? [];

            if (!array_key_exists($value, $files)) {
                abort(404, "Unknown file key: $value");
            }

            return array_merge($files[$value], ['config_key' => $value]);
        });

        /**
         * Bind {rowId} — DataImportAudit model po koloni row_id
         */
        Route::bind('rowId', function ($value, $route) {
            $table = $route->parameter('fileKey')['table_name'];
            return DB::table($table)->where('id', $value)->firstOrFail();
        });
    }

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
}
