<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InstalarBaseDatosPetGrooming extends Command
{
    protected $signature = 'petgrooming:instalar-db
        {--fresh : Borra todas las tablas antes de instalar la base}
        {--yes : Confirma automaticamente la instalacion}';

    protected $description = 'Instala la base de datos base de Pet Grooming desde database/sql y ejecuta migraciones pendientes.';

    public function handle(): int
    {
        $driver = DB::connection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->error('Esta instalacion integrada usa un dump MySQL/MariaDB. Configura DB_CONNECTION=mysql o mariadb.');

            return self::FAILURE;
        }

        $sqlPath = database_path('sql/spa_mascotas_base.sql');

        if (! file_exists($sqlPath)) {
            $this->error("No se encontro el archivo base: {$sqlPath}");

            return self::FAILURE;
        }

        $tables = $this->currentTables();

        if ($tables !== [] && ! $this->option('fresh')) {
            $this->warn('La base de datos ya tiene tablas.');
            $this->line('Usa php artisan petgrooming:instalar-db --fresh para reinstalarla desde cero.');

            return self::FAILURE;
        }

        if ($tables !== [] && $this->option('fresh')) {
            if (! $this->option('yes') && ! $this->confirm('Esto borrara todas las tablas de la base actual. Deseas continuar?', false)) {
                $this->info('Instalacion cancelada.');

                return self::FAILURE;
            }

            $this->dropAllTables();
        }

        $this->info('Cargando base integrada desde database/sql/spa_mascotas_base.sql...');

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($this->splitSqlStatements(file_get_contents($sqlPath)) as $statement) {
                DB::unprepared($statement);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $exception) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('No se pudo cargar el SQL integrado.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Aplicando migraciones nuevas del proyecto...');
        $this->call('migrate', ['--force' => true]);

        $this->info('Base de datos lista. Puedes iniciar Laravel con php artisan serve.');

        return self::SUCCESS;
    }

    private function currentTables(): array
    {
        $rows = DB::select('SHOW TABLES');

        return array_values(array_map(static function (object $row): string {
            return (string) array_values((array) $row)[0];
        }, $rows));
    }

    private function dropAllTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->currentTables() as $table) {
            DB::statement('DROP TABLE IF EXISTS `'.str_replace('`', '``', $table).'`');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Divide el dump sin cortar textos entre comillas.
     *
     * @return array<int, string>
     */
    private function splitSqlStatements(string $sql): array
    {
        $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql) ?? $sql;
        $sql = $this->removeDumpComments($sql);

        $statements = [];
        $statement = '';
        $quote = null;
        $escaped = false;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $statement .= $char;

            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === "'" || $char === '"') {
                $quote = $char;
                continue;
            }

            if ($char === ';') {
                $cleanStatement = trim($statement);

                if ($cleanStatement !== '') {
                    $statements[] = $cleanStatement;
                }

                $statement = '';
            }
        }

        $lastStatement = trim($statement);

        if ($lastStatement !== '') {
            $statements[] = $lastStatement;
        }

        return $statements;
    }

    private function removeDumpComments(string $sql): string
    {
        $lines = preg_split('/\R/', $sql) ?: [];
        $cleanLines = [];

        foreach ($lines as $line) {
            $trimmed = ltrim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }

            $cleanLines[] = $line;
        }

        return implode(PHP_EOL, $cleanLines);
    }
}
