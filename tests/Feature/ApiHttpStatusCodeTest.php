<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Services\Auth\JwtService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiHttpStatusCodeTest extends TestCase
{
    public function test_health_endpoint_returns_200_ok(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status_code' => 200,
            ]);
    }

    public function test_validation_errors_return_400_bad_request(): void
    {
        $this->postJson('/api/v1/reservas/horarios-disponibles', [])
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'status_code' => 400,
            ])
            ->assertJsonStructure(['errors']);
    }

    public function test_protected_api_route_without_session_returns_401_unauthorized(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status_code' => 401,
            ]);
    }

    public function test_protected_api_route_with_invalid_bearer_token_returns_401_unauthorized(): void
    {
        $this->withHeader('Authorization', 'Bearer token-invalido')
            ->getJson('/api/v1/auth/me')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status_code' => 401,
            ]);
    }

    public function test_cliente_endpoint_without_token_returns_401_unauthorized(): void
    {
        $this->getJson('/api/v1/clientes/perfil')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status_code' => 401,
            ]);
    }

    public function test_cliente_endpoint_with_invalid_token_returns_401_unauthorized(): void
    {
        $this->withHeader('Authorization', 'Bearer token-invalido')
            ->getJson('/api/v1/clientes/perfil')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'status_code' => 401,
            ]);
    }

    public function test_valid_token_without_required_role_returns_403_and_notifies_admin(): void
    {
        $this->createSecurityTestTables();

        config([
            'jwt.secret' => 'feature-test-secret',
            'jwt.ttl' => 60,
            'jwt.issuer' => 'http://127.0.0.1:8000',
            'jwt.audience' => 'Pet Grooming',
        ]);

        $employee = Usuario::findOrFail(2);
        $token = app(JwtService::class)->issueAccessToken($employee)['access_token'];

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/clientes/perfil')
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'status_code' => 403,
            ]);

        $this->assertDatabaseHas('notificaciones', [
            'id_usuario' => 1,
            'tipo' => 'Seguridad',
            'estado' => 'A',
        ]);

        $this->assertTrue(
            DB::table('notificaciones')
                ->where('id_usuario', 1)
                ->where('tipo', 'Seguridad')
                ->where('mensaje', 'like', '%sin permisos%')
                ->exists()
        );
    }

    public function test_missing_api_route_returns_404_not_found(): void
    {
        $this->getJson('/api/v1/ruta-inexistente')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'status_code' => 404,
            ]);
    }

    private function createSecurityTestTables(): void
    {
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('empleados');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('personas');

        Schema::create('personas', function (Blueprint $table): void {
            $table->increments('id_persona');
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
        });

        Schema::create('usuarios', function (Blueprint $table): void {
            $table->increments('id_usuario');
            $table->unsignedInteger('id_persona')->nullable();
            $table->string('correo');
            $table->string('contrasena')->nullable();
            $table->string('rol');
            $table->char('estado', 1)->default('A');
        });

        Schema::create('empleados', function (Blueprint $table): void {
            $table->increments('id_empleado');
            $table->unsignedInteger('id_persona');
        });

        Schema::create('notificaciones', function (Blueprint $table): void {
            $table->increments('id_notificacion');
            $table->unsignedInteger('id_usuario');
            $table->string('tipo', 50);
            $table->string('mensaje', 500);
            $table->dateTime('fecha_envio');
            $table->char('estado', 1);
            $table->string('usuario_creacion', 50);
            $table->dateTime('fecha_creacion');
            $table->string('usuario_actualizacion', 50);
            $table->dateTime('fecha_actualizacion');
        });

        DB::table('personas')->insert([
            ['id_persona' => 1, 'nombres' => 'Admin', 'apellidos' => 'Sistema'],
            ['id_persona' => 2, 'nombres' => 'Pedro', 'apellidos' => 'Empleado'],
        ]);

        DB::table('usuarios')->insert([
            [
                'id_usuario' => 1,
                'id_persona' => 1,
                'correo' => 'admin@spa.com',
                'contrasena' => 'hash',
                'rol' => 'Admin',
                'estado' => 'A',
            ],
            [
                'id_usuario' => 2,
                'id_persona' => 2,
                'correo' => 'empleado@spa.com',
                'contrasena' => 'hash',
                'rol' => 'Empleado',
                'estado' => 'A',
            ],
        ]);

        DB::table('empleados')->insert([
            'id_empleado' => 1,
            'id_persona' => 2,
        ]);
    }
}
