<?php

namespace App\Console\Commands\Cpro;

use Illuminate\Console\Command;
use App\Services\Cpro\UIDocTableService;

class UIDocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'allu-cpro:ui-doc {con=mysql} {--table=all}';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = $this->argument('con');
        $table = $this->option('table');

        $service = new UIDocTableService($connection);

        switch ($table) {
            case 'all':
                $response = $service->exportAllTable();
                break;
            case (strpos($table, ',') !== false):
                $listTable = explode(',', $table);
                $response = $service->exportListTable($listTable);
                break;
            default:
                $response = $service->exportTable($table);
                break;
        }

        $this->showResponse($response, $table);
    }

    private function showResponse(bool|array $response, string $table): void
    {
        if (is_bool($response)) {
            $this->showResponseWithStatus($response, $table);
            return;
        }

        if (is_array($response)) {
            foreach ($response as $key => $value) {
                $this->showResponseWithStatus($value, $key);
            }
            return;
        }
    }

    private function showResponseWithStatus(bool $response, string $table): void
    {
        if ($response) {
            $this->info("Export table $table success !.");
        } else {
            $this->error("Export table $table false !.");
        }
    }
}
