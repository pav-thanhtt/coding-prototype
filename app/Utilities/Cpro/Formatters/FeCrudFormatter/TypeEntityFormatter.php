<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;

class TypeEntityFormatter extends BaseFeFormatter
{

    private const STUB_FILE_NAME = 'type_entity';
    private const EXPORT_FILE_NAME_SUFFIX = '.ts';

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

    public function renderTypeEntity(int $indentTab, $file): string
    {
        $lines = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$lines) {
                if (!$this->isHidden($column) && !$this->isSoftDeletes($column)) {
                    $lines[$column->getColumnName()] = $this->typeValue($column);
                }
            }
        );

        return $this->objectRender($lines, $indentTab);
    }
}
