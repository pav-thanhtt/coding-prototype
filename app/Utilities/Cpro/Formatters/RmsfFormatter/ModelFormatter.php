<?php

namespace App\Utilities\Cpro\Formatters\RmsfFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\BaseBeFormatter;
use Illuminate\Support\Str;

class ModelFormatter extends BaseBeFormatter
{
    private const STUB_FILE_NAME = 'model';
    private const EXPORT_FILE_NAME_SUFFIX = '.php';

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

    protected function renderClassSoftDeletes(): string
    {
        if ($this->hasSoftDeletes()) {
            return "use Illuminate\Database\Eloquent\SoftDeletes;\n";
        }
        return '';
    }

    protected function renderUseSoftDeletes(): string
    {
        if ($this->hasSoftDeletes()) {
            return ', SoftDeletes';
        }
        return '';
    }

    protected function renderClassSortableTrait(): string
    {
        if ($this->hasSorter()) {
            return "\nuse App\Traits\SortableTrait;";
        }
        return '';
    }

    protected function renderUseSortableTrait(): string
    {
        if ($this->hasSorter()) {
            return ', SortableTrait';
        }
        return '';
    }

    /**
     * @param int $indentTab
     * @param $file
     * @return string
     */
    protected function renderFillable(int $indentTab, $file): string
    {
        $columns = $this->tableDefinition->getColumns();

        $fillable = array_map(function ($column) {
            if ($this->isFillable($column)) {
                return $column->getColumnName();
            }
        }, $columns);

        $fillable = $this->cleanArray($fillable);

        if (empty($fillable)) {
            return '';
        }

        $lines = $this->arrayRender($fillable, $indentTab + 1);

        return sprintf(
            '%s%s%s%s%s%s',
            $this->indentSpace($indentTab),
            "protected \$fillable = [\n",
            $lines,
            "\n",
            $this->indentSpace($indentTab),
            "];"
        );
    }

    protected function renderHidden(int $indentTab): string
    {
        $columns = $this->tableDefinition->getColumns();

        $hidden = array_map(function ($column) {
            if ($this->isHidden($column)) {
                return $column->getColumnName();
            }
        }, $columns);

        $hidden = $this->cleanArray($hidden);

        if (empty($hidden)) {
            return '';
        }

        $lines = $this->arrayRender($hidden, $indentTab + 1);

        return sprintf(
            '%s%s%s%s%s%s',
            $this->indentSpace($indentTab),
            "protected \$hidden = [\n",
            $lines,
            "\n",
            $this->indentSpace($indentTab),
            "];"
        );
    }

    protected function renderDates(int $indentTab): string
    {
        $columns = $this->tableDefinition->getColumns();

        $dates = array_map(function ($column) {
            if ($this->isDateOrTimeType($column) &&
            !in_array($column->getColumnName(), ['created_at', 'updated_at', 'deleted_at']) &&
            !$this->isCurrent($column)
            ) {
                return $column->getColumnName();
            }
        }, $columns);

        $dates = $this->cleanArray($dates);

        if (empty($dates)) {
            return '';
        }

        $lines = $this->arrayRender($dates, $indentTab + 1);

        return sprintf(
            '%s%s%s%s%s%s',
            $this->indentSpace($indentTab),
            "protected \$dates = [\n",
            $lines,
            "\n",
            $this->indentSpace($indentTab),
            "];"
        );
    }

    protected function renderIncrementing(int $indentTab): string
    {
        $idColumn = $this->tableDefinition->getColumnByName('id');
        if (!$idColumn->isAutoIncrementing())
        {
            return $this->indentSpace($indentTab) . 'public $incrementing = false;';
        }

        return '';
    }

    protected function renderKeyType(int $indentTab): string
    {
        $idColumn = $this->tableDefinition->getColumnByName('id');
        $idDataType = $idColumn->getColumnDataType();
        if (!Str::contains($idDataType, 'int') && Str::contains($idDataType, 'char'))
        {
            return $this->indentSpace($indentTab) . 'protected $keyType = \'string\';';
        }

        return '';
    }

    protected function renderSortable(int $indentTab): string
    {
        $sortFields = array_map(function ($column) {
            if ($this->isSortField($column)) {
                return $column->getColumnName();
            }
        }, $this->tableDefinition->getColumns());

        $sortFields = $this->cleanArray($sortFields);

        if (empty($sortFields)) {
            return '';
        }

        $lines = $this->arrayRender($sortFields, $indentTab + 1);

        return sprintf(
            '%s%s%s%s%s%s',
            $this->indentSpace($indentTab),
            "protected \$sortable = [\n",
            $lines,
            "\n",
            $this->indentSpace($indentTab),
            "];"
        );
    }

    protected function renderProperty(int $indentTab, $file): string
    {
        $text = $this->renderIncrementing($indentTab, $file);
        $text = sprintf('%s%s%s', $text, empty($text) ? $text : "\n\n", $keyType = $this->renderKeyType($indentTab, $file));
        $text = sprintf('%s%s%s', $text, empty($keyType) ? $keyType : "\n\n", $fillable = $this->renderFillable($indentTab, $file));
        $text = sprintf('%s%s%s', $text, empty($fillable) ? $fillable : "\n\n", $hidden = $this->renderHidden($indentTab, $file));
        $text = sprintf('%s%s%s', $text, empty($hidden) ? $hidden : "\n\n", $dates = $this->renderDates($indentTab, $file));
        $text = sprintf('%s%s%s', $text, empty($dates) ? $dates : "\n\n", $this->renderSortable($indentTab, $file));

        return rtrim($text);
    }

    private function hasSoftDeletes(): bool
    {
        $deleteColumn = $this->tableDefinition->getColumnByName('deleted_at');
        return (
            !is_null($deleteColumn) &&
            $deleteColumn->isNullable() &&
            ($deleteColumn->getColumnDataType() === 'timestamp' || $deleteColumn->getColumnDataType() === 'datetime')
        );
    }
}
