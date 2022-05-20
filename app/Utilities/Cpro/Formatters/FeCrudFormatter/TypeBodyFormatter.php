<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;

class TypeBodyFormatter extends BaseFeFormatter
{
    private const STUB_FILE_NAME = 'type_body';
    private const EXPORT_FILE_NAME_SUFFIX = 'Body.ts';

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

    public function renderTypeBody(int $indentTab, $file): string
    {
        $lines = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$lines) {
                if ($this->isFillable($column)) {
                    $lines[$this->getKey($column)] = $this->typeValue($column);
                }
            }
        );

        return $this->objectRender($lines, $indentTab);
    }
}
