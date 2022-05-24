<?php

namespace App\Utilities\Cpro\Definitions;

use Illuminate\Support\Arr;

class TableDefinition
{
    protected string $tableName;

    /** @var array<ColumnDefinition> */
    protected array $columnDefinitions = [];

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param ColumnDefinition $definition
     * @return TableDefinition
     */
    public function addColumnDefinition(ColumnDefinition $definition): TableDefinition
    {
        $this->columnDefinitions[] = $definition;

        return $this;
    }

    /**
     * @return ColumnDefinition[]
     */
    public function getColumns(): array
    {
        return $this->columnDefinitions;
    }

    /**
     * @param string $columnName
     * @return ColumnDefinition|null
     */
    public function getColumnByName(string $columnName): ColumnDefinition|null
    {
        $column = Arr::where($this->columnDefinitions, function ($columnDefinition) use ($columnName) {
            return $columnDefinition->getColumnName() === $columnName;
        });

        if (empty($column)) {
            return null;
        }

        return reset($column);
    }
}
