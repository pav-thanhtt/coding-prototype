<?php

namespace App\Utilities\Cpro\Formatters\Container;

use App\Utilities\Cpro\Definitions\TableDefinition;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\ApiFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\ModuleFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\TypeBodyFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\TypeEntityFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\TypeFilterBodyFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\TypeStoreFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\ViewFormFormatter;
use App\Utilities\Cpro\Formatters\FeCrudFormatter\ViewListFormatter;

class FeCrudFormatterContainer extends BaseFormatterContainer
{

    public ApiFormatter $apiFormatter;
    public ModuleFormatter $moduleFormatter;
    public TypeStoreFormatter $typeStoreFormatter;
    public TypeBodyFormatter $typeBodyFormatter;
    public TypeFilterBodyFormatter $typeFilterBodyFormatter;
    public TypeEntityFormatter $typeEntityFormatter;
    public ViewFormFormatter $viewFormFormatter;
    public ViewListFormatter $viewListFormatter;

    public static function init(TableDefinition $tableDefinition): static
    {
        $formatter = (new static());
        $formatter->tableName = $tableDefinition->getTableName();

        $formatter->apiFormatter = new ApiFormatter($tableDefinition);
        $formatter->moduleFormatter = new ModuleFormatter($tableDefinition);
        $formatter->typeStoreFormatter = new TypeStoreFormatter($tableDefinition);
        $formatter->typeBodyFormatter = new TypeBodyFormatter($tableDefinition);
        $formatter->typeFilterBodyFormatter = new TypeFilterBodyFormatter($tableDefinition);
        $formatter->typeEntityFormatter = new TypeEntityFormatter($tableDefinition);
        $formatter->viewFormFormatter = new ViewFormFormatter($tableDefinition);
        $formatter->viewListFormatter = new ViewListFormatter($tableDefinition);

        return $formatter;
    }
}
