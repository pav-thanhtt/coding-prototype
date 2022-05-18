<?php

namespace App\Console\Commands\Generator;

use App\Services\Cpro\Generator\GenerateRmsfResourcesService;
use App\Traits\CommandTrait;
use Illuminate\Console\Command;

class GenerateRMSFResourceCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'allu-cpro:rmsf-resource {con} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Repository, Model, Seeder, Factory resource command';

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
        try {
            $connection = $this->getConnection();

            \DB::setDefaultConnection($connection);

            $driver = config('database.connections.' . $connection)['driver'];

            $tables = $this->getTables();

            $manager = $this->resolveGeneratorManager($driver, $tables);

            $generateService = new GenerateRmsfResourcesService($manager->getTableDefinitions(), $this);

            $this->info($generateService->exportResource());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }
}
