<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\TableDefinition;

class ResourceFormatter extends BaseFormatter
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
                if (!$this->isHidden($column)) {
                    $resources[$column->getColumnName()] = '_@$this->' . $column->getColumnName();
                }
            },
            $resources);
        $resources = $this->cleanArray($resources, false);

        return $this->arrayRender($resources, $indentTab, true);
    }
}
