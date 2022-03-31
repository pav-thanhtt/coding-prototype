<?php

namespace App\Utilities\Cpro\GeneratorManagers;

use App\Console\Commands\GenerateApiResourcesCommand;
use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\Formatter;
use App\Utilities\Cpro\GeneratorManagers\Interfaces\GeneratorManagerInterface;

abstract class BaseGeneratorManager implements GeneratorManagerInterface
{
    protected GenerateApiResourcesCommand $command;

    protected array $tableDefinitions = [];

    protected array $formatters = [];

    abstract public function init(array $tables);

    /**
     * @return array<TableDefinition>
     */
    public function getTableDefinitions(): array
    {
        return $this->tableDefinitions;
    }

    public function addTableDefinition(TableDefinition $tableDefinition): BaseGeneratorManager
    {
        $this->tableDefinitions[] = $tableDefinition;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormatters(): array
    {
        return $this->formatters;
    }

    public function addFormatters(Formatter $formatters): BaseGeneratorManager
    {
        $this->formatters[] = $formatters;

        return $this;
    }

    public function handle(array $tables = [])
    {
        $this->init($tables);
    }
}
