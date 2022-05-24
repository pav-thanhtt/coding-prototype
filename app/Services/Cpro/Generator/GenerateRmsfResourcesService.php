<?php

namespace App\Services\Cpro\Generator;

use App\Utilities\Cpro\Formatters\Container\RmsfFormatterContainer;

class GenerateRmsfResourcesService extends BaseGenerateBeResourcesService
{
    /**
     * @return string
     */
    public function exportResource(): string
    {
        if (empty($this->tableDefinitions)) {
            return '<fg=red>Table list is empty</>';
        }
        $resourceFileMap = config('cpro-resource-generator.rmsf_resource_file_map');
        foreach ($this->tableDefinitions as $tableDefinition) {
            $formatter = RmsfFormatterContainer::init($tableDefinition);
            $this->command->info("Start export RMSF resource of table <fg=yellow>{$formatter->tableName}</>");
            foreach ($resourceFileMap as $type => $file) {
                $this->exportResourceFile($formatter->{$file . "Formatter"}, $file, $type);
            }
        }
        return $this->message;
    }
}
