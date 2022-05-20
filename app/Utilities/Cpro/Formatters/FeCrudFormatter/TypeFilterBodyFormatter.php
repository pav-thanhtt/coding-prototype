<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Str;

class TypeFilterBodyFormatter extends BaseFeFormatter
{

    private const STUB_FILE_NAME = 'type_filter_body';
    private const EXPORT_FILE_NAME_SUFFIX = 'FilterBody.ts';

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

    public function renderTypeFilterBody(int $indentTab, $file): string
    {
        $lines = [
            'per_page?' => '_@number',
            'page?' => '_@number',
            'keyword?' => '_@string',
            'include?' => '_@Include',
            'sort?' => '_@string',
            'sort_direction?' => '_@SortDirection',
        ];
        $columns = $this->tableDefinition->getColumns();

        array_walk($columns,
            function ($column) use (&$lines) {
                if ($this->isMethodFilter($column)) {
                    $columnName = $column->getColumnName();
                    if (Str::contains($columnName, '_at')) {
                        $lines["{$columnName}_from"] = '_@string';
                        $lines["{$columnName}_to"] = '_@string';
                    } else {
                        $lines[$columnName] = $this->typeValue($column);
                    }
                }
            }
        );

        return $this->objectRender($lines, $indentTab);
    }

    protected function getKey(ColumnDefinition $column)
    {
        return $column->isNullable() ? "{$column->getColumnName()}?" : $column->getColumnName();
    }
}
