<?php

namespace App\Utilities\Cpro\GeneratorManagers;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\GeneratorManagers\Interfaces\GeneratorManagerInterface;
use Illuminate\Console\Command;

abstract class BaseGeneratorManager implements GeneratorManagerInterface
{
    protected Command $command;

    protected array $tableDefinitions = [];

    abstract protected function init(array $tables);

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

    public function handle(array $tables = [])
    {
        $this->init($tables);
    }
}
