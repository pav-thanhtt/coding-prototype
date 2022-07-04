<?php

namespace App\Services\Cpro\Generator;

use App\Utilities\Cpro\Formatters\Container\FeCrudFormatterContainer;
use Illuminate\Support\Str;

class GenerateFeCrudResourcesService extends BaseGenerateFeResourcesService
{
    /**
     * @return string
     */
    public function exportResource(): string
    {
        if (empty($this->tableDefinitions)) {
            return '<fg=red>Table list is empty</>';
        }
        $resourceFileMap = config('cpro-resource-generator.fe_resource_file_map');
        foreach ($this->tableDefinitions as $tableDefinition) {
            $formatter = FeCrudFormatterContainer::init($tableDefinition);
            $this->command->info("Start export Frontend CRUD resource of table <fg=yellow>{$formatter->tableName}</>");
            foreach ($resourceFileMap as $type => $file) {
                $this->exportResourceFile($formatter->{Str::camel($file) . "Formatter"}, $file, $type);
            }
        }
        return $this->message;
    }
}
