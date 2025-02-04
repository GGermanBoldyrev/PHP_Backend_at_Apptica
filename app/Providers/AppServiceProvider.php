<?php

namespace App\Providers;

use App\Interfaces\TopPositionsInterface;
use App\Services\AppticaTopPositionsService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TopPositionsInterface::class, function ($app) {
            return new AppticaTopPositionsService(
                $app->make(Client::class),
                $app->make(LoggerInterface::class),
                config('services.apptica.apptica_api_key')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
