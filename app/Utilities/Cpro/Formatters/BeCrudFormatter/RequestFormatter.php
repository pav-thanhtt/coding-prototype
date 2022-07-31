<?php

namespace App\Utilities\Cpro\Formatters\BeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestFormatter extends BaseBeFormatter
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
        return $this->tableName('ClassNameSingular');
    }

    public function renderRules(int $indentTab, $type): string
    {
        $rules = [];
        $columns = $this->tableDefinition->getColumns();
        if ($type === 'search_request') {
            $rules = [
                'keyword' => 'string',
            ];

            array_walk($columns,
                function ($column) use (&$rules) {
                    if ($this->isMethodFilter($column)) {
                        $columnName = $column->getColumnName();
                        if (Str::contains($columnName, '_at')) {
                            $rules["{$columnName}_from"] = 'date|date_format:Y/m/d';
                            $rules["{$columnName}_to"] = 'date|date_format:Y/m/d';
                        } else {
                            $rules[$columnName] = $this->searchRuleValue($column);
                        }
                    }
                }
            );

            return $this->arrayRender($rules, $indentTab, true);
        }

        array_walk($columns,
            function ($column) use (&$rules) {
                if ($this->isValidateField($column)) {
                    $rules[$column->getColumnName()] = $this->ruleValue($column);
                }
            });

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

    protected function renderClassRequestTrait(string $type) {
        if ($type === 'search_request') {
            return "\nuse App\Traits\RequestTrait;";
        }
    }

    protected function renderUseRequestTrait(string $type) {
        if ($type === 'search_request') {
            return "use RequestTrait;\n\n    ";
        }
    }

    protected function renderRequestSearchRules(string $type) {
        if ($type === 'search_request') {
            $result = ' + $this->getPaginationRules()';

            if ($this->hasSorter()) {
                $result .= ' + $this->getSortRules($this)';
            }

            return $result;
        }
    }

    protected function renderRequestSearchPrepareForValidation(int $indentTab, string $type) {
        if ($type === 'search_request') {
            $method = [
                'scope' => 'protected',
                'methodName' => 'prepareForValidation',
                'params' => [],
                'returnType' => 'void',
                'hasComment' => true,
            ];

            $method['content'] = sprintf('$this->merge($this->getPaginationPrepareForValidation($this)%s);',
            $this->hasSorter() ? ' + $this->getSortPrepareForValidation($this)' : '');

            return "\n" . $this->methodRender([$method], $indentTab);
        }
    }

    private function isValidateField(ColumnDefinition $column): bool
    {
        $columnName = $column->getColumnName();
        $columnType = $column->getColumnDataType();
        return !(
            ($columnName === 'id' && Str::contains($column->getColumnDataType(), 'int')) ||
            $column->isAutoIncrementing() ||
            $this->isCurrent($column) ||
            Str::contains($columnName, 'token') ||
            (
                ($columnType === 'timestamp' || $columnType === 'datetime') &&
                in_array($columnName, ['created_at', 'updated_at', 'deleted_at'])
            )
        );
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
            default => 'date',
        };
    }

    private function searchRuleValue(ColumnDefinition $column): string
    {
        $rule = '';

        if ($this->isNumberType($column)) {
            $rule = $this->renderRuleNumber($column->getColumnDataType(), $column->getMethodParameters(), $column->isUnsigned());
        }

        if ($this->isTextType($column)) {
            $rule = $this->renderRuleString($column->getColumnName(), $column->getColumnDataType(), $column->getMethodParameters(), $column->isUUID());
        }
        return $rule;
    }
}
