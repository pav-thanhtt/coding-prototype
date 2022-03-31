<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestFormatter extends BaseFormatter
{
    private const STUB_FILE_NAME = 'request';
    private const EXPORT_SEARCH_FILE_NAME = 'SearchRequest.php';
    private const EXPORT_STORE_FILE_NAME = 'StoreRequest.php';
    private const EXPORT_UPDATE_FILE_NAME = 'UpdateRequest.php';

    protected array $columns;

    public function __construct(TableDefinition $tableDefinition)
    {
        parent::__construct($tableDefinition);
        $this->fileName['search_' . self::STUB_FILE_NAME] = $this->tableName('ClassNameSingular') . self::EXPORT_SEARCH_FILE_NAME;
        $this->fileName['store_' . self::STUB_FILE_NAME] = $this->tableName('ClassNameSingular') . self::EXPORT_STORE_FILE_NAME;
        $this->fileName['update_' . self::STUB_FILE_NAME] = $this->tableName('ClassNameSingular') . self::EXPORT_UPDATE_FILE_NAME;
    }

    protected function getStubFileName(): string
    {
        return self::STUB_FILE_NAME;
    }

    public function getExportFileName(?string $options = ''): string
    {
        return $this->fileName[$options];
    }

    public function getExportDirName(): string
    {
        return $this->tableName('ClassNamePlural');
    }

    public function renderRules(int $indentTab, $type): string
    {
        if ($type === 'search_request') {
            $rules = [
                'page' => 'int|min:1',
                'per_page' => 'int|min:1',
                'keyword' => 'string',
                'include' => '_@Rule::in([\'meta\'])',
            ];
            return $this->arrayRender($rules, $indentTab, true);
        }
        $rules = [];

        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$rules) {
                if ($this->isValidateField($column)) {
                    $rules[$column->getColumnName()] = $this->ruleValue($column);
                }
            },
            $rules);

        $rules = $this->cleanArray($rules, false);

        if ($type === 'store_request') {
            $rules = array_map(function ($rule) {
                if (is_string($rule)) {
                    if (!Str::contains($rule, 'nullable')) {
                        return "required|$rule";
                    }
                } elseif (is_array($rule) && !in_array('nullable', $rule)) {
                    $rule[] = 'required';
                }
                return $rule;
            }, $rules);
        }
        return $this->arrayRender($rules, $indentTab, true);

    }

    private function isValidateField(ColumnDefinition $column): bool
    {
        $columnName = $column->getColumnName();
        $columnType = $column->getColumnDataType();
        if (
            $columnName === 'id' ||
            $column->isAutoIncrementing() ||
            $this->isCurrent($column) ||
            (($column->getColumnDataType() === 'timestamp' || $columnType === 'datetime')
                && preg_match('/_at$/', $columnName)) ||
            Str::contains($columnName, 'token')
        ) {
            return false;
        }
        return true;
    }

    private function ruleValue(ColumnDefinition $column): string|array
    {
        $rule = '';

        $columnName = $column->getColumnName();
        $type = $column->getColumnDataType();
        $typeParams = $column->getMethodParameters();

        if ($this->isNumberType($column)) {
            $rule = $this->renderRuleNumber($type, $typeParams, $column->isUnsigned());
        }

        if ($this->isTextType($column)) {
            $rule = $this->renderRuleString($columnName, $type, $typeParams, $column->isUUID());
        }

        if ($this->isDateOrTimeType($column)) {
            $rule = $this->renderRuleDateOrTime($type);
        }

        if ($type === 'json') {
            $rule = 'json';
        }

        if ($column->isUnique()) {
            $tableName = $this->tableDefinition->getTableName();
            $rule = empty($rule) ? "unique:$tableName" : (is_array($rule) ? [...$rule, "unique:$tableName"] : "$rule|unique:$tableName");
        }

        if ($column->isNullable()) {
            $rule = empty($rule) ? 'nullable' : (is_array($rule) ? [...$rule, 'nullable'] : "$rule|nullable");
        }
        return $rule;
    }

    private function renderRuleNumber(string $type, array $params, bool $isUnsigned): array|string
    {

        if (Str::contains($type, 'int')) {
            switch ($type) {
                case 'tinyint':
                    if (count($params) === 1 && (int)$params[0] === 1) {
                        return 'boolean';
                    }

                    return $isUnsigned ? 'integer|integer|between:0,255' : 'integer|between:-128,127';
                case 'smallint':
                    return $isUnsigned ? 'integer|between:0,65535' : 'integer|between:-32768,32767';
                case 'mediumint':
                    return $isUnsigned ? 'integer|between:0,16777215' : 'integer|between:-2147483648,2147483647';
                case 'bigint':
                    return $isUnsigned ? ('numeric|between:0,' . PHP_INT_MAX * 2 + 1) : 'integer';
                default:
                    return $isUnsigned ? 'integer|between:0,4294967295' : 'integer|between:-2147483648,2147483647';
            }
        }

        $rule = 'numeric';

        if (count($params) > 0) {
            $digits = isset($params[1]) ? $params[0] - $params[1] : $params[0];
            $places = $params[1] ?? 0;
            $rule = [$rule];

            if ($isUnsigned) {
                if ($places > 0) {
                    $rule[] = "regex:/^(\d{1,$digits}\.\d{1,$places}|\d{1,$digits})$/";
                } else {
                    $rule[] = "regex:/^\d{1,$digits}$/";
                }
            } else {
                if ($places > 0) {
                    $rule[] = "regex:/^-?(\d{1,$digits}\.\d{1,$places}|\d{1,$digits})$/";
                } else {
                    $rule[] = "regex:/^-?\d{1,$digits}$/";
                }
            }
        }

        if ($isUnsigned && !is_array($rule)) {
            return "$rule|min:0";
        }

        return $rule;
    }

    private function renderRuleString(string $columnName, string $type, array $params, bool $isUUID): array|string
    {
        switch ($type) {
            case 'char':
                if ($isUUID) {
                    return 'uuid';
                }
                return "string|min:$params[0]|max:$params[0]";
            case 'tinytext':
                return 'string|max:255';
            case 'mediumtext':
                return 'string|max:16777215';
            case 'longtext':
                return 'string|max:4294967295';
            case 'text':
                return 'string';
            case 'enum':
                $enums = implode(', ', array_map(fn($param) => "'$param'", $params));
                return [
                    'string',
                    "_@Rule::in([$enums])",
                ];
            default:
                $length = 255;
                if (count($params) === 1) {
                    $length = $params[0];
                }

                if ($length === 17 && Str::contains($columnName, 'mac')) {
                    return 'mac_address';
                }

                if ($length === 45 && Str::contains($columnName, 'ip')) {
                    if (Str::contains($columnName, 'v6')) {
                        return 'ipv6';
                    }
                    if (Str::contains($columnName, 'v4')) {
                        return 'ipv4';
                    }
                    return 'ip';
                }

                if (Str::contains($columnName, 'email') && !Str::contains($columnName, 'hash')) {
                    return 'email';
                }

                if (Str::contains($columnName, 'url')) {
                    return 'url';
                }
                return "string|max:$length";
        }
    }

    private function renderRuleDateOrTime(string $type): string
    {
        return match ($type) {
            'time' => 'date_format:H:i:s',
            'date' => 'date|date_format:Y/m/d',
            'year' => 'date_format:Y',
            'timestamp' => 'date|date_format:Y/m/d H:i:s|after_or_equal:1970/01/01 00:00:01|before_or_equal:2038/01/19 03:14:07',
            default => 'date|date_format:Y/m/d H:i:s',
        };
    }

    public function renderClassRule($type): string
    {
        if ($this->hasUseEnumField($type)) {
            return "use Illuminate\Validation\Rule;\n";
        }

        return '';
    }

    private function hasUseEnumField($type): bool
    {
        if ($type === 'search_request') {
            return true;
        }
        $enumFields = DB::select("show columns from {$this->tableDefinition->getTableName()} where `Type` Like 'enum%';");
        return count($enumFields) > 0;
    }
}
