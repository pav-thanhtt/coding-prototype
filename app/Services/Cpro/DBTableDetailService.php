<?php


namespace App\Services\Cpro;
use Illuminate\Support\Facades\DB;

class DBTableDetailService
{
    public array $data;

    public function __construct(string $connection, string $table)
    {
        $this->getData($connection, $table);
    }

    private function getData(string $connection, string $tableName)
    {
        $con = DB::connection($connection);
        $columns = $con->select(DB::raw("SHOW FULL COLUMNS FROM $tableName"));
        $indexes = $con->select(DB::raw("SHOW INDEXES FROM $tableName"));
        $script = $con->select(DB::raw("SHOW CREATE TABLE $tableName"))[0]->{'Create Table'};
        $data = [
            'table' => $tableName,
            'fields' => $this->customFieldData($con, $tableName, $columns),
            'indexes' => $this->customIndexesData($indexes),
            'script' => $script
        ];
        $this->data = $data;
    }

    private function customFieldData(mixed $con, string $tableName, array $columns): array
    {
        $data = array();
        foreach ($columns as $column) {
            $columnName = $column->{'Field'};
            $sm = $con->getDoctrineColumn($tableName, $columnName);
            $hasPlatform = count($sm->getPlatformOptions()) > 0;
            $row = [
                $columnName,
                strtoupper($column->{'Type'}),
                $column->{'Default'},
                $sm->getUnsigned(),
                $column->{'Null'} == "YES",
                $hasPlatform ? $sm->getPlatformOption('charset') : '',
                $column->{'Collation'},
                $column->{'Comment'},
            ];
            array_push($data, $row);

        }
        return $data;
    }

    private function customIndexesData(array $indexes): array
    {
        $data = array();
        foreach ($indexes as $i) {
            array_push($data, [
                $i->{'Key_name'},
                $i->{'Index_type'},
                !$i->{'Non_unique'},
                $i->{'Column_name'}
            ]);
        }
        return $data;
    }
}
