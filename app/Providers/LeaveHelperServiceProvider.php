<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\LeaveHelper;

class LeaveHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('leave-helper', function ($app) {
            return new LeaveHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}