<?php

namespace App\Utilities\Cpro\GeneratorManagers;

use App\Console\Commands\GenerateApiResourcesCommand;
use App\Utilities\Cpro\Formatters\Formatter;
use App\Utilities\Cpro\Generators\MySQL\TableGenerator;
use Illuminate\Support\Facades\DB;

class MySQLGeneratorManager extends BaseGeneratorManager
{
    /**
     *
     */
    public function __construct(GenerateApiResourcesCommand $command)
    {
        $this->command = $command;
    }

    public function init($tables)
    {
        $allTables = DB::select('SHOW FULL TABLES');
        $allTables = array_map(function ($table) use ($tables) {
            $tableData = (array)$table;
            $table = $tableData[array_key_first($tableData)];
            if (!in_array($table, explode(',', config('cpro-resource-generator.table_excepts')))) {
                return $table;
            }
        }, $allTables);

        $allTables = array_values(array_filter($allTables, function ($table) {
            return !is_null($table);
        }));

        if (empty($tables)) {
            $tables = $allTables;
        }

        foreach ($tables as $table) {
            if (!in_array($table, $allTables)) {
                $this->command->warn("Table $table not exists!");
                continue;
            }
            $this->addTableDefinition(TableGenerator::init($table)->definition());
        }
    }
}
