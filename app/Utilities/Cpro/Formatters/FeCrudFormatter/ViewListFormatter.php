<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\ColumnDefinition;
use App\Utilities\Cpro\Definitions\TableDefinition;
use Illuminate\Support\Str;

class ViewListFormatter extends BaseFeFormatter
{
    private const STUB_FILE_NAME = 'view_list';
    private const EXPORT_FILE_NAME_SUFFIX = '.vue';
    private const CELL_SLOT_STUB_NAME = 'view_list_cell_slot';
    private const CELL_SLOT_STRING_CONTENT_STUB_NAME = 'view_list_cell_slot_string_content';
    private const CELL_SLOT_DATETIME_CONTENT_STUB_NAME = 'view_list_cell_slot_datetime_content';
    private const CELL_SLOT_CONTENT_STUB_NAME = 'view_list_cell_slot_content';
    private const FILTER_DATETIME_COMPUTED_STUB_NAME = 'view_list_filter_datetime_computed';
    private const NORMALIZE_DATE_FUNCTION_STUB_NAME = 'view_list_normalize_date_function';
    private const NORMALIZE_DATE_FIELD_STUB_NAME = 'view_list_normalize_date_field';
    private const NORMALIZE_DATE_WATCH_STUB_NAME = 'view_list_normalize_date_watch';
    private const SEARCH_PARAM_REACTIVE_STUB_NAME = 'view_list_search_param_reactive';
    private const ON_TABLE_CHANGE_STUB_NAME = 'view_list_on_table_change_function';
    private const FILTER_FORM_GROUP_STUB_NAME = 'view_list_filter_form_group';
    private const FILTER_FORM_DATETIME_CONTROL_STUB_NAME = 'view_list_filter_form_datetime_control';
    private const FILTER_FORM_CONTROL_STUB_NAME = 'view_list_filter_form_control';
    private const COLUMN_VARIABLE_STUB_NAME = 'view_list_column_variable';

    public function __construct(TableDefinition $tableDefinition)
    {
        parent::__construct($tableDefinition);
        $this->fileName[self::STUB_FILE_NAME] = $this->tableName('ClassNameSingular') . self::EXPORT_FILE_NAME_SUFFIX;
    }

    protected function getStubFileName(): string
    {
        return self::STUB_FILE_NAME;
    }

    public function getExportDirName(): string
    {
        return $this->tableName('ClassNameSingular');
    }

    public function getExportFileName(?string $options = ''): string
    {
        return $this->fileName[self::STUB_FILE_NAME];
    }

