<?php

namespace App\Utilities\Cpro\Formatters\RmsfFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\BaseBeFormatter;
use Illuminate\Support\Str;

class FactoryFormatter extends BaseBeFormatter
{
    private const STUB_FILE_NAME = 'factory';
    private const EXPORT_FILE_NAME_SUFFIX = 'Factory.php';

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

    protected function renderFactory(int $indentTab, $file)
    {
        $factoryFields = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$factoryFields) {
                if ($this->isFactoryField($column)) {
                    $factoryFields[$column->getColumnName()] = '_@' . $this->factoryValue($column);
                }
            });

        return $this->arrayRender($factoryFields, $indentTab, true);
    }

    private function isFactoryField(ColumnDefinition $column): bool
    {
        $columnName = $column->getColumnName();
        return !(
            ($columnName === 'id' && Str::contains($column->getColumnDataType(), 'int')) ||
            $columnName === 'created_at' ||
            $columnName === 'updated_at' ||
            $columnName === 'deleted_at' ||
            $column->isAutoIncrementing() ||
            ($this->isCurrent($column))
        );
    }

    private function factoryValue(ColumnDefinition $column): string
    {
        $type = $column->getColumnDataType();
        $columnName = $column->getColumnName();
        $typeParams = $column->getMethodParameters();

        $factoryValue = '1';

        if ($this->isNumberType($column)) {
            $factoryValue = $this->renderFactoryNumber($type, $typeParams, $column->isUnsigned());
        }

        if ($this->isTextType($column)) {
            $factoryValue = $this->renderFactoryString($columnName, $type, $typeParams, $column->isUUID());
        }

        if ($this->isDateOrTimeType($column)) {
            $factoryValue = $this->renderFactoryDateOrTime($type);
        }

        if ($type === 'json') {
            $factoryValue = 'json_encode($this->faker->words())';
        }

        if ($column->isUnique()) {

            if (preg_match('/^\$this->faker->/', $factoryValue, $matches)) {
                $factoryValue = Str::replace($matches[0], '$this->faker->unique()->', $factoryValue);
            }
        }

        return $factoryValue;
    }

    private function renderFactoryNumber(string $type, array $params, bool $isUnsigned): string
    {
        if (Str::contains($type, 'int')) {
            switch ($type) {
                case 'tinyint':
                    if (count($params) === 1 && (int)$params[0] === 1) {
                        return 'rand(0, 1)';
                    }

                    return $isUnsigned ? '$this->faker->numberBetween(0, 255)' : '$this->faker->numberBetween(-128, 127)';
                case 'smallint':
                    return $isUnsigned ? '$this->faker->numberBetween(0, 65535)' : '$this->faker->numberBetween(-32768, 32767)';
                case 'mediumint':
                    return $isUnsigned ? '$this->faker->numberBetween(0, 16777215)' : '$this->faker->numberBetween(-8388608, 8388607)';
                case 'bigint':
                    return $isUnsigned ? '$this->faker->numberBetween(0, PHP_INT_MAX) * 2' : '$this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX)';
                default:
                    return $isUnsigned ? '$this->faker->numberBetween(0, 4294967295)' : '$this->faker->numberBetween(-2147483648, 2147483647)';
            }
        }

        $digits = 6;

        if (count($params) > 0) {
            $digits = isset($params[1]) ? $params[0] - $params[1] : $params[0];
            $places = $params[1] ?? 0;
            $numberStrFormat = Str::padLeft('', rand(1, $digits), '#');
            if ($places > 0) {
                $numberStrFormat .= ('.' . Str::padLeft('', rand(1, $places), '#'));
            }
            $factory = '$this->faker->numerify(\'' . $numberStrFormat . '\')';
            if (!$isUnsigned) {
                $factory .= ' * $this->faker->randomElement([-1, 1])';
            }
            return $factory;

        }
        $numberStrFormat = Str::padLeft('', rand(1, $digits), '#');
        $factory = '$this->faker->numerify(\'' . $numberStrFormat . '\')';
        if (!$isUnsigned) {
            $factory .= ' * $this->faker->randomElement([-1, 1])';
        }
        return $factory;
    }

    private function renderFactoryString(string $columnName, string $type, array $params, bool $isUUID): string
    {
        switch ($type) {
            case 'char':
                if ($isUUID) {
                    return '$this->faker->uuid()';
                }
                return '$this->faker->regexify(\'[A-Za-z0-9]{' . $params[0] . '}\')';
            case 'mediumtext':
            case 'tinytext':
                return '$this->faker->text(255)';
            case 'longtext':
                return '$this->faker->paragraph()';
            case 'text':
                return '$this->faker->paragraph(5)';
            case 'enum':
                $enums = implode(', ', array_map(fn($param) => "'$param'", $params));
                return '$this->faker->randomElement([' . $enums . '])';
            default:
                $length = 255;
                if (count($params) === 1 && $params[0] < 255) {
                    $length = $params[0];
                }

                if ($length === 17 && Str::contains($columnName, 'mac')) {
                    return '$this->faker->macAddress()';
                }

                if ($length === 45 && Str::contains($columnName, 'ip')) {
                    if (Str::contains($columnName, 'v6')) {
                        return '$this->faker->ipv6()';
                    }
                    return '$this->faker->ipv4()';
                }

                if (Str::contains($columnName, 'email') && !Str::contains($columnName, 'hash')) {
                    return '$this->faker->email()';
                }

                if (Str::contains($columnName, 'url')) {
                    return '$this->faker->url()';
                }

                if (Str::contains($columnName, 'address')) {
                    return '$this->faker->address()';
                }

                if (Str::contains($columnName, 'description')) {
                    return '$this->faker->paragraph()';
                }

                if (Str::contains($columnName, 'password')) {
                    return '\'P@ssw0rd\'';
                }

                if (Str::contains($columnName, 'name')) {
                    if (Str::contains($columnName, 'first')) {
                        return '$this->faker->firstName()';
                    }
                    if (Str::contains($columnName, 'last')) {
                        return '$this->faker->lastName()';
                    }
                    if (Str::contains($columnName, 'user') || Str::contains($columnName, 'account')) {
                        return '$this->faker->userName()';
                    }
                    return '$this->faker->name()';
                }
                if (Str::contains($columnName, 'id')) {
                    return '$this->faker->regexify(\'[A-Za-z0-9]{' . $params[0] . '}\')';
                }
                return '$this->faker->text(' . $length . ')';
        }
    }

    private function renderFactoryDateOrTime(string $type): string
    {
        return match ($type) {
            'time' => '$this->faker->time()',
            'date' => '$this->faker->date(\'Y/m/d\')',
            'year' => '$this->faker->year()',
            default => 'now()',
        };
    }
}
