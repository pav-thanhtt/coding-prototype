<?php

namespace App\Services\Cpro\Generator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseGenerateResourcesService
{
    protected array $tableDefinitions;

    protected Command $command;

    protected array $stubs = [];

    protected string $tableName;

    protected array $rules;

    protected string $message = 'Finished exporting';

    public function __construct(array $tableDefinitions, Command $command)
    {
        $this->tableDefinitions = $tableDefinitions;
        $this->command = $command;
    }

    /**
     * @return string
     */
    abstract public function exportResource(): string;

    abstract protected function exportResourceFile($formatter, $file, $type = '');

    protected function exportResourceFileFinished($formatter, $pathOutputConfig, $type)
    {
        $path = $this->createMissingDirectory($pathOutputConfig);
        $stub = $formatter->render($type);
        $fileName = $formatter->getExportFileName($type);

        $export = $this->export($path, $stub, $fileName);
        $this->command->info("    Create file <fg=yellow>$fileName</> in <fg=cyan>$export</> success");
    }

    public function createMissingDirectory($basePath)
    {
        if (!File::isDirectory($basePath)) {
            File::makeDirectory($basePath, 0777, true);
        }
        return $basePath;
    }

    public function export(string $basePath, string $stub, string $fileName): string
    {
        File::put($final = $basePath . '/' . $fileName, $stub);

        return $final;
    }
}
