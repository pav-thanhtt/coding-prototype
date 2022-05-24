<?php

namespace App\Utilities\Cpro\Formatters\BeCrudFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;

class ServiceFormatter extends BaseBeFormatter
{
    private const STUB_FILE_NAME = 'service';
    private const EXPORT_FILE_NAME_SUFFIX = 'Service.php';

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
