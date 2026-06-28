<?php

namespace App\Services\Auth;

use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class JwtService
{
    private const ALGORITHM = 'HS256';

    public function issueAccessToken(Usuario $usuario, array $extraClaims = []): array
    {
        $now = time();
        $ttlSeconds = max(1, (int) config('jwt.ttl', 60)) * 60;
        $expiresAt = $now + $ttlSeconds;

        $payload = array_merge([
            'iss' => (string) config('jwt.issuer'),
            'aud' => (string) config('jwt.audience'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $expiresAt,
            'jti' => (string) Str::uuid(),
            'sub' => (string) $usuario->id_usuario,
            'type' => 'access',
            'correo' => $usuario->correo,
            'rol' => $usuario->rol,
            'mfa_verified' => true,
        ], $extraClaims);

        return [
            'access_token' => $this->encode($payload),
            'token_type' => 'Bearer',
            'expires_in' => $ttlSeconds,
            'expires_at' => Carbon::createFromTimestamp($expiresAt)->toIso8601String(),
        ];
    }

    public function decode(string $token, bool $checkRevoked = true): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Token JWT con formato invalido.');
        }

        [$encodedHeader, $encodedPayload, $signature] = $parts;
        $header = $this->jsonDecode($this->base64UrlDecode($encodedHeader), 'header');
        $payload = $this->jsonDecode($this->base64UrlDecode($encodedPayload), 'payload');

        if (($header['typ'] ?? null) !== 'JWT' || ($header['alg'] ?? null) !== self::ALGORITHM) {
            throw new RuntimeException('Token JWT con algoritmo invalido.');
        }

        $expectedSignature = $this->sign($encodedHeader . '.' . $encodedPayload);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new RuntimeException('Firma JWT invalida.');
        }

        $this->validateClaims($payload);

        if ($checkRevoked && $this->isRevoked($payload)) {
            throw new RuntimeException('Token JWT revocado.');
        }

        return $payload;
    }

    public function revokeToken(string $token): void
    {
        $payload = $this->decode($token, false);

        if (!$this->revocationTableExists() || empty($payload['jti'])) {
            return;
        }

        DB::table('jwt_revoked_tokens')->updateOrInsert(
            ['jti' => $payload['jti']],
            [
                'id_usuario' => isset($payload['sub']) ? (int) $payload['sub'] : null,
                'expires_at' => Carbon::createFromTimestamp((int) $payload['exp']),
                'revoked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function encode(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => self::ALGORITHM,
        ];

        $encodedHeader = $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));

        return $encodedHeader . '.' . $encodedPayload . '.' . $this->sign($encodedHeader . '.' . $encodedPayload);
    }

    private function validateClaims(array $payload): void
    {
        $now = time();
        $leeway = max(0, (int) config('jwt.leeway', 30));

        if (($payload['type'] ?? null) !== 'access') {
            throw new RuntimeException('Tipo de token JWT invalido.');
        }

        if (empty($payload['sub'])) {
            throw new RuntimeException('Token JWT sin sujeto.');
        }

        if (!empty($payload['nbf']) && ((int) $payload['nbf']) > ($now + $leeway)) {
            throw new RuntimeException('Token JWT aun no valido.');
        }

        if (empty($payload['exp']) || ((int) $payload['exp']) < ($now - $leeway)) {
            throw new RuntimeException('Token JWT expirado.');
        }

        if (($payload['iss'] ?? null) !== (string) config('jwt.issuer')) {
            throw new RuntimeException('Emisor JWT invalido.');
        }
    }

    private function isRevoked(array $payload): bool
    {
        if (!$this->revocationTableExists() || empty($payload['jti'])) {
            return false;
        }

        return DB::table('jwt_revoked_tokens')
            ->where('jti', $payload['jti'])
            ->where('expires_at', '>', now())
            ->exists();
    }

    private function revocationTableExists(): bool
    {
        return Schema::hasTable('jwt_revoked_tokens');
    }

    private function sign(string $value): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $value, $this->secret(), true));
    }

    private function secret(): string
    {
        $secret = (string) config('jwt.secret');

        if (str_starts_with($secret, 'base64:')) {
            $decoded = base64_decode(substr($secret, 7), true);

            if ($decoded !== false) {
                return $decoded;
            }
        }

        if ($secret === '') {
            throw new RuntimeException('JWT_SECRET o APP_KEY no esta configurado.');
        }

        return $secret;
    }

    private function jsonDecode(string $json, string $part): array
    {
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            throw new RuntimeException("No se pudo leer el {$part} JWT.");
        }

        return $decoded;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        if ($decoded === false) {
            throw new RuntimeException('Base64Url JWT invalido.');
        }

        return $decoded;
    }
}
