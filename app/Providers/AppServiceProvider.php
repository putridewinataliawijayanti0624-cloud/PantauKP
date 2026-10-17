<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');
        Carbon::setLocale('id');

        $tz = env('APP_TIMEZONE', 'Asia/Jakarta');
        config(['app.timezone' => $tz]);
        date_default_timezone_set($tz);
    }
}
