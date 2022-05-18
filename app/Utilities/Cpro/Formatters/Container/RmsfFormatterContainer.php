<?php

namespace App\Utilities\Cpro\Formatters\Container;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\RmsfFormatter\FactoryFormatter;
use App\Utilities\Cpro\Formatters\RmsfFormatter\ModelFormatter;
use App\Utilities\Cpro\Formatters\RmsfFormatter\RepositoryFormatter;
use App\Utilities\Cpro\Formatters\RmsfFormatter\SeederFormatter;

class RmsfFormatterContainer extends BaseFormatterContainer
{
    public ModelFormatter $modelFormatter;
    public SeederFormatter $seederFormatter;
    public RepositoryFormatter $repositoryFormatter;
    public FactoryFormatter $factoryFormatter;

    public static function init(TableDefinition $tableDefinition): static
    {
        $formatter = (new static());
        $formatter->tableName = $tableDefinition->getTableName();

        $formatter->modelFormatter = new ModelFormatter($tableDefinition);
        $formatter->seederFormatter = new SeederFormatter($tableDefinition);
        $formatter->repositoryFormatter = new RepositoryFormatter($tableDefinition);
        $formatter->factoryFormatter = new FactoryFormatter($tableDefinition);

        return $formatter;
    }
}
