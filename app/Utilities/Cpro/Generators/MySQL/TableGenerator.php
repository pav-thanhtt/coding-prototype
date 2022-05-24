<?php

namespace App\Utilities\Cpro\Generators\MySQL;

use Illuminate\Support\Facades\DB;
use App\Utilities\Cpro\Generators\BaseTableGenerator;
use App\Utilities\Cpro\Tokenizers\MySQL\ColumnTokenizer;

/**
 * Class TableGenerator
 * @package LaravelMigrationGenerator\Generators\MySQL
 */
class TableGenerator extends BaseTableGenerator
{
    public function resolveStructure()
    {
        $structure = DB::select("SHOW FULL COLUMNS FROM {$this->definition()->getTableName()}");

        $this->rows = $structure;
    }

    public function parse()
    {
        foreach ($this->rows as $row) {
            $tokenizer = ColumnTokenizer::parse((array)$row);
            $this->definition()->addColumnDefinition($tokenizer->definition());
        }
    }
}
