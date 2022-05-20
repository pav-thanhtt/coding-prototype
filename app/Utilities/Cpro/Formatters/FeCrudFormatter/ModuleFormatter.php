<?php

namespace App\Utilities\Cpro\Formatters\FeCrudFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;

class ModuleFormatter extends BaseFeFormatter
{
    private const STUB_FILE_NAME = 'module';
    private const EXPORT_FILE_NAME_SUFFIX = '.ts';

    public function __construct(TableDefinition $tableDefinition)
    {
        parent::__construct($tableDefinition);
        $this->fileName[self::STUB_FILE_NAME] = $this->tableName('ClassNameSingular') . self::EXPORT_FILE_NAME_SUFFIX;
    }

    protected function getStubFileName(): string
    {
        return self::STUB_FILE_NAME;
    }


    public function getExportFileName(?string $options = ''): string
    {
        return $this->fileName[self::STUB_FILE_NAME];
    }
}
