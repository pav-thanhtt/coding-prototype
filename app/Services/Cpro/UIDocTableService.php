<?php

namespace App\Services\Cpro;

use Exception;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;
use App\Services\Cpro\ExportDocTableListService;

class UIDocTableService
{
    private $connection;

    public function __construct(string $connection)
    {
        $this->connection = $connection;
    }

    public function exportAllTable(): array
    {
        $tables = DB::connection($this->connection)->getDoctrineSchemaManager()->listTableNames();
        return $this->exportListTable($tables);
    }

    public function exportListTable(array $listTable): array
    {
        $res = [];

        foreach ($listTable as $key => $table) {
            $this->exportTable($table);
            $res[$table] = $this->exportTable($table);
        }

        return $res;
    }

    public function exportTable(string $table): bool
    {
        try {
            // exprot list doc
            $listExportService = new ExportDocTableListService($this->connection, $table);
            $listExportService->export($table);

            // exprot detail doc
            $listExportService = new ExportDocTableDetailService($this->connection, $table);
            $listExportService->export($table);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
