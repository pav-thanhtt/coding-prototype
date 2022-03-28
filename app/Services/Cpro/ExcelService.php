<?php

namespace App\Services\Cpro;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{

    protected $spreadsheet;
    protected $numberRowInserted = 0;
    protected $cellInserted = 'A1:A1';


    public function __construct($sheetName = 'Sheet')
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet
            ->getActiveSheet()
            ->setTitle($sheetName);
    }

    public function setWidthColumn($column, $width = 4)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getColumnDimension($column)
            ->setWidth($width);
        return $this;
    }


    public function createSheet($sheetName)
    {
        $this->spreadsheet
            ->createSheet()
            ->setTitle($sheetName);

        $this->spreadsheet
            ->setActiveSheetIndexByName($sheetName);
        $this->numberRowInserted = 0;
    }


    public function setStyle($style, $cells = [])
    {
        if ($cells == []) {
            $this->spreadsheet
                ->getActiveSheet()
                ->getStyle($this->cellInserted)
                ->applyFromArray($style);
            return $this;
        }

        foreach ($cells as $cell) {
            $this->spreadsheet
                ->getActiveSheet()
                ->getStyle($cell)
                ->applyFromArray($style);
        }
        return $this;
    }

    public function mergeCell($cells)
    {
        foreach ($cells as $cell) {
            $this->spreadsheet
                ->getActiveSheet()
                ->mergeCells($cell);
        }
    }

    public function addTableStatic($arrayData, $startIndex)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->fromArray($arrayData, NULL, $startIndex);

        $this->spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('D')->getAutoSize();

        return $this;
    }

    public function setCellValue($cell, $value)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->setCellValue($cell, $value);
        return $this;
    }


    public function insertRowData($rowData, $startIndex)
    {
        if (count($rowData) > 0) {
            $row = (int)substr($startIndex, 1);
            $column = substr($startIndex, 0, 1);
            if ($this->numberRowInserted != 0) {
                $row += $this->numberRowInserted;
            }

            $startIndexNew = $column . $row;
            $numberRowInsert = count($rowData);
            $this->spreadsheet
                ->getActiveSheet()
                ->insertNewRowBefore($row + 1, $numberRowInsert)
                ->fromArray($rowData, NULL, $startIndexNew);
            $this->numberRowInserted += $numberRowInsert;
            $columnEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($column) + count($rowData[0]) - 1);
            $rowEnd = $row + $numberRowInsert - 1;
            $this->cellInserted = "$startIndexNew:$columnEnd$rowEnd";
        }
        return $this;
    }


    public function export($path)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($path);
    }
}