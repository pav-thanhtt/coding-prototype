<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;

class ModelFormatter extends BaseFormatter
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

        return $this->arrayRender($fillable, $indentTab);
    }

    public function renderHidden(int $indentTab): string
    {
        $columns = $this->tableDefinition->getColumns();

        $fillable = array_map(function ($column) {
            if ($this->isHidden($column)) {
                return $column->getColumnName();
            }
        }, $columns);

        $fillable = $this->cleanArray($fillable);

        return $this->arrayRender($fillable, $indentTab);
    }

    private function hasSoftDeletes(): bool
    {
        if (!is_null($deleteColumn = $this->tableDefinition->getColumnByName('deleted_at'))) {
            return $deleteColumn->isNullable() &&
                ($deleteColumn->getColumnDataType() === 'timestamp' || $deleteColumn->getColumnDataType() === 'datetime');
        }

        return false;
    }
}
