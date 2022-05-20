<?php

namespace App\Services\Cpro\Generator;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

abstract class BaseGenerateFeResourcesService extends BaseGenerateResourcesService
{
    public function __construct(array $tableDefinitions, Command $command)
    {
        parent::__construct($tableDefinitions, $command);
    }

    protected function exportResourceFile($formatter, $file, $type = '')
    {
        $pathOutputConfig = config("cpro-resource-generator.fe_{$file}_output_path");

        if (Str::contains($type, 'view')) {
            $pathOutputConfig .= '/' . $formatter->getExportDirName($type);
        }

        $this->exportResourceFileFinished($formatter, $pathOutputConfig, $type);
    }
}
