<?php

namespace App\Services\Cpro;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportDocTableBaseService
{
    protected $filename;
    protected $sheetTitle;

    public function export(string $table)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setTitle($this->sheetTitle);

        $spreadsheet = $this->headerStyle($spreadsheet);
        $spreadsheet = $this->writeListData($spreadsheet, $this->data);

        // write file
        $writer = new Xlsx($spreadsheet);
        $writer->save($this->pathFile($table));
    }

    protected function pathFile(string $table)
    {
        $storagePath = storage_path("ui-doc");
        File::ensureDirectoryExists($storagePath);

        $filename = date('Y-m-d') . '-' . $this->filename . '.xlsx';
        $pathfile = $storagePath . '/' . $filename;

        return $pathfile;
    }

    private function headerStyle(Spreadsheet $spreadsheet): Spreadsheet
    {
        $spreadsheet
            ->getActiveSheet()
            ->getStyle('B2:F2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('E7F0FD');
        
        $spreadsheet
            ->getActiveSheet()
            ->getStyle('G2:H2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFF2CC');

        $spreadsheet
            ->getActiveSheet()
            ->getStyle('I2:K2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('D9EAD3');
        
        $spreadsheet
            ->getActiveSheet()
            ->getStyle('L2:M2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('F4CCCC');
        
        $spreadsheet
            ->getActiveSheet()
            ->getStyle('N2')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('D9E2EF');
        
        $this->setStyleBorder($spreadsheet, 2);

        return $spreadsheet;
    }

    private function setStyleBorder(Spreadsheet $spreadsheet, int $row): void
    {
        $spreadsheet
            ->getActiveSheet()
            ->getStyle("B$row:N$row")
            ->applyFromArray($this->rowBorderStyle());
    }

    private function rowBorderStyle(): array
    {
        return [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'inside' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function writeListData(Spreadsheet $spreadsheet, array $listData): Spreadsheet
    {
        $sheet = $spreadsheet->getActiveSheet();

        $datas = $this->mergeHeaderData($listData);
        $sheet->fromArray($datas, null, 'B2');

        foreach($datas as $key => $data) {
            $row = $key + 2; // begin 2
            $this->setStyleBorder($spreadsheet, $row);
        }

        return $spreadsheet;
    }

    private function headerData(): array
    {
        return [
            'No.',
            'Section',
            'Name',
            'Type',
            'I/O',
            'Static Value',
            'Dynamic Value',
            'Init Display Show/Hide',
            'Place Holder',
            'Valid Input',
            'Action',
            'Logic ID',
            'Comments'
        ];
    }

    private function mergeHeaderData(array $listData): array
    {
        $res = [];
        $res[] = $this->headerData();

        foreach ($listData as $data) {
            $res[] = array_values($data);
        }

        return $res;
    }
}