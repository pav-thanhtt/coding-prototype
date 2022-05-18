<?php

namespace App\Utilities\Cpro\Formatters\RmsfFormatter;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\BaseFormatter;
use Illuminate\Support\Facades\File;

abstract class BaseRmsfFormatter extends BaseFormatter
{
    public function __construct(TableDefinition $tableDefinition)
    {
        parent::__construct($tableDefinition);
    }
    /**
     * @param string $resourceType
     * @return false|string
     */

    public function getStubPath(string $resourceType): bool|string
    {
        if (File::exists($overridden = resource_path(config('cpro-resource-generator.be_stub_path') . $resourceType . '.stub'))) {
            return $overridden;
        }
        return false;
    }
}
