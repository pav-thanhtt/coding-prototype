<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Str;

class RepositoryFormatter extends BaseFormatter
{
    private const STUB_FILE_NAME = 'repository';
    private const EXPORT_FILE_NAME_SUFFIX = 'Repository.php';

    protected array $searchFields;

    public function __construct(TableDefinition $tableDefinition)
    {
        $searchFields = array_map(function ($column) {
            if ($this->isSearchField($column)) {
                return $column->getColumnName();
            }
        }, $tableDefinition->getColumns());

        $this->searchFields = $this->cleanArray($searchFields);
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

    public function renderSearchFields(int $indentTab, $file): string
    {
        return $this->arrayRender($this->searchFields, $indentTab);
    }

    private function isSearchField(ColumnDefinition $column): bool
    {
        if ($this->isTextType($column) && !$this->isHidden($column)) {
            return true;
        }
        return false;
    }
}
