<?php

namespace App\Console\Commands\Generator;

use App\Services\Cpro\GenerateApiResourcesService;
use App\Traits\CommandTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class GenerateResourcesCommand extends Command
{
    use CommandTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'allu-cpro:resource {con} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RESTFUL API resource command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $connection = $this->getConnection();

            Artisan::call('allu-cpro:rmsf-resource', [
                'con' => $connection, '--table' => $this->option('table')
            ]);
            Artisan::call('allu-cpro:be-crud-resource', [
                'con' => $connection, '--table' => $this->option('table')
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
