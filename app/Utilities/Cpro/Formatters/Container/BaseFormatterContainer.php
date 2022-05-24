<?php

namespace App\Utilities\Cpro\Formatters\Container;

use App\Utilities\Cpro\Definitions\TableDefinition;

abstract class BaseFormatterContainer
{
    public string $tableName;

    abstract public static function init(TableDefinition $tableDefinition): static;
}
