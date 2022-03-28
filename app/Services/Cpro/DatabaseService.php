<?php

namespace App\Services\Cpro;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseService
{
    /**
     * get all table name
     *
     * @return array
     */

    public $tableDefault = [];
    public static function getAllTableName()
    {
        $objectTable = DB::select('SHOW TABLES');
        $listTable = [];
        foreach ($objectTable as $table){
            $listTable[] = $table -> Tables_in_allu;
        }
        return $listTable;
    }

    /**
     * get all table name
     * @param $table array
     * @return array
     */
    public static function getTableDetail($table){
        return DB::select("describe $table");
    }


    public static function getAllColumnName($table){
        $tableDetail = self::getTableDetail($table);
        $columnList = [];
        foreach ($tableDetail as $row){
            $columnList[] = $row -> Field;
        }
        return $columnList;

    }

    public static function getTableFormatType($table){
        $data = self::getTableDetail($table);
        $dataFormat = [];
        foreach ($data  as $row){
            $typeNew = Schema::getColumnType($table, $row -> Field);
            if ($typeNew == "integer") {
                $typeNew = 'int';
            }
            $row -> Type = $typeNew;
            $dataFormat[] = $row;
        }
      return $dataFormat;
    }





}