<?php

namespace App\Utilities\Cpro\Formatters\RmsfFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;

class SeederFormatter extends BaseRmsfFormatter
{
    private const STUB_FILE_NAME = 'seeder';
    private const EXPORT_FILE_NAME_SUFFIX = 'Seeder.php';

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

    protected function factoryLimit()
    {
        return config('cpro-resource-generator.factory_limit_default');
    }
}
