<?php


namespace App\Services\Cpro;

use App\Services\Cpro\DBTableDetailService;
use App\Services\Cpro\ExportDBDocTableService;
class DBDocTableService
{
    public function exportTable(string $connection, array $tables): bool
    {
        foreach ($tables as $table) {
            $exportService = new ExportDBDocTableService();
            $tableDetailService = new DBTableDetailService($connection, $table);
            $exportService->export($tableDetailService->data);
        }
        return true;
    }
}
