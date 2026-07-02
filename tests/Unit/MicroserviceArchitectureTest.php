<?php

namespace Tests\Unit;

use App\Architecture\ContextMap;
use App\Contracts\Auth\TokenIssuer;
use App\Contracts\Reservations\AvailabilityProvider;
use App\Contracts\Security\SecurityAlertReporter;
use App\Services\Auth\JwtService;
use App\Services\Reservas\ReservationAvailabilityService;
use App\Services\Security\SecurityAlertService;
use Tests\TestCase;

class MicroserviceArchitectureTest extends TestCase
{
    public function test_context_map_exposes_main_business_contexts(): void
    {
        $contextMap = app(ContextMap::class);
        $contexts = $contextMap->contexts();

        $this->assertSame('modular_monolith', $contextMap->mode());
        $this->assertArrayHasKey('auth', $contexts);
        $this->assertArrayHasKey('reservas', $contexts);
        $this->assertArrayHasKey('pagos', $contexts);
        $this->assertArrayHasKey('notificaciones', $contexts);
        $this->assertArrayHasKey('catalogo', $contexts);
        $this->assertFalse($contextMap->usesRemoteService('reservas'));
    }

    public function test_contracts_resolve_to_current_local_implementations(): void
    {
        $this->assertInstanceOf(JwtService::class, app(TokenIssuer::class));
        $this->assertInstanceOf(ReservationAvailabilityService::class, app(AvailabilityProvider::class));
        $this->assertInstanceOf(SecurityAlertService::class, app(SecurityAlertReporter::class));
    }
}
