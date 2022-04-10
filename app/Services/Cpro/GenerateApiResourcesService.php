<?php

namespace App\Services\Cpro;

use App\Utilities\Cpro\Formatters\Formatter;

class GenerateApiResourcesService extends BaseGenerateApiResourcesService
{
    /**
     * @return string
     */
    public function exportResource(): string
    {
        if (empty($this->tableDefinitions)) {
            return '<fg=red>Table list is empty</>';
        }
        $resourceFileMap = config('cpro-resource-generator.resource_file_map');
        foreach ($this->tableDefinitions as $tableDefinition) {
            $formatter = Formatter::init($tableDefinition);
            $this->command->info("Start export resource of table <fg=yellow>{$formatter->tableName}</>");
            foreach ($resourceFileMap as $type => $file) {
                if ($this->isExit) {
                    return $this->message;
                }
                $this->exportResourceFile($formatter->{$file . "Formatter"}, $file, $type);
            }
        }
        return $this->message;
    }
}
