<?php

namespace App\Console\Commands;

use App\Utilities\Cpro\GeneratorManagers\Interfaces\GeneratorManagerInterface;
use App\Utilities\Cpro\GeneratorManagers\MySQLGeneratorManager;
use App\Services\Cpro\GenerateApiResourcesService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class GenerateApiResourcesCommand extends Command
{
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
     * @return string
     * @throws Exception
     */
    private function getConnection(): string
    {
        $connection = $this->argument('con');

        if (!Config::has('database.connections.' . $connection)) {
            throw new Exception('Could not find connection `' . $connection . '` in your config.');
        }

        return $connection;
    }

    /**
     * @return array
     */
    private function getTables(): array
    {
        $tables = $this->option('table');

        return isset($tables) ? explode(',', $tables) : [];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $connection = $this->getConnection();
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info('Using connection ' . $connection);
        DB::setDefaultConnection($connection);

        $driver = Config::get('database.connections.' . $connection)['driver'];

        $tables = $this->getTables();

        $manager = $this->resolveGeneratorManager($driver);

        $manager->handle($tables);

        $generateService = new GenerateApiResourcesService($manager->getFormatters(), $this);

        $this->info($generateService->exportResource());

        return 0;
    }

    /**
     * @param string $driver
     * @return false|GeneratorManagerInterface
     */
    protected function resolveGeneratorManager(string $driver): GeneratorManagerInterface|bool
    {
        $supported = [
            'mysql' => MySQLGeneratorManager::class
        ];

        if (!isset($supported[$driver])) {
            return false;
        }

        return new $supported[$driver]($this);
    }
}
