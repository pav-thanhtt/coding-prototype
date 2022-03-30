<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Cpro\DBDocTableService;

class DBDocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'allu-cpro:db-doc {con} {--table=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export doc excel from database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $con = $this->argument('con');
        $optionTable = $this->option('table');
        // Check connection
        try {
            $connection = DB::connection($con);
        } catch (\Exception $e) {
            $this->info("Could not connect to the database.  Please check your configuration. Error: {$e}");
        }
        // Check table in database
        $dbTables = $connection->getDoctrineSchemaManager()->listTableNames();
        if (!$optionTable || $optionTable == "all") {
            $tables = $dbTables;
        } else {
            $tables = explode(',', $optionTable);
            $diffTables = array_diff($tables, $dbTables);
            if (count($diffTables)) {
                $notExistsTable = implode(',', $diffTables);
                $this->info("Table '{$notExistsTable}' not exists in the database");
                return;
            }
        }

        // Export
        $service = new DBDocTableService();
        $response = $service->exportTable($con, $tables);
        if ($response) {
            $this->info('Successfully exported');
        } else {
            $this->info('Failed');
        }

    }
}
