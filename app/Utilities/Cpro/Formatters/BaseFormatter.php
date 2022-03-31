<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseFormatter
{
    private const INDENT_SPACE_FORMAT_DEFAULT = 4;

    protected string $stubFileName;

    protected array $fileName;

    protected TableDefinition $tableDefinition;

    abstract protected function getStubFileName();

    abstract public function getExportFileName(?string $options = '');

    public function __construct(TableDefinition $tableDefinition)
    {
        $this->tableDefinition = $tableDefinition;
    }

    public function render(?string $file = ''): string
    {
        $stub = $this->getStubFile($this->getStubFileName());
        return $this->replace($stub, $file);
    }

    protected function replace($stub, $file): string
    {
        return preg_replace_callback('/{{(.+?)}}/', function ($matches) use ($file) {
            $matches = $matches[1];
            if (preg_match('/\((\d*|\w*)\)/', trim($matches), $matchParam)) {
                if (!empty($param = trim($matchParam[0], '() '))) {
                    return $this->{trim(Str::replace($matchParam[0], '', $matches))}($param, $file);
                }
            }
            return $this->{trim($matches)}($file);
        }, $stub);
    }

    /**
     * @param string $resourceType
     * @return false|string
     */
    public function getStubFile(string $resourceType): bool|string
    {
        if ($overridden = $this->getStubPath($resourceType)) {
            return File::get($overridden);
        }
        return false;
    }

    /**
     * @param string $resourceType
     * @return false|string
     */
    private function getStubPath(string $resourceType): bool|string
    {
        if (File::exists($overridden = resource_path(config('cpro-resource-generator.stub_path') . $resourceType . '.stub'))) {
            return $overridden;
        }
        return false;
    }


    /**
     * @param array $array
     * @param int $indentTab
     * @param bool $useKey
     * @return string
     */
    public function arrayRender(array $array = [], int $indentTab = 0, bool $useKey = false): string
    {
        $resultString = '';

        foreach ($array as $key => $value) {
            if ($useKey) {
                $resultString .= $this->indentSpace($indentTab) . $this->singleQuotes($key) . ' => ' . $this->renderArrayValue($value, $indentTab) . ",\n";
            } else {
                $resultString .= $this->indentSpace($indentTab) . $this->renderArrayValue($value, $indentTab) . ",\n";
            }
        }

        return rtrim($resultString, "\n");
    }


    protected function isFillable(ColumnDefinition $column): bool
    {
        $dataType = $column->getColumnDataType();
        $colName = $column->getColumnName();

        if ($colName === 'id' || Str::contains($colName, 'token') || $this->isCurrent($column)) {
            return false;
        }
        if (
            (($dataType === 'timestamp' || $dataType === 'datetime') && preg_match('/_at$/', $colName))
        ) {
            return false;
        }
        return true;
    }

    protected function isHidden(ColumnDefinition $column): bool
    {
        return Str::contains($column->getColumnName(), ['password', 'token']);
    }

    protected function isTextType(ColumnDefinition $column): bool
    {
        return Str::contains($column->getColumnDataType(), ['char', 'text', 'enum']);
    }

    protected function isNumberType(ColumnDefinition $column): bool
    {
        return Str::contains($column->getColumnDataType(), ['int', 'decimal', 'float', 'double']);
    }

    protected function isDateOrTimeType(ColumnDefinition $column): bool
    {
        return Str::contains($column->getColumnDataType(), ['time', 'date', 'year']);
    }

    protected function isCurrent(ColumnDefinition $column): bool
    {
        return Str::contains($column->getColumnDataType(), ['datetime', 'timestamp']) && $column->getDefaultValue() === 'CURRENT_TIMESTAMP';
    }

    protected function tableName($type, $file = ''): string
    {
        $tableName = $this->tableDefinition->getTableName();
        switch ($type) {
            case 'ClassNamePlural':
                $tableName = Str::ucfirst(Str::camel($tableName));;
                break;
            case 'BindingName':
                $tableName = Str::singular($tableName);
                break;
            case 'ParamNamePlural':
                $tableName = Str::camel($tableName);
                break;
            case 'PropertyName':
                $tableName = Str::camel(Str::singular($tableName));
                break;
            default:
                $suffix = match ($file) {
                    'search_request' => 'Search',
                    'store_request' => 'Store',
                    'update_request' => 'Update',
                    default => ''
                };
                $tableName = Str::ucfirst(Str::camel(Str::singular($tableName))) . $suffix;
        }
        return $tableName;
    }

    private function singleQuotes($str): string
    {
        return "'$str'";
    }

    private function indentSpace($indentTab): string
    {
        return Str::padLeft('', $indentTab * self::INDENT_SPACE_FORMAT_DEFAULT, ' ');
    }

    protected function cleanArray($array, $isSortKey = true): array
    {
        $arrClean = array_filter($array, fn($item) => !is_null($item));
        if (!$isSortKey) {
            return $arrClean;
        }
        return array_values($arrClean);
    }

    private function renderArrayValue($value, $indentTab = 0): float|int|string
    {
        if (is_array($value)) {
            return "[\n{$this->arrayRender($value, $indentTab + 1)}\n{$this->indentSpace($indentTab)}]";
        }
        if (is_numeric($value)) {
            return $value;
        }

        if (preg_match('/^_@/', $value, $matchPrefix)) {
            return Str::replace($matchPrefix[0], '', $value);
        }
        return $this->singleQuotes($value);
    }
}
