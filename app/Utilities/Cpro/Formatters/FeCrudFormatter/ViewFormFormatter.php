<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Str;

class ViewFormFormatter extends BaseFeFormatter
{

    private const STUB_FILE_NAME = 'view_form';
    private const EXPORT_FILE_NAME_SUFFIX = 'CreateUpdate.vue';
    private const FORM_GROUP_STUB_NAME = 'view_form_form_group';
    private const FORM_CONTROL_STUB_NAME = 'view_form_form_control';
    private const FORM_CONTROL_SELECT_STUB_NAME = 'view_form_form_control_select';

    protected $columnName, $columnLabel, $requiredTxt = '';

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

    public function getExportDirName(): string
    {
        return $this->tableName('BindingName');
    }

    public function renderFormControls(int $indentTab, $file): string
    {
        $formGroups = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$formGroups, $file) {
                if ($this->isFillable($column)) {
                    $formGroups[] = $this->getFormGroupContent($column, $file);
                }
            }
        );

        return $this->renderHtml($indentTab, $formGroups);
    }

    public function renderInitCurrent(int $indentTab, $file): string
    {
        $lines = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$lines) {
                if ($this->isFillable($column) && !$column->isNullable()) {
                    $lines[$column->getColumnName()] = $this->initValue($column);
                }
            }
        );

        return $this->objectRender($lines, $indentTab);
    }

    public function renderRules(int $indentTab, $file): string
    {
        $lines = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$lines) {
                if ($this->isFillable($column) && !$column->isNullable() && $this->isTextType($column)) {
                    $lines[] = $this->getRuleContent($column);
                }
            }
        );

        return rtrim($this->renderHtml($indentTab, $lines), ',');
    }

    public function renderImportDateFormat($file): string
    {
        if (!$this->hasFillableDateTime()) {
            return '';
        }

        return "import { DATE_TIME_FORMAT } from '@/config/constants'\n";
    }

    public function renderReturnDateFormat($indentTab, $file): string
    {
        if (!$this->hasFillableDateTime()) {
            return '';
        }

        return sprintf("\n%sDATE_TIME_FORMAT,", $this->indentSpace($indentTab));
    }

    private function getRuleContent(ColumnDefinition $column) {
        return "{$column->getColumnName()}: {\n  required: true,\n  trigger: 'blur'\n},";
    }

    private function getFormGroupContent(ColumnDefinition $columnDefinition, $file)
    {

        $stubContent = $this->getStubFile(self::FORM_GROUP_STUB_NAME);

        $this->column = $columnDefinition;
        $this->columnName = $columnDefinition->getColumnName();
        $this->columnLabel = $this->getColumnLabel($this->columnName);
        $this->requiredTxt = $columnDefinition->isNullable() ? '' : '&nbsp;&nbsp;<span class="required-text">Required</span>';

        $html = $this->replace($stubContent, $file);

        return $this->replaceVariable($html);
    }

    protected function renderFormControl(int $indentTab, $file)
    {
        $dataType = $this->column->getColumnDataType();
        $this->tableName = $this->tableName('ClassNameSingular');
        if ($dataType === 'enum' || $dataType === 'tinyint' && $this->column->getMethodParameters()[0] === 1) {
            $stubContent = $this->getStubFile(self::FORM_CONTROL_SELECT_STUB_NAME);
            $this->optionList = '';
            if ($dataType === 'tinyint') {
                $this->optionList = "  <a-select-option value=\"1\">True</a-select-option>\n  <a-select-option value=\"0\">False</a-select-option>";
            } else {
                foreach ($this->column->getMethodParameters() as $param) {
                    $this->optionList .= "  <a-select-option value=\"{$param}\">" . Str::ucfirst($param) . "</a-select-option>\n";
                }
                $this->optionList = rtrim($this->optionList, "\n");
            }

            return $this->renderHtml($indentTab, [$this->replaceVariable($stubContent)]);
        } else {
            $stubContent = $this->getStubFile(self::FORM_CONTROL_STUB_NAME);
            $this->columnTag = match ($dataType) {
                'varchar', 'char' => "input :maxlength=\"{$this->column->getMethodParameters()[0]}\"",
                'text', 'longtext', 'mediumtext' => 'textarea',
                'int', 'bigint', 'mediumint', 'smallint' => 'input-number',
                'float', 'double' => "input-number :step=\"0.01\"",
                'datetime', 'timestamp' => 'date-picker :format="DATE_TIME_FORMAT"'
            };
        }

        return sprintf('%s%s', $this->indentSpace($indentTab), $this->replaceVariable($stubContent));
    }

    private function hasFillableDateTime () {
        $columns = $this->tableDefinition->getColumns();

        foreach($columns as $column) {
            if ($this->isFillable($column) && $this->isDateOrTimeType($column)) {
                return true;
            }
        }

        return false;
    }
}
