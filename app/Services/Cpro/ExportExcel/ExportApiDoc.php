<?php

namespace App\Services\Cpro\ExportExcel;

use App\Services\Cpro\DatabaseService;
use App\Services\Cpro\ExcelService;
use Carbon\Carbon;

class ExportApiDoc extends ExcelService
{
    private $tab = "   ";
    private $tableDetail;

    function __construct($sheetName = 'Sheet', $tableName)
    {
        parent::__construct($sheetName);
        $this->tableDetail = DatabaseService::getTableFormatType($tableName);
    }

    public function getJsonFailIndex()
    {
        $data = [
            "code" => 200,
            "message" => "successful",
        ];
        return json_encode($data, JSON_PRETTY_PRINT);;
    }

    public function getDataSample($numRecord, $isFakeValue = True, $isShowID = True)
    {
        $data = [];
        $tableDetail = $this->tableDetail;

        for ($i = 1; $i <= $numRecord; $i++) {
            $itemData = [];
            foreach ($tableDetail as $columnDetail) {
                if (!$isShowID && $columnDetail->Key == "PRI") {

                    continue;

                }
                $itemData[$columnDetail->Field] = "";
                if ($isFakeValue) {
                    $itemData[$columnDetail->Field] = $this->dataFake($columnDetail, $i);
                }
            }
            $data[] = $itemData;
        }

        if ($numRecord == 1) {
            return $data[0];
        }
        return $data;
    }

    public function getJsonSuccessIndex($tableName)
    {
        $dataSample = $this->getDataSample(2, True, True);

        $data = [
            "message" => "successful",
            "meta" => [
                "page" => 2,
                "per_page" => 2,
                "page_count" => 2,
                "total_count" => 10,
                "links" => [
                    "self" => "http://localhost:8000/api/v1/$tableName?page=2&per_page=2&include=meta",
                    "first" => "http://localhost:8000/api/v1/$tableName?page=1&per_page=2&include=meta",
                    "previous" => "http://localhost:8000/api/v1/$tableName?page=1&per_page=2&include=meta",
                    "next" => "http://localhost:8000/api/v1/$tableName?page=3&per_page=2&include=meta",
                    "last" => "http://localhost:8000/api/v1/$tableName?page=5&per_page=2&include=meta"
                ],
            ],
            "data" => $dataSample

        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getJsonFailStore()
    {

        $tableDetail = $this->tableDetail;
        $messageError = [];
        foreach ($tableDetail as $columnDetail) {
            if ($columnDetail->Key == "PRI") {
                continue;
            }
            $messageError[] = "The " . $columnDetail->Field . " field is required.";
        }

        $data = [
            "message" => implode(",", $messageError),
        ];
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getJsonSuccessStore()
    {
        $dataSample = $this->getDataSample(1, True, True);

        $data = [
            "messsage" => "successful",
            "data" => $dataSample
        ];
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getJsonSampleSuccessStore()
    {
        $data = $this->getDataSample(1, True, False);
        return "Post:\n" . json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getJsonSampleFailStore()
    {
        $data = $this->getDataSample(1, False, False);
        return "Post:\n" . json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getDataRequestIndex()
    {
        $data = [
            ["per_page", "int", "O", 10, NULL],
            ["page", "int", "O", 1, NULL],
            ["keyword", "string", "O", NULL, NULL],
            ["include", "string", "O", NULL, NULL]
        ];
        return $data;
    }

    public function getDataResponseIndex()
    {
        $tableDetail = $this->tableDetail;
        $tab = $this->tab;
        $data = [
            ["message", "string", "M", NULL, NULL],
            ["meta", "object", "O", NULL, NULL],
            ["$tab page", "int", "M", NULL, NULL],
            ["$tab per_page", "int", "M", NULL, NULL],
            ["$tab page_count", "int", "M", NULL, NULL],
            ["$tab total_count", "int", "M", NULL, NULL],
            ["$tab links", "object", "M", NULL, NULL],
            ["$tab$tab self", "string", "M", NULL, NULL],
            ["$tab$tab first", "string", "M", NULL, NULL],
            ["$tab$tab previous", "string", "M", NULL, NULL],
            ["$tab$tab next", "string", "M", NULL, NULL],
            ["$tab$tab last", "string", "M", NULL, NULL],
            ["data", "array", "O", NULL, NULL],
            ["$tab item", "object", "O", NULL, NULL],
        ];

        foreach ($tableDetail as $columnDetail) {
            $data[] = ["$tab$tab " . $columnDetail->Field, $columnDetail->Type, "M", $columnDetail->Default, NULL];
        }
        return $data;
    }


    public function dataFake($columnDetail, $index = "1")
    {
        $columnName = $columnDetail->Field;
        $dataType = $columnDetail->Type;
        if (str_contains($dataType, "int")) {
            return $index;
        } elseif (str_contains($dataType, "decimal")) {
            return number_format(rand(100, 1000));
        } elseif (str_contains($dataType, "datetime")) {
            return Carbon::now()->format("Y-m-d");
        } else {
            return "$columnName $index";
        }
    }

    public function getCellFromValue($columFind, $value)
    {
        $columMax = $this->spreadsheet->getActiveSheet()->getHighestRow();
        for ($i = 1; $i <= $columMax; $i++) {
            $cellValue = $this->spreadsheet->getActiveSheet()->getCell($columFind . $i)->getCalculatedValue();
            if ($cellValue == $value) {
                return $columFind . $i;
            }
        }
    }

    public function getDataRequestBodyStore()
    {
        $tableDetail = $this->tableDetail;
        $tab = $this->tab;
        $data = [
            ["item", "object", "M", NULL, NULL],
        ];

        foreach ($tableDetail as $columnDetail) {
            if ($columnDetail->Key == "PRI") {
                continue;
            }
            $data[] = ["$tab " . $columnDetail->Field, $columnDetail->Type, "M", $columnDetail->Default, NULL];
        }
        return $data;
    }

    public function getDataResponseStore()
    {
        $tableDetail = $this->tableDetail;
        $tab = $this->tab;
        $data = [
            ["message", "string", "M", NULL, NULL],
            ["data", "object", "O", NULL, NULL],
        ];

        foreach ($tableDetail as $columnDetail) {
            $data[] = ["$tab " . $columnDetail->Field, $columnDetail->Type, "M", $columnDetail->Default, NULL];
        }
        return $data;
    }

    public function getDataRequestShow()
    {
        $tableDetail = $this->tableDetail;
        $data = [];
        foreach ($tableDetail as $columnDetail) {
            $data[] = [$columnDetail->Field, $columnDetail->Type, "M", $columnDetail->Default, NULL];
            break;
        }

        return $data;
    }

    public function getJsonSuccessDestroy()
    {
        $data = [
            "message" => "successful"
        ];
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getJsonSampleSuccessUpdate()
    {
        $data = $this->getDataSample(1, True, False);
        return "PUT:\n" . json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getJsonSampleFailUpdate()
    {
        $data = $this->getDataSample(1, False, False);
        return "PUT:\n" . json_encode($data, JSON_PRETTY_PRINT);
    }

}