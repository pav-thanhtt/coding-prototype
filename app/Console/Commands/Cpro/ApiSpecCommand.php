<?php

namespace App\Console\Commands\Cpro;

use App\Common\ExcelStyleCommon;
use App\Common\ExcelTableCommon;
use App\Common\PathCommon;
use App\Services\Cpro\ExportExcel\ExportApiDoc;
use App\Services\Cpro\DatabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApiSpecCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'allu-cpro:api-doc {con} {--table=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export API doc from database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $con = explode("=", $this->argument('con')[1]);
        $listTableName = explode(",", $this->option('table'));

        if (!File::isDirectory(PathCommon::EXCEL_API_SPEC)) {

            File::makeDirectory(PathCommon::EXCEL_API_SPEC, 0777, true, true);

        }

        if ($listTableName[0] == "all"){
            $listTableName = DatabaseService::getAllTableName();
        }


        foreach ($listTableName as $tableName) {
            $filePath = PathCommon::EXCEL_API_SPEC . '/' . $tableName . ".xlsx";

            $apiExcelDoc = new ExportApiDoc('index', $tableName);
            // sheet index
            $apiExcelDoc
                ->setWidthColumn("C", 40)
                ->setWidthColumn("B", 20)
                ->setWidthColumn("D", 40)
                ->setWidthColumn("G", 20);

            $responseSuccess = $apiExcelDoc->getJsonSuccessIndex($tableName);
            $responseFail = $apiExcelDoc->getJsonFailIndex();
            $requestData = $apiExcelDoc->getDataRequestIndex();
            $responseData = $apiExcelDoc->getDataResponseIndex();


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Base'], "B2")
                ->setCellValue("C2", "api/v1/$tableName")
                ->setCellValue("C3", "GET");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Request'], "B6");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response'], "B9");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response_code'], "B12");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Sample'], "B22")
                ->setCellValue("C23", "api/v1/$tableName?per_page=2&page=2&include=meta")
                ->setCellValue("C25", $responseSuccess)->setCellValue("D25", $responseFail);


            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['title'], ["B2:B4", "B6", "C7:G7", "B9", "C10:G10", "B12", "C13:G13", "C22:D22", "B23:B25"]);
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default'], ["C2:C4", "C6", "C9", "C12", "C14:G20", "C23:D25"]);
            $apiExcelDoc->mergeCell([
                "D13:G13",
                "D14:G14",
                "D15:G15",
                "D16:G16",
                "D17:G17",
                "D18:G18",
                "D19:G19",
                "D20:G20",
            ]);


            $apiExcelDoc->insertRowData($requestData, 'C8')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);
            $apiExcelDoc->insertRowData($responseData, 'C11')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);


            $cellStyle = $apiExcelDoc->getCellFromValue('C', '    item');
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['text_red'], [$cellStyle]);


            // sheet store
            $apiExcelDoc->createSheet('store');
            $apiExcelDoc
                ->setWidthColumn("C", 40)
                ->setWidthColumn("B", 20)
                ->setWidthColumn("D", 40)
                ->setWidthColumn("G", 20);

            $responseSuccess = $apiExcelDoc->getJsonSuccessStore();
            $responseFail = $apiExcelDoc->getJsonFailStore();
            $dataSampleSuccess = $apiExcelDoc->getJsonSampleSuccessStore();
            $dataSampleFail = $apiExcelDoc->getJsonSampleFailStore();
            $requestData = $apiExcelDoc->getDataRequestBodyStore();
            $responseData = $apiExcelDoc->getDataResponseStore();


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Base'], "B2")
                ->setCellValue("C2", "api/v1/$tableName")
                ->setCellValue("C3", "POST");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Request_body'], "B6");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response'], "B9");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response_code'], "B12");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Sample'], "B22")
                ->setCellValue("C23", "api/v1/$tableName")
                ->setCellValue("C25", $responseSuccess)
                ->setCellValue("D25", $responseFail)
                ->setCellValue("C24", $dataSampleSuccess)
                ->setCellValue("D24", $dataSampleFail);


            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['title'], ["B2:B4", "B6", "C7:G7", "B9", "C10:G10", "B12", "C13:G13", "C22:D22", "B23:B25"]);
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default'], ["C2:C4", "C6", "C9", "C12", "C14:G20", "C23:D25"]);
            $apiExcelDoc->mergeCell([
                "D13:G13",
                "D14:G14",
                "D15:G15",
                "D16:G16",
                "D17:G17",
                "D18:G18",
                "D19:G19",
                "D20:G20",
            ]);


            $apiExcelDoc->insertRowData($requestData, 'C8')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);
            $apiExcelDoc->insertRowData($responseData, 'C11')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);


            $cellStyle = $apiExcelDoc->getCellFromValue('C', 'item');
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['text_red'], [$cellStyle]);


            // sheet show
            $apiExcelDoc->createSheet('show');
            $apiExcelDoc
                ->setWidthColumn("C", 40)
                ->setWidthColumn("B", 20)
                ->setWidthColumn("D", 40)
                ->setWidthColumn("G", 20);

            $responseSuccess = $apiExcelDoc->getJsonSuccessStore();
            $responseFail = $apiExcelDoc->getJsonFailIndex();
            $requestData = $apiExcelDoc->getDataRequestShow();
            $responseData = $apiExcelDoc->getDataResponseStore();


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Base'], "B2")
                ->setCellValue("C2", "api/v1/$tableName/{id}")
                ->setCellValue("C3", "GET");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Request'], "B6");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response'], "B9");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response_code'], "B12");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Sample'], "B22")
                ->setCellValue("C23", "api/v1/$tableName/1")
                ->setCellValue("C25", $responseSuccess)
                ->setCellValue("D25", $responseFail);


            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['title'], ["B2:B4", "B6", "C7:G7", "B9", "C10:G10", "B12", "C13:G13", "C22:D22", "B23:B25"]);
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default'], ["C2:C4", "C6", "C9", "C12", "C14:G20", "C23:D25"]);
            $apiExcelDoc->mergeCell([
                "D13:G13",
                "D14:G14",
                "D15:G15",
                "D16:G16",
                "D17:G17",
                "D18:G18",
                "D19:G19",
                "D20:G20",
            ]);


            $apiExcelDoc->insertRowData($requestData, 'C8')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);
            $apiExcelDoc->insertRowData($responseData, 'C11')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);


            // sheet update
            $apiExcelDoc->createSheet('update');
            $apiExcelDoc
                ->setWidthColumn("C", 40)
                ->setWidthColumn("B", 20)
                ->setWidthColumn("D", 40)
                ->setWidthColumn("G", 20);


            $getJsonDataSuccess = $apiExcelDoc->getJsonSampleSuccessUpdate();
            $getJsonDataFail = $apiExcelDoc->getJsonSampleFailUpdate();
            $getJsonResponseSuccess = $apiExcelDoc->getJsonSuccessStore();
            $getJsonResponseFail = $apiExcelDoc->getJsonFailStore();
            $requestData = $apiExcelDoc->getDataRequestShow();
            $requestBodyData = $apiExcelDoc->getDataRequestBodyStore();
            $responseData = $apiExcelDoc->getDataResponseStore();

            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Base'], "B2")
                ->setCellValue("C2", "api/v1/$tableName/{id}")
                ->setCellValue("C3", "PUT");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Request'], "B6");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Request_body'], "B9");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response'], "B12");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response_code'], "B15");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Sample'], "B25")
                ->setCellValue("C26", "api/v1/$tableName/1")
                ->setCellValue("C27", $getJsonDataSuccess)
                ->setCellValue("D27", $getJsonDataFail)
                ->setCellValue("C28", $getJsonResponseSuccess)
                ->setCellValue("D28", $getJsonResponseFail);


            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['title'], ["B2:B4", "B6", "C7:G7", "B9", "C10:G10", "B12", "C13:G13", "B15", "C16:G16", "B26:B28", "C25:D25"]);
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default'], ["C2:C4", "C6", "C9", "C12", "C15", "C17:G23", "C26:D28"]);
            $apiExcelDoc->mergeCell([
                "D16:G16",
                "D17:G17",
                "D18:G18",
                "D19:G19",
                "D20:G20",
                "D21:G21",
                "D22:G22",
                "D23:G23",
            ]);

            $apiExcelDoc->insertRowData($requestData, 'C8')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);
            $apiExcelDoc->insertRowData($requestBodyData, 'C11')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);
            $apiExcelDoc->insertRowData($responseData, 'C14')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);

            $cellStyle = $apiExcelDoc->getCellFromValue('C', 'item');
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['text_red'], [$cellStyle]);

            // sheet destroy
            $apiExcelDoc->createSheet('destroy');
            $apiExcelDoc
                ->setWidthColumn("C", 40)
                ->setWidthColumn("B", 20)
                ->setWidthColumn("D", 40)
                ->setWidthColumn("G", 20);


            $responseSuccess = $apiExcelDoc->getJsonSuccessDestroy();
            $responseFail = $apiExcelDoc->getJsonFailIndex();
            $requestData = $apiExcelDoc->getDataRequestShow();


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Base'], "B2")
                ->setCellValue("C2", "api/v1/$tableName/{id}")
                ->setCellValue("C3", "DELETE");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Request'], "B6");
            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Response_code'], "B9");


            $apiExcelDoc->addTableStatic(ExcelTableCommon::EXCEL_API_SPEC['Sample'], "B19")
                ->setCellValue("C20", "api/v1/$tableName/1")
                ->setCellValue("C22", $responseSuccess)
                ->setCellValue("D22", $responseFail);

            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['title'], ["B2:B4", "B6", "C7:G7", "B9", "C10:G10", "C19:D19", "B20:B22"]);
            $apiExcelDoc->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default'], ["C2:C4", "C6", "C9", "C11", "C11:G17", "C20:D22"]);
            $apiExcelDoc->mergeCell([
                "D10:G10",
                "D11:G11",
                "D12:G12",
                "D13:G13",
                "D14:G14",
                "D15:G15",
                "D16:G16",
                "D17:G17",
            ]);

            $apiExcelDoc->insertRowData($requestData, 'C8')->setStyle(ExcelStyleCommon::STYLE_API_SPEC['default']);


            $apiExcelDoc->export($filePath);
        }


    }
}
