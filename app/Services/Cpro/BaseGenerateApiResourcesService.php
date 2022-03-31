<?php

namespace App\Services\Cpro;

use App\Console\Commands\GenerateApiResourcesCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseGenerateApiResourcesService
{
    protected array $formatters;

    protected GenerateApiResourcesCommand $command;

    protected array $stubs = [];

    protected string $tableName;

    protected array $rules;

    protected bool $isOverride = true;
    protected bool $isExit = false;
    protected bool $isUnquestioning = false;

    protected string $message = 'Finished exporting';

    protected array $choice = [
        1 => 'Override',
        2 => 'Skip',
        3 => 'Override All',
        4 => 'Skip All',
        5 => 'Exit'
    ];

    public function __construct(array $formatters, GenerateApiResourcesCommand $command)
    {
        $this->formatters = $formatters;
        $this->command = $command;
    }

    /**
     * @return string
     */
    abstract public function exportResource(): string;

    protected function exportResourceFile($formatter, $file, $type = '')
    {
        $pathOutputConfig = config("cpro-resource-generator.{$file}_output_path");

        if (Str::contains($type, 'request')) {
            $pathOutputConfig .= '/' . $formatter->getExportDirName($type);
        }

        $path = $this->createMissingDirectory($pathOutputConfig);
        $stub = $formatter->render($type);
        $fileName = $formatter->getExportFileName($type);
        if (!$this->isContinueExport("$path/$fileName")) {
            return;
        }

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

    private function isContinueExport(string $filePath): bool
    {
        if (File::exists($filePath)) {
            if (!$this->isOverride) {
                return false;
            }

            if (!$this->isUnquestioning) {
                $choice = $this->command->choice("File <fg=yellow>$filePath</> already exists", $this->choice);
                switch ($choice) {
                    case 'Skip':
                        return false;
                    case 'Override All':
                        $this->isUnquestioning = true;
                        return true;
                    case 'Skip All':
                        $this->isOverride = false;
                        $this->isUnquestioning = true;
                        return false;
                    case 'Exit':
                        $this->message = '<fg=yellow>Exit!</>';
                        $this->isExit = true;
                        return false;
                }
            }
        }

        return true;
    }
}
