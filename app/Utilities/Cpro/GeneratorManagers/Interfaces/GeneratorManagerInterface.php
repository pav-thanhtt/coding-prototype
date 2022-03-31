<?php

namespace App\Utilities\Cpro\GeneratorManagers\Interfaces;

use App\Utilities\Cpro\Definitions\TableDefinition;

interface GeneratorManagerInterface
{
    public function handle(array $tables = []);

    public function addTableDefinition(TableDefinition $tableDefinition);

    public function getTableDefinitions(): array;
}
