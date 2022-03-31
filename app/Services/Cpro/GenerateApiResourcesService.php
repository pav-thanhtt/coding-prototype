<?php

namespace App\Services\Cpro;

class GenerateApiResourcesService extends BaseGenerateApiResourcesService
{
    /**
     * @return string
     */
    public function exportResource(): string
    {
        if (empty($this->formatters)) {
            return '<fg=red>List table is empty</>';
        }
        $resourceFileMap = config('cpro-resource-generator.resource_file_map');
        foreach ($this->formatters as $formatter) {
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
