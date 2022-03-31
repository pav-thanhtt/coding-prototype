<?php

namespace App\Utilities\Cpro\Formatters;

use App\Utilities\Cpro\Definitions\TableDefinition;

class Formatter
{

    public string $tableName;
    public ModelFormatter $modelFormatter;
    public ControllerFormatter $controllerFormatter;
    public RepositoryFormatter $repositoryFormatter;
    public ServiceFormatter $serviceFormatter;
    public RequestFormatter $requestFormatter;
    public FactoryFormatter $factoryFormatter;
    public ResourceFormatter $resourceFormatter;
    public SeederFormatter $seederFormatter;


    public static function init(TableDefinition $tableDefinition): static
    {
        $formatter = (new static());
        $formatter->tableName = $tableDefinition->getTableName();
        $formatter->factoryFormatter = new FactoryFormatter($tableDefinition);
        $formatter->seederFormatter = new SeederFormatter($tableDefinition);
        $formatter->modelFormatter = new ModelFormatter($tableDefinition);
        $formatter->repositoryFormatter = new RepositoryFormatter($tableDefinition);
        $formatter->serviceFormatter = new ServiceFormatter($tableDefinition);
        $formatter->controllerFormatter = new ControllerFormatter($tableDefinition);
        $formatter->requestFormatter = new RequestFormatter($tableDefinition);
        $formatter->resourceFormatter = new ResourceFormatter($tableDefinition);

        return $formatter;
    }
}
