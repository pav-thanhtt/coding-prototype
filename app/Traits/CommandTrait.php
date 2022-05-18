<?php

namespace App\Traits;

use App\Utilities\Cpro\GeneratorManagers\Interfaces\GeneratorManagerInterface;
use App\Utilities\Cpro\GeneratorManagers\MySQLGeneratorManager;

trait CommandTrait
{
    protected function getConnection(): string
    {
        $connection = $this->argument('con');

        if (!\Config::has('database.connections.' . $connection)) {
            throw new \Exception('Could not find connection `' . $connection . '` in your config.');
        }

        return $connection;
    }

    protected function getTables(): array
    {
        $tables = $this->option('table');

        return isset($tables) ? explode(',', $tables) : [];
    }

    protected function resolveGeneratorManager(string $driver, $tables): GeneratorManagerInterface|bool
    {
        $supported = [
            'mysql' => MySQLGeneratorManager::class
        ];

        if (!isset($supported[$driver])) {
            return false;
        }

        return $supported[$driver]::instance($this, $tables);
    }
}
