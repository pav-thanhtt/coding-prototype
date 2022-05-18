<?php

namespace App\Utilities\Cpro\Formatters\RmsfFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;

class RepositoryFormatter extends BaseRmsfFormatter
{
    private const STUB_FILE_NAME = 'repository';
    private const EXPORT_FILE_NAME_SUFFIX = 'Repository.php';

    protected array $sortFields;

    public function __construct(TableDefinition $tableDefinition)
    {
        $sortFields = array_map(function ($column) {
            if ($this->isSortField($column)) {
                return $column->getColumnName();
            }
        }, $tableDefinition->getColumns());

        $this->sortFields = $this->cleanArray($sortFields);
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

    public function renderSortFields(int $indentTab, $file): string
    {
        return $this->arrayRender($this->sortFields, $indentTab);
    }

    private function isSortField(ColumnDefinition $column): bool
    {
        return !$this->isHidden($column) && 'deleted_at' !== $column->getColumnName();
    }
}
