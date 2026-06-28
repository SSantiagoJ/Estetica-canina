<?php

namespace Tests\Unit;

use App\Models\Usuario;
use App\Services\Auth\JwtService;
use Tests\TestCase;

class JwtServiceTest extends TestCase
{
    public function test_it_issues_and_decodes_access_tokens(): void
    {
        config([
            'jwt.secret' => 'unit-test-secret',
            'jwt.ttl' => 60,
            'jwt.issuer' => 'http://127.0.0.1:8000',
            'jwt.audience' => 'Pet Grooming',
        ]);

        $usuario = new Usuario([
            'correo' => 'cliente@spa.com',
            'rol' => 'Cliente',
        ]);
        $usuario->id_usuario = 15;

        $jwt = app(JwtService::class);
        $issued = $jwt->issueAccessToken($usuario);
        $payload = $jwt->decode($issued['access_token'], false);

        $this->assertSame('Bearer', $issued['token_type']);
        $this->assertSame('15', $payload['sub']);
        $this->assertSame('Cliente', $payload['rol']);
        $this->assertTrue($payload['mfa_verified']);
        $this->assertSame('access', $payload['type']);
    }
}
