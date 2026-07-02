<?php

namespace App\Contracts\Auth;

use App\Models\Usuario;

interface TokenIssuer
{
    public function issueAccessToken(Usuario $usuario, array $extraClaims = []): array;

    public function decode(string $token, bool $checkRevoked = true): array;

    public function revokeToken(string $token): void;
}
