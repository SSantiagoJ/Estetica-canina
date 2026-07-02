<?php

namespace App\Providers;

use App\Contracts\Auth\TokenIssuer;
use App\Contracts\Reservations\AvailabilityProvider;
use App\Contracts\Security\SecurityAlertReporter;
use App\Services\Auth\JwtService;
use App\Services\Reservas\ReservationAvailabilityService;
use App\Services\Security\SecurityAlertService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TokenIssuer::class, JwtService::class);
        $this->app->bind(AvailabilityProvider::class, ReservationAvailabilityService::class);
        $this->app->bind(SecurityAlertReporter::class, SecurityAlertService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
