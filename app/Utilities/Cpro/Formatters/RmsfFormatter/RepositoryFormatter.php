<?php

namespace App\Utilities\Cpro\Formatters\RmsfFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\BaseBeFormatter;

class RepositoryFormatter extends BaseBeFormatter
{
    private const STUB_FILE_NAME = 'repository';
    private const EXPORT_FILE_NAME_SUFFIX = 'Repository.php';

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
