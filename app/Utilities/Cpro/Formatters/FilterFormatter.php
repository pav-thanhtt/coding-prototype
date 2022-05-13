<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Str;

class FilterFormatter extends BaseFormatter
{
    private const STUB_FILE_NAME = 'filter';
    private const EXPORT_FILE_NAME_SUFFIX = 'Filter.php';

    protected array $searchFields;
    protected array $methodFilters;

    public function __construct(TableDefinition $tableDefinition)
    {

        $searchFields = [];
        $methodFilters = [];
        $columns = $tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$searchFields, &$methodFilters) {
            $columnName = $column->getColumnName();
                if ($this->isSearchField($column)) {
                    $searchFields[] = $columnName;
                }

                if ($this->isMethodFilter($column)) {
                    if (Str::contains($columnName, '_at')) {
                        $methodFilters[] = $this->parseMethod($columnName, 'from');
                        $methodFilters[] = $this->parseMethod($columnName, 'to');
                    } else {
                        $methodFilters[] = $this->parseMethod($columnName);
                    }
                }
            });

        $this->searchFields = $searchFields;
        $this->methodFilters = $methodFilters;
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

    public function renderMethodFilters(int $indentTab, $file): string
    {
        return $this->methodRender($this->methodFilters, $indentTab);
    }

    private function isSearchField(ColumnDefinition $column): bool
    {
        return $this->isTextType($column) && !$this->isHidden($column);
    }

    private function parseMethod(string $columnName, $support = ''): array
    {
        $methodName = $columnName;
        $content = 'return $this->where(\'' . $columnName . '\', $value);';

        if (!empty($support)) {
            $methodName = "{$methodName}_{$support}";
            if ('from' === $support) {
                $content = 'return $this->whereDate(\'' . $columnName . '\', \'>=\', $value);';
            } else {
                $content = 'return $this->whereDate(\'' . $columnName . '\', \'<=\', $value);';
            }
        } elseif (preg_match('/_id$/', $methodName)) {
            $methodName = preg_replace('/_id$/', '', $methodName);
        }
        $methodName = Str::camel($methodName);

        return [
            'hasComment' => false,
            'scope' => 'public',
            'methodName' => $methodName,
            'params' => ['$value'],
            'returnType' => 0,
            'content' => $content,
        ];
    }
}
