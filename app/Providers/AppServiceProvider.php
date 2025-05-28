<?php

namespace App\Providers;

use App\Team\Interfaces\TeamSelectorInterface;
use App\Team\Strategy\StandardSelectorStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TeamSelectorInterface::class,
            StandardSelectorStrategy::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
