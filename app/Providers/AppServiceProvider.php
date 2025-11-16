<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Uncomment to see what database select queries are being
        // called. Useful for cache checks and query counts.
        //        DB::listen(function($query) {
        //            if (Str::contains($query->sql, 'update')) {
        //                Log::debug(
        //                    'select statement',
        //                    context: [ 'sql' => $query->sql, 'bindings' => $query->bindings ],
        //                );
        //            }
        //        });
    }
}
