<?php

namespace App\Utilities\Cpro\Formatters\Container;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\ControllerFormatter;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\FilterFormatter;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\RequestFormatter;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\ResourceFormatter;
use App\Utilities\Cpro\Formatters\BeCrudFormatter\ServiceFormatter;

class BeCrudFormatterContainer extends BaseFormatterContainer
{
    public ControllerFormatter $controllerFormatter;
    public FilterFormatter $filterFormatter;
    public RequestFormatter $requestFormatter;
    public ResourceFormatter $resourceFormatter;
    public ServiceFormatter $serviceFormatter;

    public static function init(TableDefinition $tableDefinition): static
    {
        $formatter = (new static());
        $formatter->tableName = $tableDefinition->getTableName();
        $formatter->controllerFormatter = new ControllerFormatter($tableDefinition);
        $formatter->filterFormatter = new FilterFormatter($tableDefinition);
        $formatter->requestFormatter = new RequestFormatter($tableDefinition);
        $formatter->resourceFormatter = new ResourceFormatter($tableDefinition);
        $formatter->serviceFormatter = new ServiceFormatter($tableDefinition);

        return $formatter;
    }
}