    public function renderCellSlot(int $indentTab, $file)
    {
        $cellSlots = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$cellSlots, $file) {
                if (!$this->isHidden($column) && !$this->isSoftDeletes($column)) {
                    $cellSlots[] = $this->getCellSlotContent($column, $file);
                }
            }
        );
        return $this->renderHtml($indentTab, $cellSlots);
    }

    public function renderOnTableChangeFunction(int $indentTab, $file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        $stubContent = $this->getStubFile(self::ON_TABLE_CHANGE_STUB_NAME);

        return "\n\n" . $this->renderHtml($indentTab, [$stubContent]) . "\n";
    }

    public function renderSorterConstant(int $indentTab, $file)
    {
        if (!$this->hasSorter()) {
            return '      ';
        }
        return "      const sorted = sortedInfo.value\n      ";
    }

    public function renderImportSorterText($file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        return "\nimport Sorter from '@/types/entities/Sorter'";
    }

    public function renderImportNormalizeHelperText($file)
    {
        if (!$this->hasNormalizeDates()) {
            return '';
        }
        return "\nimport { normalizeFormDate } from '@/utils/helper'";
    }

    public function renderImportMomentText($file)
    {
        if (!$this->hasResourceDateTimeType()) {
            return '';
        }
        return "\nimport moment from 'moment'";
    }

    public function renderDateFormatConstantText(int $indentTab, $file)
    {
        if (!$this->hasNormalizeDates()) {
            return '';
        }
        return sprintf("\n%sDATE_FORMAT,", $this->indentSpace($indentTab));
    }

    public function renderImportRefText($file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        return ", ref";
    }

    public function renderCallNormalizeDatesFunction(int|string $options)
    {
        if (!$this->hasNormalizeDates()) {
            return '';
        }

        if (preg_match('/\d+/', $options)) {
            return sprintf(",\n%s...normalizeDates(),", $this->indentSpace($options));
        }

        return ", ...normalizeDates()";
    }

    public function renderCharacterMaxLengthText(int $indentTab, $file)
    {
        if (!$this->hasTooltipCell()) {
            return '';
        }

        return sprintf("\n%sCHARACTER_CELL_MAX_LENGTH,", $this->indentSpace($indentTab));
    }

    public function renderDateTimeFormatConstantText(int $indentTab, $file)
    {
        if (!$this->hasResourceDateTimeType()) {
            return '';
        }
        return sprintf("\n%sDATE_TIME_FORMAT,", $this->indentSpace($indentTab));
    }

    public function renderMomentReturnText(int $indentTab, $file)
    {
        if (!$this->hasResourceDateTimeType()) {
            return '';
        }
        return sprintf("\n%smoment,", $this->indentSpace($indentTab));
    }

    public function renderOnTableChangeReturnText(int $indentTab, $file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        return sprintf("\n%sonTableChange,", $this->indentSpace($indentTab));
    }

    public function renderFilterFormGroups(int $indentTab, $file)
    {
        $filterFormGroups = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$filterFormGroups, $file) {
                if ($this->isMethodFilter($column)) {
                    $filterFormGroups[] = $this->getFilterFormGroupContent($column, $file);
                }
            }
        );
        return "\n{$this->renderHtml($indentTab, $filterFormGroups)}";
    }

    public function renderInterfaceAntd($file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        return "\nimport { TableState, TableStateFilters } from 'ant-design-vue/es/table/interface'";
    }

    private function getFilterFormGroupContent(ColumnDefinition $column, $file)
    {
        $stubContent = $this->getStubFile(self::FILTER_FORM_GROUP_STUB_NAME);
        $this->column = $column;
        $this->columnName = $column->getColumnName();
        $this->columnLabel = $this->getColumnLabel($this->columnName);

        $html = $this->replace($stubContent, $file);

        return $this->replaceVariable($html);
    }

    protected function renderFilterFormControl(int $indentTab, $file) {
        $stubContent = '';
        if ($this->isFilterDatetimeField($this->column)) {
            $stubContent = $this->getStubFile(self::FILTER_FORM_DATETIME_CONTROL_STUB_NAME);
        } else {
            $stubContent = $this->getStubFile(self::FILTER_FORM_CONTROL_STUB_NAME);
        }
        return $this->renderHtml($indentTab, [$this->replaceVariable($stubContent)]);
    }
    public function renderCallOnTableChange($file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        return '        @onTableChange="onTableChange"';
    }

    public function renderSorterInfoConstant(int $indentTab, $file)
    {
        if (!$this->hasSorter()) {
            return '';
        }
        $code =  $this->renderHtml($indentTab, ["const sortedInfo = ref({\n...route.query.sort && { field: route.query.sort },\n...route.query.sort_direction && { order: `\${route.query.sort_direction}end` }\n})"]);

        return "\n\n{$code}\n";
    }

    public function renderResetSorter(int $indentTab, $file)
    {
        if (!$this->hasSorter()) {
            return '      ';
        }
        return $this->renderHtml($indentTab, ["searchParam.sort = undefined\nsearchParam.sort_direction = undefined\nsortedInfo.value = {}"]) . "\n      ";
    }

    public function renderFilterFieldQueryString(int $indentTab, $file)
    {
        $filterFieldQueryString = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$filterFieldQueryString, $file) {
                if ($this->isMethodFilter($column) && !$this->isFilterDatetimeField($column)) {
                    $this->columnName = $column->getColumnName();
                    $filterFieldQueryString[] = $this->replaceVariable("...route.query.{!columnName!} && { {!columnName!}: route.query.{!columnName!} },");
                }
            }
        );
        return ",\n" . rtrim($this->renderHtml($indentTab, $filterFieldQueryString), ',');
    }

    public function renderFilterDateField(int $indentTab, $file)
    {
        $filterDateField = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$filterDateField, $file) {
                if ($this->isFilterDatetimeField($column)) {
                    $this->columnName = $column->getColumnName();
                    $filterDateField[] = $this->replaceVariable("searchParam.{!columnName!}_from = undefined\nsearchParam.{!columnName!}_to = undefined");
                }
            }
        );
        return "\n" . $this->renderHtml($indentTab, $filterDateField);
    }

    public function renderSorterQueryString(int $indentTab, $file)
    {
        if (!$this->hasSorter()) {
            return '';
        }

        return ",\n" . $this->renderHtml($indentTab, ["...route.query.sort && { sort: route.query.sort },\n...route.query.sort_direction && { sort_direction: route.query.sort_direction }"]);
    }

    public function renderNormalizeDatesWatch(int $indentTab, $file)
    {
        $normalizeDatesWatch = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$normalizeDatesWatch, $file) {
                if ($this->isFilterDatetimeField($column)) {
                    $normalizeDatesWatch[] = $this->getNormalizeDatesWatch($column, $file);
                }
            }
        );
        return ",\n" . rtrim($this->renderHtml($indentTab, $normalizeDatesWatch), ',');
    }

    public function renderFilterDatetimeVariableComputed(int $indentTab, $file)
    {
        $variableFilterDatetimeComputed = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$variableFilterDatetimeComputed, $file) {
                if ($this->isFilterDatetimeField($column)) {
                    $variableFilterDatetimeComputed[] = $this->getVariableFilterDatetimeComputedContent($column, $file);
                }
            }
        );
        return "\n{$this->renderHtml($indentTab, $variableFilterDatetimeComputed)}";
    }

    public function renderColumnVariable(int $indentTab, $file)
    {
        $columnVariables = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$columnVariables, $file) {
                if (!$this->isHidden($column) && !$this->isSoftDeletes($column)) {
                    $columnVariables[] = $this->getColumnVariableContent($column, $file);
                }
            }
        );
        return "\n" . $this->renderHtml($indentTab, $columnVariables);
    }

    public function renderNormalizeDatesFunction(int $indentTab, $file)
    {
        if (!$this->hasNormalizeDates()) {
            return '';
        }

        $stubContent = $this->getStubFile(self::NORMALIZE_DATE_FUNCTION_STUB_NAME);

        $html = $this->replace($stubContent, $file);
        return "\n\n{$this->renderHtml($indentTab, [$html])}\n";
    }

    private function getCellSlotContent(ColumnDefinition $columnDefinition, $file)
    {
        $stubContent = $this->getStubFile(self::CELL_SLOT_STUB_NAME);
        $this->column = $columnDefinition;
        $this->columnName = $columnDefinition->getColumnName();

        $html = $this->replace($stubContent, $file);

        return $this->replaceVariable($html);
    }

    private function getColumnVariableContent(ColumnDefinition $columnDefinition, $file)
    {
        $stubContent = $this->getStubFile(self::COLUMN_VARIABLE_STUB_NAME);
        $this->column = $columnDefinition;
        $this->columnName = $columnDefinition->getColumnName();
        $this->columnLabel = $this->getColumnLabel($this->columnName);
        $this->fixedText = $this->columnName === 'id' ? "\n  fixed: 'left'," : '';
        $this->sorterText = $this->hasSorter() ? "\n  sorter: true,\n  sortOrder: sorted.field === '{$this->columnName}' && sorted.order," : '';

        return $this->replaceVariable($stubContent);
    }

    private function getNormalizeDatesWatch(ColumnDefinition $columnDefinition, $file)
    {
        $stubContent = $this->getStubFile(self::NORMALIZE_DATE_WATCH_STUB_NAME);
        $this->columnName = $columnDefinition->getColumnName();

        return $this->replaceVariable($stubContent);
    }

    protected function renderCellSlotContent(int $indentTab, $file)
    {
        $stubContent = '';

        if ($this->isTextType($this->column) && !Str::contains($this->column->getColumnName(), 'id')
            && $this->column->getMethodParameters()[0] > 50) {
            $stubContent = $this->getStubFile(self::CELL_SLOT_STRING_CONTENT_STUB_NAME);
        } elseif($this->isDateOrTimeType($this->column)) {
            $this->momentFormat = match ($this->column->getColumnDataType()) {
                'date' => 'DATE_FORMAT',
                default => 'DATE_TIME_FORMAT',
            };

            $stubContent = $this->getStubFile(self::CELL_SLOT_DATETIME_CONTENT_STUB_NAME);
        } else {
            $stubContent = $this->getStubFile(self::CELL_SLOT_CONTENT_STUB_NAME);
        }

        return $this->renderHtml($indentTab, [$this->replaceVariable($stubContent)]);
    }

    protected function renderSearchParamReactive(int $indentTab, $file) {
        if (!$this->hasNormalizeDates()) {
            return '';
        }

        $searchParamReactive = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$searchParamReactive, $file) {
                if ($this->isFilterDatetimeField($column)) {
                    $searchParamReactive[] = $this->getSearchParamReactive($column, $file);
                }
            }
        );
        return ",\n" . rtrim($this->renderHtml($indentTab, $searchParamReactive), ',');
    }

    protected function renderNormalizeDateFields(int $indentTab, $file)
    {
        $variableFilterDatetimeComputed = [];
        $columns = $this->tableDefinition->getColumns();
        array_walk($columns,
            function ($column) use (&$variableFilterDatetimeComputed, $file) {
                if ($this->isFilterDatetimeField($column)) {
                    $variableFilterDatetimeComputed[] = $this->getNormalizeDateFieldContent($column, $file);
                }
            }
        );
        return rtrim($this->renderHtml($indentTab, $variableFilterDatetimeComputed), ',');
    }

    private function getVariableFilterDatetimeComputedContent(ColumnDefinition $columnDefinition, $file) {
        $stubContent = $this->getStubFile(self::FILTER_DATETIME_COMPUTED_STUB_NAME);
        $this->columnName = $columnDefinition->getColumnName();
        $this->columnCamel = Str::camel($this->columnName);

        return $this->replaceVariable($stubContent);
    }

    private function getNormalizeDateFieldContent(ColumnDefinition $columnDefinition, $file) {
        $stubContent = $this->getStubFile(self::NORMALIZE_DATE_FIELD_STUB_NAME);
        $this->columnName = $columnDefinition->getColumnName();

        return $this->replaceVariable($stubContent);
    }

    private function getSearchParamReactive(ColumnDefinition $columnDefinition, $file) {
        $stubContent = $this->getStubFile(self::SEARCH_PARAM_REACTIVE_STUB_NAME);
        $this->columnName = $columnDefinition->getColumnName();
        $this->columnCamel = Str::camel($this->columnName);

        return $this->replaceVariable($stubContent);
    }

    private function hasNormalizeDates() {
        $columns = $this->tableDefinition->getColumns();

        foreach($columns as $column) {
            if($this->isFilterDatetimeField($column)) {
                return true;
            }
        }
        return false;
    }

    private function hasSorter() {
        $columns = $this->tableDefinition->getColumns();

        foreach($columns as $column) {
            if($this->isSortField($column)) {
                return true;
            }
        }
        return false;
    }

    private function hasResourceDateTimeType() {
        $columns = $this->tableDefinition->getColumns();

        foreach($columns as $column) {
            $dataType = $column->getColumnDataType();
            if(!$this->isHidden($column) && !$this->isSoftDeletes($column)
            && ($dataType === 'timestamp' || $dataType === 'datetime')) {
                return true;
            }
        }
        return false;
    }

    private function hasTooltipCell() {
        $columns = $this->tableDefinition->getColumns();

        foreach($columns as $column) {
            if($this->isTextType($column) && !Str::contains($column->getColumnName(), 'id')
            && $column->getMethodParameters()[0] > 50) {
                return true;
            }
        }
        return false;
    }
}
