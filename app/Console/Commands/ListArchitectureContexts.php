<?php

namespace App\Console\Commands;

use App\Architecture\ContextMap;
use Illuminate\Console\Command;

class ListArchitectureContexts extends Command
{
    protected $signature = 'architecture:contexts {--json : Output the context map as JSON}';

    protected $description = 'List modular contexts prepared for future microservice extraction.';

    public function handle(ContextMap $contextMap): int
    {
        $contexts = $contextMap->extractionOrder();

        if ($this->option('json')) {
            $this->line(json_encode([
                'mode' => $contextMap->mode(),
                'contexts' => $contexts,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('Architecture mode: '.$contextMap->mode());

        $rows = [];

        foreach ($contexts as $key => $context) {
            $rows[] = [
                $key,
                $context['label'] ?? $key,
                $contextMap->usesRemoteService($key) ? 'remote-ready' : 'local',
                $context['service_url'] ?: '-',
                $context['queue'] ?? '-',
                $context['extraction_priority'] ?? '-',
            ];
        }

        $this->table(
            ['Context', 'Label', 'Mode', 'Service URL', 'Queue', 'Priority'],
            $rows
        );

        return self::SUCCESS;
    }
}
