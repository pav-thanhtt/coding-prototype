<?php

namespace App\Utilities\Cpro\Formatters\BeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;

class ResourceFormatter extends BaseBeFormatter
{
    private const STUB_FILE_NAME = 'resource';
    private const EXPORT_FILE_NAME_SUFFIX = 'Resource.php';

    public function __construct(TableDefinition $tableDefinition)
    {
        parent::__construct($tableDefinition);
        $this->fileName[self::STUB_FILE_NAME] = $this->tableName('ClassNameSingular') . self::EXPORT_FILE_NAME_SUFFIX;
    }

    protected function getStubFileName(): string
    {
        return self::STUB_FILE_NAME;
    }


    public function getExportFileName(?string $options = ''): string
    {
        return $this->fileName[self::STUB_FILE_NAME];
    }

    public function renderResources(int $indentTab, $file): string
    {
        $resources = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$resources) {
                if (!$this->isHidden($column) && !$this->isSoftDeletes($column)) {
                    $resources[$column->getColumnName()] = '_@$this->' . $column->getColumnName();
                }
            });

        return $this->arrayRender($resources, $indentTab, true);
    }

    private function isSoftDeletes(ColumnDefinition $column): bool
    {
        return (
            $column->getColumnName() === 'deleted_at' &&
            $column->isNullable() &&
            ($column->getColumnDataType() === 'timestamp' || $column->getColumnDataType() === 'datetime')
        );
    }
}
