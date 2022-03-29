<?php


namespace App\Services\Cpro;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Config;


class ExportDBDocTableService
{
    protected string $table;
    protected array $config = [];
    protected Spreadsheet $spreadsheet;
    protected int $startIdx = 1;

    public function __construct()
    {
        $this->config = Config::get('services.db_doc');
        $this->spreadsheet = new Spreadsheet();
    }

    public function export(array $data)
    {
        $this->table = $data['table'];
        $this->write($data);
        $this->setAutoFit($this->spreadsheet);
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->pathFile());
    }

    protected function pathFile(): string
    {
        File::ensureDirectoryExists(storage_path("db-doc"));
        $filename = date('YmdHis') . '-db_doc-' . $this->table . '.xlsx';
        return storage_path("db-doc/{$filename}");
    }

    private function write(array $listData)
    {
        $this->spreadsheet->getActiveSheet()->setTitle($this->table);
        $fieldsData = $this->prepareExportData($listData['fields'], count($this->config['headers']['fields']['header']));
        $indexesData = $this->prepareExportData($listData['indexes'], count($this->config['headers']['indexes_detail']['header']));
        $script = $this->prepareExportData($listData['script'], count($this->config['headers']['script']['header']));

        $this->writeTableData($this->config['headers']['table_name'], $this->table, $this->startIdx);

        $this->writeTableData($this->config['headers']['fields'], $fieldsData, $this->startIdx);

        $this->writeTableData($this->config['headers']['indexes'], '', $this->startIdx);

        $this->writeTableData($this->config['headers']['indexes_detail'], $indexesData, $this->startIdx);

        $this->writeTableData($this->config['headers']['script'], $script, $this->startIdx);
    }

    private function writeTableData(array $headers, array|string $listData, int $rowStart)
    {
        foreach ($headers['header'] as $i => $header) {
            $cell = "{$headers['coordinate'][$i]}{$rowStart}";
            $this->spreadsheet->getActiveSheet()->setCellValue($cell, $header);
            $this->setStyleHeaderCell($headers['bgColor'], $cell);
            $this->setStyleDefault($cell);
        }
        if (is_array($listData)) {
            $rowStart++;
            foreach ($listData as $data) {
                foreach ($data as $rowIdx => $value) {
                    $cell = "{$headers['coordinate'][$rowIdx]}{$rowStart}";
                    $this->spreadsheet->getActiveSheet()->setCellValue($cell, $value);
                    $this->setStyleDefault($cell);
                }
                $rowStart++;
            }
        } else {
            $columnIdx = count($headers['coordinate']);
            $cell = "{$this->config['coordinate'][$columnIdx]}{$rowStart}";
            $this->spreadsheet->getActiveSheet()->setCellValue($cell, $listData);
        }
        $this->startIdx = $rowStart + 1;
    }

    private function prepareExportData(array|string $data, int $numberColumn): array
    {
        $result = array();
        if (is_array($data)) {
            foreach ($data as $d) {
                $newArr = array_map(function ($i) {
                    if (!$i) return '';
                    if ($i === true) return 'O';
                    return $i;
                }, $d);
                array_push($result, array_pad($newArr, $numberColumn, ''));
            }
        } else {
            array_push($result, [$data]);
        }
        return $result;
    }

    private function setStyleHeaderCell(string $bgColor, string $cell)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyle($cell)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($bgColor);
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyle($cell)
            ->getFont()
            ->setBold(true);
    }

    private function setStyleDefault($cell)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyle($cell)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyle($cell)
            ->getAlignment()
            ->setWrapText(true);
    }

    private function setAutoFit(Spreadsheet $spreadsheet)
    {
        foreach ($this->config['coordinate'] as $columnID) {
            $spreadsheet
                ->getActiveSheet()
                ->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
    }

}
