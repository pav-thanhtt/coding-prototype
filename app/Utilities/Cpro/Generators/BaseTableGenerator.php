<?php

namespace App\Utilities\Cpro\Generators;

use App\Utilities\Cpro\Generators\Interfaces\TableGeneratorInterface;

use App\Utilities\Cpro\Definitions\TableDefinition;


abstract class BaseTableGenerator implements TableGeneratorInterface
{
    protected array $rows = [];

    protected TableDefinition $definition;

    public function __construct(string $tableName)
    {
        $this->definition = new TableDefinition($tableName);
    }

    public function definition(): TableDefinition
    {
        return $this->definition;
    }

    abstract public function resolveStructure();

    abstract public function parse();

    public static function init(string $tableName)
    {
        $instance = (new static($tableName));
        $instance->resolveStructure();
        $instance->parse();

        return $instance;
    }
}
