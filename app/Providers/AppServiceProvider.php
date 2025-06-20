<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        // Disable foreign key constraints during migrations
        // Schema::disableForeignKeyConstraints();
        
        // // Re-enable them after migrations
        // $this->app->booted(function () {
        //     Schema::enableForeignKeyConstraints();
        // });
    }
}
