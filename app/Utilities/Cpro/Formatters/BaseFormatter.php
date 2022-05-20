<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseFormatter
{
    protected string $stubFileName;

    protected array $fileName;

    protected TableDefinition $tableDefinition;

    abstract protected function getStubFileName();

    abstract public function getExportFileName(?string $options = '');

    abstract public function getStubPath(string $resourceType): bool|string;

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
        return preg_replace_callback('/{{{(.+?)}}}/', function ($matches) use ($file) {
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

    /**
     * @param array $methods
     * @param int $indentTab
     * @return string
     */
    public function methodRender(array $methods = [], int $indentTab = 0): string
    {
        $resultString = "\n";

        foreach ($methods as $method) {
            $params = implode(', ', $method['params']);
            $return = '';
            if (isset($method['hasComment']) && $method['hasComment']) {
                $resultString .= $this->parseComment($method, $indentTab);
                $return = $method['returnType'] !== 0 ? ": {$method['returnType']}" : ": mixed";
            }
            $resultString .= "{$this->indentSpace($indentTab)}{$method['scope']} function {$method['methodName']}({$params}){$return}\n";
            $resultString .= "{$this->indentSpace($indentTab)}{\n";
            $resultString .= "{$this->indentSpace($indentTab + 1)}{$method['content']}\n";
            $resultString .= "{$this->indentSpace($indentTab)}}\n\n";
        }

        return rtrim($resultString, "\n");
    }

    protected function isFillable(ColumnDefinition $column): bool
    {
        $dataType = $column->getColumnDataType();
        $colName = $column->getColumnName();

        return !(
            ($colName === 'id' && Str::contains($dataType, 'int')) ||
            Str::contains($colName, 'token') ||
            $this->isCurrent($column) ||
            (
                ($dataType === 'timestamp' || $dataType === 'datetime') &&
                preg_match('/_at$/', $colName)
            )
        );
    }

    protected function isHidden(ColumnDefinition $column): bool
    {
        return Str::contains($column->getColumnName(), ['password', 'token']);
    }

    protected function isMethodFilter(ColumnDefinition $column): bool
    {
        $columnName = $column->getColumnName();
        $dataType = $column->getColumnDataType();

        return Str::contains($columnName, '_id') ||
            'deleted_at' !== $columnName && 'updated_at' !== $columnName &&
            Str::contains($columnName, '_at') &&
            ($dataType === 'timestamp' || $dataType === 'datetime');
    }

    protected function isSortField(ColumnDefinition $column): bool
    {
        return !$this->isHidden($column) && 'deleted_at' !== $column->getColumnName();
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

    protected function isSoftDeletes(ColumnDefinition $column): bool
    {
        return (
            $column->getColumnName() === 'deleted_at' &&
            $column->isNullable() &&
            ($column->getColumnDataType() === 'timestamp' || $column->getColumnDataType() === 'datetime')
        );
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
            case 'ClassNameSingularRequest':
                $tableName = Str::ucfirst(Str::camel(Str::singular($tableName)));
                break;
            case 'PascalSingular':
                $tableName = Str::upper((Str::singular($tableName)));
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

    protected function indentSpace($indentTab): string
    {
        return Str::padLeft('', $indentTab * $this->indentSpaceDefault, ' ');
    }

    protected function cleanArray($array, $isSortKey = true): array
    {
        $arrClean = array_filter($array, fn($item) => !is_null($item));
        if (!$isSortKey) {
            return $arrClean;
        }
        return array_values($arrClean);
    }

    protected function renderArrayValue($value, $indentTab = 0): float|int|string
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

    private function parseComment($method, $indentTab = 0): string
    {
        $comment = "{$this->indentSpace($indentTab)}/**\n";
        if (!empty($method['params'])) {
            $comment .= implode("", array_map(fn($param) => "{$this->indentSpace($indentTab)}* @param {$param}\n", $method['params']));
        }
        if (isset($method['returnType']) && 0 !== $method['returnType']) {
            $comment .= "{$this->indentSpace($indentTab)}* @return {$method['returnType']}\n";
        } else {
            $comment .= "{$this->indentSpace($indentTab)}* @return mixed\n";
        }

        $comment .= "{$this->indentSpace($indentTab)}*/\n";

        return $comment;
    }
}
