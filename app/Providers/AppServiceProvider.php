<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AttendanceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AttendanceService::class, function ($app) {
            return new AttendanceService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
