<?php

namespace App\Architecture;

use InvalidArgumentException;

class ContextMap
{
    public function mode(): string
    {
        return (string) config('microservices.mode', 'modular_monolith');
    }

    public function contexts(): array
    {
        return (array) config('microservices.contexts', []);
    }

    public function get(string $context): array
    {
        $contexts = $this->contexts();

        if (!array_key_exists($context, $contexts)) {
            throw new InvalidArgumentException("Unknown architecture context [{$context}].");
        }

        return $contexts[$context];
    }

    public function serviceUrl(string $context): ?string
    {
        $url = $this->get($context)['service_url'] ?? null;

        return is_string($url) && $url !== '' ? $url : null;
    }

    public function usesRemoteService(string $context): bool
    {
        return $this->mode() !== 'modular_monolith' && $this->serviceUrl($context) !== null;
    }

    public function extractionOrder(): array
    {
        $contexts = $this->contexts();

        uasort($contexts, function (array $left, array $right): int {
            return ($left['extraction_priority'] ?? 99) <=> ($right['extraction_priority'] ?? 99);
        });

        return $contexts;
    }
}
