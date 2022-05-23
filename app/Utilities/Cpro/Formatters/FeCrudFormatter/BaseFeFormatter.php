<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\BaseFormatter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseFeFormatter extends BaseFormatter
{
    protected $indentSpaceDefault = 2;

    private $tsType = [
        'varchar' => 'string',
        'char' => 'string',
        'text' => 'string',
        'longtext' => 'string',
        'mediumtext' => 'string',
        'enum' => 'string',
        'timestamp' => 'Date',
        'datetime' => 'Date',
        'tinyint' => 'boolean',
        'int' => 'number',
        'bigint' => 'number',
        'mediumint' => 'number',
        'smallint' => 'number',
        'float' => 'number',
        'double' => 'number'
    ];

    public function __construct(TableDefinition $tableDefinition)
    {
        parent::__construct($tableDefinition);
    }
    /**
     * @param string $resourceType
     * @return false|string
     */

    public function getStubPath(string $resourceType): bool|string
    {
        if (File::exists($overridden = resource_path(config('cpro-resource-generator.fe_stub_path') . $resourceType . '.stub'))) {
            return $overridden;
        }
        return false;
    }

    protected function typeValue(ColumnDefinition $column, $type = false)
    {
        if ($type) {
            $tsType = $this->tsType[$column->getColumnDataType()] ?? 'any';
            return sprintf('%s%s', '_@', $tsType === 'Date' ? 'Date | Moment' : $tsType );
        }

        return sprintf('%s%s', '_@', $this->tsType[$column->getColumnDataType()] ?? 'any');
    }

    protected function getKey(ColumnDefinition $column)
    {
        return $column->isNullable() ? "{$column->getColumnName()}?" : $column->getColumnName();
    }

    protected function objectRender(array $object = [], float $indentTab = 0): string
    {
        $resultString = '';

        foreach ($object as $key => $value) {
            $resultString .= $this->indentSpace($indentTab) . "{$key}: " . $this->renderArrayValue($value, $indentTab) . ",\n";
        }

        return rtrim($resultString, ",\n");
    }

    protected function initValue(ColumnDefinition $columnDefinition)
    {
        if ($this->isTextType($columnDefinition)) {
            return '';
        }

        return '_@undefined';
    }

    protected function renderHtml(int $indentTab, array $groups, string $twoLine = "\n")
    {
        return implode("$twoLine\n", array_map(function($group) use($indentTab) {
            $lines = explode("\n", $group);
            return implode("\n", array_map(function($line) use($indentTab) {
                return sprintf('%s%s', $this->indentSpace($indentTab), $line);
            }, $lines));
        }, $groups));
    }

    protected function getColumnLabel(string $columnName) {
        return Str::replace('_', ' ', Str::ucfirst(Str::snake($columnName)));
    }

    protected function replaceVariable($stub) {
        return preg_replace_callback('/{!(.+?)!}/', function ($matches) {
            return $this->{trim($matches[1])};
        }, $stub);
    }

    protected function isFilterDatetimeField(ColumnDefinition $column): bool
    {
        $columnName = $column->getColumnName();
        $dataType = $column->getColumnDataType();

        return $this->isMethodFilter($column) &&
            Str::contains($columnName, '_at') &&
            ($dataType === 'timestamp' || $dataType === 'datetime');
    }

    protected function idType($file)
    {
      $idColumn = $this->tableDefinition->getColumnByName('id');
      if (is_null($idColumn)) {
        return 'any';
      }

      if($idColumn->isAutoIncrementing() || Str::contains($idColumn->getColumnDataType(), 'int')) {
        return 'number';
      }

      if(Str::contains($idColumn->getColumnDataType(), 'char')) {
        return 'string';
      }
      return 'any';
    }

    protected function hasSorter() {
        $columns = $this->tableDefinition->getColumns();

        foreach($columns as $column) {
            if($this->isSortField($column)) {
                return true;
            }
        }
        return false;
    }
}
